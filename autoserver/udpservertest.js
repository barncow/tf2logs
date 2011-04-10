var
  assert = require('assert'),
  udpServer = require('./udpserver.js'),
  cfg = require('./udpserverconfig_test.js').udpServerConfig,
  parsingUtils = new udpServer.ParsingUtils(),
  driver = new udpServer.DBDriver(cfg.DB_USER, cfg.DB_PASS, cfg.DB_DATABASE, cfg.DB_CONNECTIONS),
  logUDPServer = new udpServer.LogUDPServer(cfg.SERVER_PORT, driver, cfg.SITE_BASE_DIR, cfg.SITE_ENV),
  logDAO = new udpServer.LogDAO(driver);
  
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

//check that getting verifykey from non verifykey line returns false
assert.ok(!parsingUtils.getVerifyKey(parsingUtils.getLogLineDetails(logLine)));

//check that one form of chat message gets our key
var verifykey1 = 'L 04/02/2011 - 10:05:03: "Console<0><Console><Console>" say "tf2logs:627385e4af85"';
assert.equal('tf2logs:627385e4af85', parsingUtils.getVerifyKey(parsingUtils.getLogLineDetails(verifykey1)));

//check that another form of chat message gets our key
var verifykey2 = 'L 04/02/2011 - 10:05:19: "Console<0><Console><Console>" say ""tf2logs:61867d3ed590""';
assert.equal('tf2logs:61867d3ed590', parsingUtils.getVerifyKey(parsingUtils.getLogLineDetails(verifykey2)));

//check that can get world triggered events
var roundStartLine = 'L 09/29/2010 - 19:08:56: World triggered "Round_Start"';
assert.ok(parsingUtils.isWorldTriggeredEvent(parsingUtils.getLogLineDetails(roundStartLine))); //with only one arg, just determines if it is a world triggered line
assert.ok(!parsingUtils.isWorldTriggeredEvent(parsingUtils.getLogLineDetails(logLine)));
assert.ok(parsingUtils.isWorldTriggeredEvent(parsingUtils.getLogLineDetails(roundStartLine), "Round_Start"));
assert.ok(!parsingUtils.isWorldTriggeredEvent(parsingUtils.getLogLineDetails(roundStartLine), "Game_Over"));

//check that roundStart is detected
assert.ok(parsingUtils.isRoundStart(parsingUtils.getLogLineDetails(roundStartLine)));
assert.ok(!parsingUtils.isRoundStart(parsingUtils.getLogLineDetails(logLine)));

//check that game over is detected
var gameOverLine = 'L 01/31/2011 - 21:11:22: World triggered "Game_Over" reason "Reached Time Limit"';
assert.ok(parsingUtils.isGameOver(parsingUtils.getLogLineDetails(gameOverLine)));
var logFileClosed = 'L 01/31/2011 - 21:20:54: Log file closed';
assert.ok(parsingUtils.isGameOver(parsingUtils.getLogLineDetails(logFileClosed)));
assert.ok(!parsingUtils.isGameOver(parsingUtils.getLogLineDetails(roundStartLine)));

//check that map lines are detected
var loadingMapLine = 'L 04/03/2011 - 15:20:36: Loading map "ctf_impact2"';
assert.equal('ctf_impact2', parsingUtils.getMap(parsingUtils.getLogLineDetails(loadingMapLine)));
var startingMapLine = 'L 04/03/2011 - 15:20:36: Started map "ctf_impact2" (CRC "1634099807")';
assert.equal('ctf_impact2', parsingUtils.getMap(parsingUtils.getLogLineDetails(startingMapLine)));
assert.ok(!parsingUtils.getMap(parsingUtils.getLogLineDetails(logLine)));

////////////////////////////////////
//TESTS FOR LogDAO
////////////////////////////////////
logDAO.getStatus('96.8.112.126',	27015, function(status){
  assert.equal(logDAO.SERVER_STATUS_ACTIVE, status);
});

////////////////////////////////////
//TESTS FOR LogUDPServer._onMessage
////////////////////////////////////

//test onMessage sunny case
assert.equal(logUDPServer.STATUS_SUCCESS, logUDPServer._onMessage(udpMessage(logLine), createRinfo()));

//test that the verifykey onmessage handler works
assert.equal(logUDPServer.STATUS_SUCCESS, logUDPServer._onMessage(udpMessage(verifykey1), createRinfo()));

//test that the map line onmessage handler works
assert.equal(logUDPServer.STATUS_SUCCESS, logUDPServer._onMessage(udpMessage(loadingMapLine), createRinfo()));


//this MUST be last - closes the pool and then the test suite.
logUDPServer.stop();
