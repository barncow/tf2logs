#!/usr/bin/env node

/**
  This script is in charge of setting the environment, and then launching server to track incoming UDP messages.
*/

require('../lib/environmentchooser')(function(environment){
  require('../udp').start();
});