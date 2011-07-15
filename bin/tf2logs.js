#!/usr/bin/env node

//This is in charge of launching the application.

(function() {
  var environment = 'development';
  
  //skipping the first two which will be node and this file
  for(var i = 2; i < process.argv.length; ++i) {
    var token = process.argv[i];
    switch(token) {
      case '-d':
        environment = 'development';
      break;
      case '-t':
        environment = 'test';
      break;
      case '-q':
        environment = 'qa';
      break;
      case '-p':
        environment = 'production';
      break;
      default: throw new Error("The switch '"+token+"' could not be recognized.");
    }
  }
  
  //setting the NODE_ENV environment variable, which Connect and Express use to configure themselves
  process.env.NODE_ENV = environment;
  
  var envConfig = require('../conf/environments.js'), app = require('../app.js');
  
  app.listen(envConfig[environment].port);
  console.log("Express server listening on port %d in %s mode", app.address().port, app.settings.env);
})();
