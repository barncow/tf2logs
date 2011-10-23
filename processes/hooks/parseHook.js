/*
  This will take lines from Redis
*/

//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';

var conf = require('../../conf/conf.js')()
  , redislib = require('redis')
  , util = require('util')
  , EventEmitter = require('events').EventEmitter
  , Hook = require('hook.io').Hook
  , realtime = require('../../lib/realtime')
  , loadModules = require('../../lib/loadmodules');


var log = (function() {
  var logCount = 0;
  return function() {
    ++logCount;
    var str = logCount+' '+new Date().toLocaleString()+': '+Array.prototype.slice.call(arguments).join(' ');
    process.stdout.write(str+'\n');
  }
})();

var ParseHook = function(options) {
  if(!(this instanceof ParseHook)) return new ParseHook(options);
  log('Initializing ParseHook');

  this.options = options;
  if(typeof this.options.name === 'undefined') this.options.name = 'ParseHook';
  if(typeof this.options.waitMsecs === 'undefined') this.options.waitMsecs = 500;
  if(typeof this.options.delaySecs === 'undefined' || this.options.delaySecs < 0) throw 'No delaySecs specified.';

  this.DO_IMMEDIATE = 0;
  this.DO_GET_SERVERS = 1;
  this.DO_SERVERS = 2;
  this.DO_WAIT = 3;

  this._redis = redislib.createClient();
  this._mongoose = require('mongoose');
  this._mongoose.connect(conf.dataDbUrl);
  loadModules('./schemas', /Schema(s?)\.js$/, this._mongoose, conf); //pull in models
  
  this.cacheImmediateTryLock();
  this.cacheServerTryLock();
  
  this._reset();

  //call super ctor
  Hook.call(this, this.options);

  this.on('hook::ready', function() {
    this.scheduleNextWork();
  });
}
module.exports = ParseHook;
util.inherits(ParseHook, Hook);

/**
  sees where we are now, and where we need to go. This way, the handlers can be context-free.
*/
ParseHook.prototype.scheduleNextWork = function(){
  var self = this;

  if(self._state === self.DO_IMMEDIATE) { 
    log('Scheduling immediate check');

    self._iterationStart = Date.now(); //we are beginning an iteration, let's mark the time, so we only iterate once a second.
    
    //do immediate lines
    self._immediate.tryLock(realtime.immediateMessagesKey);
    
    self._state = self.DO_GET_SERVERS;
  } else if(self._state === self.DO_GET_SERVERS) {
    log('Getting server list');

    self._state = self.DO_SERVERS;
    self.getServers();
  } else if(self._state === self.DO_SERVERS) {
    var server = self._serverList[self._serverIndex];
    log('About to do server', server);
    ++self._serverIndex
    self._server.tryLock(server);
    
    if(self._serverIndex >= self._serverList.length) {
      log('changing from do server to do wait');
      //we have processed all servers, we will wait until we can run again.
      self._state = self.DO_WAIT;
    }
  } else if(self._state === self.DO_WAIT) {
    //we are done. Let's get everything ready to reset and start over.
    self._reset();
    
    log('about to wait', self.options.waitMsecs);
    setTimeout(function() {self.scheduleNextWork();}, self.options.waitMsecs);
  }
};

ParseHook.prototype.getServers = function() {
  var self = this
    , redis = this._redis
    , serversSetKey = realtime.serversSetKey;

  redis.zrange(serversSetKey, 0, -1, function(err, servers) {
    if(err) return log('error ocurred when getting servers from: ', serversSetKey, err);
    log('servers', servers);

    if(servers.length > 0) {
      log('retrieved '+servers.length+' servers');
      self._serverList = servers; 
    }

    //if there are no servers, this will just fall through to the wait state.
    self.scheduleNextWork();
  });
};

ParseHook.prototype.cacheImmediateTryLock = function() {
  var self = this;

  self._immediate = new TryLock(self._redis);
  self._immediate.on('locked', function(key, releaseLock){
    log('got lock on immediate');
    //get lines, if any. Process lines as needed.
    releaseLock();
  });
  self._immediate.on('error', function(err, key){log('immediate trylock error', err, key);});
  self._immediate.on('done', function(key){
    log('finished immediate');
    self.scheduleNextWork();
  });
};

ParseHook.prototype.cacheServerTryLock = function() {
  var self = this;

  self._server = new TryLock(self._redis);
  self._server.on('locked', function(server, releaseLock){
    log('got lock on: ', server);
    var serverInfo = realtime.parseServerSetClient(server)
      , linesSetKey = realtime.getLinesSetKey(serverInfo.ip, serverInfo.port)
      , linesOlderThanDelay = Date.now()-(self.options.delaySecs*1000);

      //todo get isValidVerified from redis (how would this become invalid? expires?)
      //if not in redis, get from mongo, place into redis
      //only do following if the server is valid and verified.

    //get a list of lines from redis
    self._redis.zrangebyscore(linesSetKey, 0, linesOlderThanDelay, function(err, rawLines) {
      if(err) return log('error occurred when getting lines from: ', linesSetKey, err);
      log('got '+rawLines.length+' lines from redis');

      if(rawLines.length > 0) {
        context = realtime.getContext(serverInfo.game, self, self._redis, self._mongoose, conf);
        
        //clean up the lines
        var lines = [];
        rawLines.forEach(function(rawLine) {
          lines.push(realtime.deHashLogLine(rawLine));
        });

        //send to parser for processing
        log('sending '+lines.length+' lines to parser');
        context.parseLines(lines, serverInfo.ip, serverInfo.port, function(err) {
          if(err) {
            log('error in parseLines', err);
            return releaseLock();
          }

          //processing was a success, remove old data.
          self._redis.zremrangebyscore(linesSetKey, 0, linesOlderThanDelay, function(err) {
            if(err) /*don't return*/ log('error removing processed lines');

            releaseLock();
          });
        });
      } else releaseLock();
    }); 
  });
  self._server.on('error', function(err, server){log(err, server);});
  self._server.on('done', function(server){
    self.scheduleNextWork();
  });
};

ParseHook.prototype._reset = function() {
  log('resetting state');
  this._state = this.DO_IMMEDIATE;
  this._serverList = []; //set of servers from redis
  this._serverIndex = 0; //where we are at within array;
  this._iterationStart = null;
  this._numServersToParse = this.options.numParallel;
};

/**
  Helper object to get only do an action if a lock can be made. Otherwise, nothing is done.
*/
function TryLock(redis) {
  if(!(this instanceof TryLock)) return new TryLock(redis);

  //call super ctor
  EventEmitter.call(this);
  
  this._redis = redis; //this should be a raw redis client object, or at least inherit from it
  this.TIMEOUT_MS = 5 * 1000;
}
util.inherits(TryLock, EventEmitter);

/**
  Actually tries to lock the key given. Be sure to add event handlers before invoking!
  @param key String of key to try to lock. The lock will be in: key+":lock"
*/
TryLock.prototype.tryLock = function(key) {
  var self = this
    , redis = self._redis
    , timeout = Math.floor((Date.now()+self.TIMEOUT_MS)/1000); //redis wants timeouts in secs, not msecs
  
  if(!key) return self.emit('error', 'no key provided', key);
  var lockKey = key+':lock';
  
  //create our function to eventually release the lock manually, if necessary.
  var releaseLock = function(){
    log('TryLock: about to release lock on: ', key);
    redis.get(lockKey, function(err, data){
      if(err) return self.emit('error', err, key);

      data = parseInt(data, 10);
      
      //if lock has not already been deleted or taken by someone else, remove it.
      if(data === timeout) {
        log('lock is still ours, going to delete: ', key);
        redis.del(lockKey, function(err){
          if(err) return self.emit('error', err, key);
          else return self.emit('done', key);
        });
      } else return self.emit('done', key);
    });
  };
  
  //try to lock
  redis.setnx(lockKey, timeout, function(err, data){
    if(err) return self.emit('error', err, key);
    
    //data will be a 1 or 0 if lock succeeded or not.
    if(data) {
      log('TryLock: got lock for', key);
      //we have our lock, let's tell redis when to remove it
      redis.expireat(lockKey, timeout, function(err) {
        if(err) self.emit('error', err, key);
      });

      //pass control back to caller
      return self.emit('locked', key, releaseLock);
    } else {
      //lock did not succeed. Someone else is already processing the resource.
      //We can just move on.
      log('TryLock: NOT ABLE TO LOCK', key, data);
      return self.emit('done', key);
    }
  });
};

//todo this shouldn't really start the hook
//todo mongo MUST be run in 64bit mode
//should also set environment properly.
var parser = new ParseHook({delaySecs: 90, waitMsecs: 5*1000});
parser.start();