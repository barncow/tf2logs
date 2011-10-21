/*
  This will take lines from a game server and put them into Redis.
*/

var redislib = require('redis')
  , udp = require('dgram')
  , util = require('util')
  , realtime = require('../lib/realtime');

var LineServer = function(port, game) {
  if(!(this instanceof LineServer)) return new LineServer(port, game);

  if(!port) throw 'Port is required.'
  if(!game) throw 'Game is required.';

  var contextClass = realtime.contexts[game];
  if(!contextClass) throw 'Game is not supported: '+game;
  this.convertFromBuffer = new contextClass().convertFromBuffer;

  this._server = null;
  this._redis = null;
  this.port = port;
  this.game = game; //what game this server is taking lines from (ie. tf2)
  this._START_INDEX = 5; //where the udp message should start - garbage? data before this point.
  this._END_DECREMENT = 2; //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
  this._lastReceived = null; //need to track the Date.now() of the last message received, since it can repeat when it shouldn't.
  this._lastReceivedCounter = 0; //keep track of how many lines have occurred at the same Date.now()
};
module.exports = LineServer;

/**
  Connects to Redis, starts server
*/
LineServer.prototype.start = function() {
  var self = this;

  self._redis = redislib.createClient();
  self._server = udp.createSocket("udp4");

  self._server.on("message", function(msg, rinfo) {
    if(msg.length < this._START_INDEX+this._END_DECREMENT) return; //invalid msg
    var logLine = self.convertFromBuffer(msg);
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
LineServer.prototype.stop = function() {
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
LineServer.prototype.onMessage = function(received, logLine, ip, port) {
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

  var redis = self._redis
    , clientKey = realtime.getServersSetClient(ip, port, self.game)
    , linesSetKey = realtime.getLinesSetKey(ip, port)
    , serversSetKey = realtime.serversSetKey
    , logLineHashed = realtime.hashLogLine(logLine, received, self._lastReceivedCounter); 

  redis.zadd(linesSetKey, received, logLineHashed, function(err, data){
    if(err) return util.log("Error ocurred when saving line to sorted set in onMessage: "+err, received, logLineHashed, ip, port);

    //redis returns the number of items that were added to the set (not score changed items)
    //if it is not one, something wrong happened.
    if(data !== 1) return util.log("Line was added to sorted set in onMessage: ", received, logLineHashed, ip, port);

    //we are still here, therefore everything went OK with saving the line.
    //let's add (or update the score of) the server to the sorted set of servers.
    //doing this since it will be easy to tell when servers are stale.
    redis.zadd(serversSetKey, received, clientKey, function(err){
      if(err) return util.log("Could not add server to sorted servers set: "+err, received, clientKey);
    });
  });
};

//todo this file shouldn't really start the server
var s = new LineServer(2600, 'tf2').start();