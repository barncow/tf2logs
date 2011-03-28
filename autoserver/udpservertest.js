var
  assert = require('assert'),
  udpServer = require('./udpserver.js'),
  cfg = require('./udpserverconfig_test.js').udpServerConfig,
  parsingUtils = new udpServer.ParsingUtils()/*,
  driver = new udpServer.DBDriver(cfg.DB_USER, cfg.DB_PASS, cfg.DB_DATABASE, cfg.DB_CONNECTIONS),
  logUDPServer = new udpServer.LogUDPServer(cfg.SERVER_PORT, driver)*/;
  
var logLine = 'L 03/27/2011 - 18:00:08: "Console<0><Console><Console>" say "fresh prince of bel air"';

//test helper function - will take a normally formatted logLine and pad it to a standard UDP message.
//used for onMessage to simulate a UDP message. 
function udpMessage(logLine) {
  return new Buffer('.....'+logLine+'..', 'utf8');
}
//quick assertion for helper
assert.equal('.....L 03/27/2011 - 18:00:08: "Console<0><Console><Console>" say "fresh prince of bel air"..', udpMessage(logLine).toString('utf8'));

//creates a basic UDP onMessage rinfo object, to be used when simulating an onMessage.
//both arguments are optional.
function createRinfo(ip, port) {
  if(!ip) ip = '255.255.255.255';
  if(!port) port = 2700;
  
  return {address: ip, port: port};
}
//quick assertions for helper
assert.deepEqual({address: '255.255.255.255', port: 2700}, createRinfo());

////////////////////////////////////
//TESTS FOR PARSING UTILS
////////////////////////////////////

//check that getTimestamp works for sunny case
assert.deepEqual({
  month: 3,
  day: 27,
  year: 2011,
  hour: 18,
  minute: 0,
  second: 8
}, parsingUtils.getTimestamp(logLine));

//check that getTimestamp will return the value false if the timestamp is corrupt.
assert.ok(!parsingUtils.getTimestamp(logLine.substring(0, 10)));

//get the logLineDetails - which is the log line without the timestamp chars.
assert.equal('"Console<0><Console><Console>" say "fresh prince of bel air"', parsingUtils.getLogLineDetails(logLine));

//check that getLogLineDetails on a corrupt string returns false.
assert.ok(!parsingUtils.getLogLineDetails(logLine.substring(0, 10)));

////////////////////////////////////
//TESTS FOR LogUDPServer._onMessage
////////////////////////////////////

//test onMessage sunny case
//assert.equal(logUDPServer.STATUS_SUCCESS, logUDPServer._onMessage(udpMessage(logLine), createRinfo()));


//this MUST be last - closes the pool and then the test suite.
//logUDPServer.stop(); //has problems actually closing due to mysql pool
