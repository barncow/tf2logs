#!/usr/bin/env node

/**
  This script is in charge of setting the environment, and then launching the application.
*/

require('../lib/environmentchooser')(function(environment){
  //start the server
  var app = require('../app.js');
});