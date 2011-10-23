/**
  Helper functions for processing data in real time.
  Contexts are objects that tell the real time servers how to act upon data from a specific source.
*/
var util = require('util')
  , exports = module.exports
  , contexts = exports.contexts = {}
  , TF2LogParser = require('tf2logparser').TF2LogParser;

/**
  Gets an instantiated context. Caches the instantiated versions.
*/
var initializedContexts = {};
var getContext = exports.getContext = function(game, hook, redis, mongoose, conf) {
  var context = initializedContexts[game];

  if(!context) {
    var contextClass = contexts[game];
    if(!contextClass) return false;

    context = new contextClass(hook, redis, mongoose, conf);
    initializedContexts[game] = context;
  }

  return context;
};

/**
  Converts an ip, port combination to something that can be used as a key in Redis.
*/
var getClientKey = exports.getClientKey = function(ip, port) {
  return ip+'-'+port;
};

/**
  Converts an ip, port combination to a key to reference a server's buffer of lines in Redis.
*/
var getLinesSetKey = exports.getLinesSetKey = function(ip, port) {
  return 'rts:'+getClientKey(ip, port)+':lines';
};

/**
  Converts an ip, port combination to a key to reference a server's parser state in Redis.
*/
var getClientStateKey = exports.getClientStateKey = function(ip, port) {
  return 'rts:'+getClientKey(ip, port)+':state';
};

/**
  Converts an ip, port, game combination to a value to put in the servers set in Redis.
*/
var getServersSetClient = exports.getServersSetClient = function(ip, port, game) {
  return game+'-'+getClientKey(ip, port);
};

/**
  Takes the string from getServersSetClient and returns an object with keys ip, port, and game.
*/
var parseServerSetClient = exports.parseServerSetClient = function(serversSetClient) {
  var a = serversSetClient.split('-');
  if(a.length !== 3) return false;
  return {game: a[0], ip: a[1], port: a[2]};
};

/**
  Reference to the set of servers in Redis. To put a value in this set, see getServersSetClient
*/
var serversSetKey = exports.serversSetKey = 'rts:servers';

/**
  Stores the messages that should be processed ASAP.
*/
var immediateMessagesKey = exports.immediateMessagesKey = 'rts:immediate';

/**
  For the LineServer to hash the log line so that it is guaranteed to be unique and ordered properly.
*/
var HASH_SEPARATOR = ':**:';
var hashLogLine = exports.hashLogLine = function(logLine, received, lastReceivedCounter) {
  //concat'ing the . casts both sides as string instead of adds, easier to read
  return received+'.'+lastReceivedCounter+HASH_SEPARATOR+logLine;
};

/**
  Takes a logLine from hashLogLine and strips the hash.
*/
var deHashLogLine = exports.deHashLogLine = function(logLineHashed) {
  var sepIndex = logLineHashed.indexOf(HASH_SEPARATOR);
  if(sepIndex >= 0) {
    sepIndex += HASH_SEPARATOR.length;
    return logLineHashed.substr(sepIndex);
  } else return logLineHashed;
};

/**
  Iterates over the array with a function, that will take a function to call the next item in the array.
  @param array The array to iterate over
  @param iteratorFn(next, value, index, array) The function that will be called for each item in the array. Call next to go to the next item in the array.
  @param callbackFn The function that will be called when everything is iterated over.
*/
function iterateOver(array, iteratorFn, callbackFn) {
  if(!array || array.length == 0) callbackFn();

  var index = 0;

  //helper function to hopefully prevent stack overflows
  var nextTick = function(fn) {
    var args = Array.prototype.slice.call(arguments);
    args.shift(); //remove fn
    process.nextTick(function() {
      fn.apply(null, args);
    });
  }

  //increments the iteration, calls the iteratorFn if there are more elements to iterate, otherwise callbackFn.
  var next = function() {
    ++index;

    if(index < array.length) {
      nextTick(iteratorFn, next, array[index], index, array);
    } else {
      nextTick(callbackFn);
    }
  }

  //start the loop
  nextTick(iteratorFn, next, array[index], index, array);
}

/** Base Context function - Use as interface for every other context. */
function Context(hook, redis, mongoose) {
  this.hook = hook;
  this.redis = redis;
  this.mongoose = mongoose;
}

/**
  Convert a Node.js buffer object that came in through the LineServer to a regular String.
*/
Context.prototype.convertFromBuffer = function(buffer) {};

/**
  Takes an array of logLines, and is expected to do all necessary processing.
  @param lines array of lines to process
  @callback(err) callback to call when processing is complete.
*/
Context.prototype.parseLines = function(lines, ip, port, callback) {};

/** SourceContext - common functionality for any Source engine game */
function SourceContext(hook, redis, mongoose) {
  Context.call(this, hook, redis, mongoose);

  this._START_INDEX = 5; //where the udp message should start - garbage? data before this point.
  this._END_DECREMENT = 2; //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
}
util.inherits(SourceContext, Context);

SourceContext.prototype.convertFromBuffer = function(buffer) {
  //convert message to string, stripping unneeded chars.
  return buffer.toString('utf8', this._START_INDEX, buffer.length - this._END_DECREMENT);
};

/**
  TF2Context - functionality needed to parse TF2 information.
*/
var TF2Context = contexts['tf2'] = function(hook, redis, mongoose) {
  var self = this;

  SourceContext.call(self, hook, redis, mongoose);

  self.parser = new TF2LogParser({isRealTime: true});
  self.parser.originalState = self.pullStateFromParser();
  self.parser.on('start', function() {
    util.log('Game Started event');
    self.status = 'start';
  });
  self.parser.on('done', function(log) {
    util.log('Game Ended event');
    self.status = 'done';
    self.sequence = self.parser._state.tf2logsSequence;
    self.log = log;
  });
}
util.inherits(TF2Context, SourceContext);

TF2Context.prototype.pullStateFromParser = function() {
  //fetching direct properties to avoid any processing that could be done - just want raw values
  var state = {
      state: this.parser._state
    , log: this.parser._log._log
  };

  //make sure that we are refreshing state
  delete this.parser._state;
  delete this.parser._log._log;

  return state;
};

TF2Context.prototype.placeStateIntoParser = function(state) {
  this.parser._state = state.state;
  this.parser._log._log = state.log;
};

TF2Context.prototype.parseLines = function(lines, ip, port, callback) {
  var self = this
    , clientStateKey = getClientStateKey(ip, port);
  //get state from redis
  self.redis.get(clientStateKey, function(err, state) {
    if(err) return callback(err);

    //if we have a state, parse it into an object, if not, grab the original state of the parser, and put it into the parser.
    if(state) state = JSON.parse(state);
    else state = self.parser.originalState;
    self.placeStateIntoParser(state);

    //send lines to parser for processing
    iterateOver(lines, function(next, line, index) {
      var deltas = self.parser.parseLine(line);
      if(deltas.length > 0) self.hook.emit('gameChange', {ip: ip, port: port, id: self.parser._state.tf2logsSequence, deltas: deltas});

      if(self.status === 'start') {
        delete self.status;

        util.log('Game Started (from loop)');
        //reserve log in mongo, save id to state
        self.mongoose.model('Log').reserveLog({
            name: 'generated log' //todo get proper name
          , mapName: 'cp_hydro' //todo get proper map name
        }, function(err, sequence) {
          if(err) return callback(err);
          self.hook.emit('gameStarted', {ip: ip, port: port, id: sequence});
          self.parser._state.tf2logsSequence = sequence;
          next();
        });
      } else if(self.status === 'done') {
        var sequence = self.sequence
          , log = self.log;

        delete self.status;
        delete self.sequence;
        delete self.log;

        util.log('Game Ended (from loop)');
        //save to mongodb
        self.mongoose.model('Log').completeLog(sequence, log, function(err) {
          if(err) return callback('Could not save log: '+err);
          self.hook.emit('gameEnded', {ip: ip, port: port, id: sequence});
          next();
        });
      } else next();
    }, function() {
      //save state to redis
      var stateJSON = JSON.stringify(self.pullStateFromParser());
      self.redis.set(clientStateKey, stateJSON, function(err) {
        if(err) return callback(err);
        return callback(null);
      });
    });
  });
};