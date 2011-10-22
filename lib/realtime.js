/**
  Helper functions for processing data in real time.
  Contexts are objects that tell the real time servers how to act upon data from a specific source.
*/
var util = require('util')
  , exports = module.exports
  , contexts = exports.contexts = {};

/**
  Gets an instantiated context. Caches the instantiated versions.
*/
var initializedContexts = {};
var getContext = exports.getContext = function(game, hook, redis, mongoose) {
  var context = initializedContexts[game];

  if(!context) {
    var contextClass = contexts[game];
    if(!contextClass) return false;

    context = new contextClass(hook, redis, mongoose);
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
  SourceContext.call(this, hook, redis, mongoose);
}
util.inherits(TF2Context, SourceContext);

TF2Context.prototype.parseLines = function(lines, ip, port, callback) {
  //todo get state from redis
  //todo save to mongodb, save state to redis, remove lines from redis

  this.hook.emit('pretend', {numLines: lines.length, ip: ip, port: port});

  console.log('pretending to process '+lines.length+' lines from ip: '+ip+', port: '+port);

  /*lines.forEach(function(line) {
    console.log(line);
  });*/

  callback(null);
};