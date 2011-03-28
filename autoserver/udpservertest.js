var
  assert = require('assert'),
  udpserver = require('./udpserver.js')
  parsingUtils = new udpserver.ParsingUtils();
  
var logLine = 'L 03/27/2011 - 18:00:08: "Console<0><Console><Console>" say "fresh prince of bel air"';

//TESTS FOR PARSING UTILS

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
