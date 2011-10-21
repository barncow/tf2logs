/*
  This will take lines from TF2 (and probably other Source engine games) and put them into Redis.
*/

var redislib = require('redis')
  , udp = require('dgram')
  , util = require('util');

var SourceLineServer = function(port) {
  if(!(this instanceof SourceLineServer)) return new SourceLineServer(port);

  this._server = null;
  this._redis = null;
  this.port = port;
  this._START_INDEX = 5; //where the udp message should start - garbage? data before this point.
  this._END_DECREMENT = 2; //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
  this._lastReceived = null; //need to track the Date.now() of the last message received, since it can repeat when it shouldn't.
  this._lastReceivedCounter = 0; //keep track of how many lines have occurred at the same Date.now()
};
module.exports = SourceLineServer;

/**
  Connects to Redis, starts server
*/
SourceLineServer.prototype.start = function() {
  var self = this;

  self._redis = redislib.createClient();
  self._server = udp.createSocket("udp4");

  self._server.on("message", function(msg, rinfo) {
    if(msg.length < this._START_INDEX+this._END_DECREMENT) return; //invalid msg
    //convert message to string, stripping unneeded chars.
    var logLine = msg.toString('utf8', self._START_INDEX, msg.length - self._END_DECREMENT);
    self.onMessage(Date.now(), logLine, rinfo.address, rinfo.port);
  });

  self._server.on("listening", function() {
    var address = self._server.address();
    util.log("server listening " + address.address + ":" + address.port);
  });

  self._server.bind(self.port);
};

/**
  Stops everything
*/
SourceLineServer.prototype.stop = function() {
  if(this._server) this._server.close();
  if(this._redis) this._redis.quit();
};

/**
  On message handler. Customized to make testing easier, and factor out dgram subtleties
  @param received timestamp that the message was received
  @param logLine string of the message that was received (cleaned up as necessary)
  @param ip ip of server that sent the message
  @param port port of server that sent the message
*/
SourceLineServer.prototype.onMessage = function(received, logLine, ip, port) {
  util.log('Message Received ('+ip+':'+port+'): ' + logLine); //todo remove
  var self = this;

  //if the Date.now() did not change from the last time a message was received
  //increment counter, otherwise reset with the current Date.now().
  if(received === self._lastLineReceived) {
    ++self._lastReceivedCounter;
  } else {
    self._lastLineReceived = received;
    self._lastReceivedCounter = 0;
  }

  var clientKey = ip+'-'+port
    , linesSetKey = 'rts:'+clientKey+':lines'
    , logLineHashed = received+'.'+self._lastReceivedCounter+logLine;

  self._redis.zadd(linesSetKey, received, logLineHashed, function(err, data){
    if(err) util.log("Error ocurred in onMessage: "+err, received, logLineHashed, ip, port);

    //redis returns the number of items that were added to the set (not score changed items)
    //if it is not one, something wrong happened.
    if(data !== 1) util.log("Line was added to set in onMessage: ", received, logLineHashed, ip, port)
  });
};

//todo this file shouldn't really start the server
var s = new SourceLineServer(2600).start();