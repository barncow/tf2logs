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
    , CLIENT_STALE_TIMEOUT = null
    , UDPHook = null;

/**
  Connects to Mongo, starts server
*/
var start = module.exports.start = function(hook) {
  util.log('starting server');

  //setup Mongoose
  mongoose.connect(conf.dataDbUrl);
  loadModules('./schemas', /Schema(s?)\.js$/, mongoose, conf); //pull in models

  server = udp.createSocket("udp4");

  server.on("message", function(msg, rinfo) {
    if(msg.length < START_INDEX+END_DECREMENT) return; //invalid msg
    //convert message to string, stripping unneeded chars.
    var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
    onMessage({received: Date.now(), logLine: logLine}, rinfo.address, rinfo.port);
  });

  server.on("listening", function() {
    var address = server.address();
    util.log("server listening " + address.address + ":" + address.port + " in '"+process.env.NODE_ENV+"' environment");
  });

  UDPHook = hook;
  //if a server change event was caught, see if the server is currently a client. If so, update.
  UDPHook.on('*::serverChange', function(data) {
    var clientKey = data.ip+":"+data.port
      , client = clients[clientKey];
    if(typeof client !== 'undefined') {
      client.updateServer();
    }

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
    client = clients[clientKey] = new Client(ip, port);
    client.addLine(logLineObj);
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

  CLIENT_STALE_TIMEOUT = setTimeout(removeStaleClients, CLIENT_TIMEOUT_MSECS);
}

//todo set an interval to clean clients that are no longer active

/**
  Represents a client
*/
var Client = module.exports.Client = function(ip, port) {
  util.log('creating client for: '+ip+":"+port);
  this._ip = ip;
  this._port = port;
  this._parser = null; //create parser obj when ready and we know we can take info
  this._queuedLines = []; //while waiting for async operations or for 90 sec delay, we need to hold lines until we get a go, no-go decision.
  this._server = null;
  this._waitingForServerInfo = true;
  this._verifyLine = null;

  //send request to get server information. Will queue lines until we hear back.
  this.updateServer();
};

Client.prototype.getVerifyString = function(logLine) {
  var matches = logLine.match(/^L \d\d\/\d\d\/\d\d\d\d - \d\d:\d\d:\d\d: "Console<0><Console><Console>" say "(tf2logs:[0-9a-f]+)/);
  if(!matches || matches.length == 0) return false;
  return matches[1];
};

/**
  Determines if this client has timed out or is no longer active
  Client is stale iff the client has no queued lines, and has not received a message within the timeout
  , or, if there is a server for this client, and it is inactive, it is also stale.
*/
Client.prototype.isStale = function(now) {
  return ((this._queuedLines.length === 0 && this._lastLineReceived+CLIENT_TIMEOUT_MSECS < now) || (this.server && this._server.get('active') === 'I'));
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
  mongoose.model('ServerMeta').getServerForIPAndPort(this._ip, this._port, function(err, server) {
    //need to preserve "this" context for the callback
    Client.prototype._onGetServerInfo.call(self, err, server);
  });
};

Client.prototype.verify = function(verifyString) {
  util.log('verifying '+this.getIpAndPort());
  var verified = this._server.verifyServer(this._ip, this._port, verifyString, function(err) {
    console.log('verified')
    if(err) {
      util.log('Error verifying server information for: '+this.getIpAndPort());
      util.log(err);
      return;
    }
  });
  if(verified) {
    util.log('verified '+this.getIpAndPort());
    UDPHook.emit('verifiedServer', {ip: this._ip, port: this._port});
  }
};

/**
  Callback for when mongoose has retrieved server info
*/
Client.prototype._onGetServerInfo = function(err, server) {
  var self = this;
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

  //now that we have our server info, if we have a verifystring, lets check it.
  if(this._verifyString) {
    this.verify(this._verifyString);
    this._verifyString = null;
  }

  if(!this._parser) this._parser = new TF2LogParser({isRealTime: true});

  this._parser.on("done", function(log) {
    util.log("Processing done for: "+self.getIpAndPort())
    var logModel = mongoose.model('Log')
      , meta = {
        logName: 'auto generate'
        , mapName: server.map, 
      };
    logModel.createLog(log, meta, function(err, savedLog){
      if(err) util.log(err); 
      util.log('Added log for: '+self.getIpAndPort()+ " id: "+savedLog.id);     
    });
  });

  //messages will be processed on next interval.
};

/**
  Adds a line to the queue, to be processed when server information is received and valid, and pass our delay
*/
Client.prototype.addLine = function(logLineObj) {
  util.log('given a line to add - if it is added the next log line will say so for: '+this.getIpAndPort());
  var verifyString = this.getVerifyString(logLineObj.logLine);

  if(verifyString && this._waitingForServerInfo) {
    //we received a verify string, but still waiting for server data. save for later.
    util.log('caching verifystring');
    this._verifyString = verifyString;
  } else if(verifyString && this._server) {
    this.verify(verifyString);
  } else if(this._waitingForServerInfo || this._server) { //add lines iff we are waiting for server info, or if we have a server
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

  while(this._queuedLines.length > 0) {
    var logLineObj = this._queuedLines.shift(); //shifting so that the line is already removed if processed
    if(logLineObj.received+PARSE_DELAY_MSECS <= now) {
      util.log('processing line for: '+this.getIpAndPort()+" "+logLineObj.logLine);
      this._parser.parseLine(logLineObj.logLine);
    } else {
      this._queuedLines.unshift(logLineObj); //can't process line yet, unshift it back
      break;
    }
  }
};