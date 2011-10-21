/**
  Helper functions for processing data in real time.
  Contexts are objects that tell the real time servers how to act upon data from a specific source.
*/
var util = require('util')
  , exports = module.exports
  , contexts = exports.contexts = {};

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
  Reference to the set of servers in Redis. To put a value in this set, see getServersSetClient
*/
var getServersSetKey = exports.serversSetKey = 'rts:servers';

/**
  For the LineServer to hash the log line so that it is guaranteed to be unique and ordered properly.
*/
var hashLogLine = exports.hashLogLine = function(logLine, received, lastReceivedCounter) {
  //concat'ing the . casts both sides as string instead of adds, easier to read
  return received+'.'+lastReceivedCounter+logLine+':**:';
};

/**
  Takes a logLine from hashLogLine and strips the hash.
*/
var deHashLogLine = exports.deHashLogLine = function(logLineHashed) {
  var sepIndex = logLineHashed.indexOf(':**:');
  if(sepIndex >= 0) {
    return logLineHashed.substr(sepIndex);
  } else return logLineHashed;
};

/** Base Context function - Use as interface for every other context. */
function Context() {}

/**
  Convert a Node.js buffer object that came in through the LineServer to a regular String.
*/
Context.prototype.convertFromBuffer = function(buffer) {};

/** SourceContext - common functionality for any Source engine game */
function SourceContext() {
  Context.call(this);

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
var TF2Context = contexts['tf2'] = function() {
  SourceContext.call(this);
}
util.inherits(TF2Context, SourceContext);