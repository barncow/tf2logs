//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';

var conf = require('./conf/conf.js')()
    , mongoose = require('mongoose')
    , udp = require('dgram')
    , util = require('util')
    , loadModules = require('./lib/loadmodules')
    , server = null //reference to our UDP server
    , clients = {} //holds all clients currently connected to this server. Keys are IP:Port (ie. 255.255.255.255:27015)
    , START_INDEX = 5 //where the udp message should start - garbage? data before this point.
    , END_DECREMENT = 2; //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
  
//setup Mongoose
mongoose.connect(conf.dataDbUrl);
loadModules('./schemas', /Schema(s?)\.js$/, mongoose, conf); //pull in models

var start = module.exports.start = function() {
  server = udp.createSocket("udp4");

  server.on("message", function(msg, rinfo) {
    //convert message to string, stripping unneeded chars.
    var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
    onMessage(logLine, rinfo.address, rinfo.port);
  });

  server.on("listening", function() {
    var address = server.address();
    util.log("server listening " + address.address + ":" + address.port + " in '"+process.env.NODE_ENV+"' environment");
  });

  server.bind(conf.udpServerPort);
};

var onMessage = module.exports.onMessage = function(logLine, ip, port) {
  util.log(logLine);
};