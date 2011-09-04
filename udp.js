//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';

var conf = require('./conf/conf.js')()
    , mongoose = require('mongoose')
    , udp = require('dgram')
    , util = require('util')
    , loadModules = require('./lib/loadmodules')
    , tf2lib = require('tf2logparser')
    , _ = require('underscore')
    , TF2LogParser = tf2lib.TF2LogParser
    , server = null //reference to our UDP server
    , clients = module.exports.clients = {} //holds all clients currently connected to this server. Keys are IP:Port (ie. 255.255.255.255:27015)
    , START_INDEX = 5 //where the udp message should start - garbage? data before this point.
    , END_DECREMENT = 2 //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
    , PARSE_DELAY_MSECS = module.exports.PARSE_DELAY_MSECS = conf.udpParseDelaySecs*1000
    , CLIENT_ITERATION_MSECS = 1 * 1000
    , CLIENT_ITERATION_TIMEOUT = null
    , CLIENT_TIMEOUT_MSECS = conf.udpClientTimeoutSecs*1000
    , CLIENT_STALE_TIMEOUT = null;

/**
  Connects to Mongo, starts server
*/
var start = module.exports.start = function() {
  util.log('starting server');

  //setup Mongoose
  mongoose.connect(conf.dataDbUrl);
  loadModules('./schemas', /Schema(s?)\.js$/, mongoose, conf); //pull in models

  server = udp.createSocket("udp4");

  server.on("message", function(msg, rinfo) {
    //convert message to string, stripping unneeded chars.
    var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
    onMessage({received: Date.now(), logLine: logLine}, rinfo.address, rinfo.port);
  });

  server.on("listening", function() {
    var address = server.address();
    util.log("server listening " + address.address + ":" + address.port + " in '"+process.env.NODE_ENV+"' environment");
  });

  server.bind(conf.udpServerPort);
  iterateClients();
  removeStaleClients();
};

/**
  Stops everything
*/
var stop = module.exports.stop = function() {
  util.log('stopping server');
  mongoose.disconnect();
  if(server) server.close();
  if(CLIENT_ITERATION_TIMEOUT !== null) clearTimeout(CLIENT_ITERATION_TIMEOUT);
  if(CLIENT_STALE_TIMEOUT !== null) clearTimeout(CLIENT_STALE_TIMEOUT);
};

/**
  On message handler. Customized to make testing easier, and factor out dgram subtleties
  @param logLineObj obj in the form of {received: Date.now(), logLine: "L my log line, etc"}
  @param ip ip of server that sent the message
  @param port port of server that sent the message
*/
var onMessage = module.exports.onMessage = function(logLineObj, ip, port) {
  util.log('Message Received ('+ip+':'+port+'): ' + logLineObj.logLine); //todo remove
  var clientKey = ip+":"+port
  , client = clients[clientKey];
  
  if(typeof client === 'undefined') {
    util.log('no client found, creating new');
    clients[clientKey] = new Client(ip, port, logLineObj);
  } else {
    util.log('client found, adding line');
    client.addLine(logLineObj);
  }
};

/**
  iterates over the clients, telling them to process their lines.
*/
var iterateClients = module.exports.iterateClients = function(){
  util.log('iterating clients');
  var now = Date.now();

  _.each(clients, function(client) {
    client.processLines(now);
  });

  CLIENT_ITERATION_TIMEOUT = setTimeout(iterateClients, CLIENT_ITERATION_MSECS);
}

/**
  Iterates over clients, removing those that have not received a message in the allotted time
*/
var removeStaleClients = module.exports.removeStaleClients = function(){
  util.log('removeTimedOutClients');
  var now = Date.now();

  _.each(clients, function(client, index, clientList) {
    if(client.isStale(now)) {
      util.log('removing client for (last message was '+client._lastLineReceived+', now: '+now+'): '+client._ip+":"+client._port);
      delete clientList[index];
    }
  });

  CLIENT_STALE_TIMEOUT = setTimeout(removeTimedOutClients, CLIENT_TIMEOUT_MSECS);
}

//todo set an interval to clean clients that are no longer active

/**
  Represents a client
*/
var Client = module.exports.Client = function(ip, port, logLineObj) {
  util.log('creating client for: '+ip+":"+port);
  this._ip = ip;
  this._port = port;
  this._parser = null; //create parser obj when ready and we know we can take info
  this._queuedLines = [logLineObj]; //while waiting for async operations or for 90 sec delay, we need to hold lines until we get a go, no-go decision.
  this._server = null;
  this._waitingForServerInfo = true;
  this._lastLineReceived = logLineObj.received;

  //send request to get server information. Will queue lines until we hear back.
  this.updateServer();
};

/**
  Determines if this client has timed out or is no longer active
*/
Client.prototype.isStale = function(now) {
  return (this._lastLineReceived+CLIENT_TIMEOUT_MSECS < now || this._server.get('active') === 'I');
};

/**
  Convenience function to easily output both the IP and Port
*/
Client.prototype.getIpAndPort = function() {
  return this._ip + ":" + this._port;
}

/**
  Retrieves the server info for this client. Delegates to _getServerInfo to actually perform the call to mongoose, so overriding it for testing is easy.
*/
Client.prototype.updateServer = function() {
  util.log('updating server info for: '+this.getIpAndPort());
  this._waitingForServerInfo = true;

  this._getServerInfo();
};

/**
  Calls mongoose to get the server info
*/
Client.prototype._getServerInfo = function() {
  util.log('getting server info from mongo for: '+this.getIpAndPort());
  var self = this;
  mongoose.model('Server').getServerForIPAndPort(this._ip, this._port, function(err, server) {
    //need to preserve "this" context for the callback
    Client.prototype._onGetServerInfo.call(self, err, server);
  });
}

/**
  Callback for when mongoose has retrieved server info
*/
Client.prototype._onGetServerInfo = function(err, server) {
  util.log('ongetserverinfo callback for: '+this.getIpAndPort());
  this._waitingForServerInfo = false;

  if(err) {
    util.log('Error retrieving server information for: '+this.getIpAndPort());
    util.log(err);
    return;
  }

  if(!server) {
    util.log('no server found for: '+this.getIpAndPort());
    //we have no server information. Pitch the lines
    this._queuedLines = [];
    return;
  }

  util.log('server found for: '+this.getIpAndPort());

  //still here, we have a valid server
  this._server = server;
  if(!this._parser) this._parser = new TF2LogParser({isRealTime: true});

  this._parser.on("done", function(log) {
      var logModel = mongoose.model('Log')
        , meta = {
          logName: 'auto generate'
          , mapName: server.map, 
        };
      logModel.createLog(log, meta, function(err, savedLog){
        if(err) util.log(err);      
      });
    });
  });
  
  //messages will be processed on next interval.
};

/**
  Adds a line to the queue, to be processed when server information is received and valid, and pass our delay
*/
Client.prototype.addLine = function(logLineObj) {
  util.log('given a line to add - if it is added the next log line will say so for: '+this.getIpAndPort());
  //add lines iff we are waiting for server info, or if we have a server
  if(this._waitingForServerInfo || this._server) {
    util.log('adding line for: '+this.getIpAndPort());
    this._queuedLines.push(logLineObj);
  }
  this._lastLineReceived = logLineObj.received;
};

/**
  Goes through buffered messages. If there is a server, and not waiting for server info, and the message is at least 90secs old, passes it to parser
  @param now essentially: Date.now()
*/
Client.prototype.processLines = function(now) {
  if(this._waitingForServerInfo || !this._server) {
    util.log('ignoring process lines for: '+this.getIpAndPort());
    return;
  }

  util.log('processing lines for: '+this.getIpAndPort());
  while(this._queuedLines.length > 0) {
    var logLineObj = this._queuedLines.shift(); //shifting so that the line is already removed if processed
    if(logLineObj.received+PARSE_DELAY_MSECS >= now) {
      this._parser.parseLine(logLineObj.logLine);
    } else this._queuedLines.unshift(logLineObj); //can't process line yet, unshift it back
  }
};