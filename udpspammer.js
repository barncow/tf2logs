//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';

var conf = require('./conf/conf.js')()
  , ReadFile = require('tf2logparser').ReadFile
  , util = require('util')
  , udp = require('dgram')
  , client = null;

/**
  Starts the spamming.
  @param file log file to send
  @port number of port to send from
*/
var start = module.exports.start = function(file, port) {
  util.log('starting output of: '+file)
  client = udp.createSocket("udp4");
  client.bind(port);
  var rf = new ReadFile();
  rf.on("line", function(line) {
    util.log("sending: "+line)
    var b = new Buffer("....."+line+".\n"); //also padding lines with junk
    client.send(b, 0, b.length, conf.udpServerPort, "localhost");
  });
  rf.on("done", stop);
  rf.readFile(file);
};

/**
  Stops everything
*/
var stop = module.exports.stop = function() {
  util.log('stopping spammer');
  if(client) client.close();
};