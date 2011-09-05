#!/usr/bin/env node
var argv = require('optimist').argv;
/**
  Used to send a bunch of udp messages to a server to make it easier to test the UDP server.
*/

require('../udpspammer').start(argv.file, argv.port);