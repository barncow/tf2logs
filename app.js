
/**
 * Module dependencies.
 */

var express = require('express');

var app = module.exports = express.createServer();

var util = require('util');

var MongoStore = require('connect-mongo');

//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';
var envConfig = require('./conf/environments.js')[process.env.NODE_ENV], globalConfig = require('./conf/global.js');

// Configuration

app.configure(function(){
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(express.cookieParser()); //make sure this is before session
  app.use(express.session({
    secret: globalConfig.sessionSecret
    , key: globalConfig.sessionCookieKey
    , store: new MongoStore({
        url: envConfig.sessionDbUrl
      })
  })); //make sure this is before app.router
  app.use(app.router);
  app.use(express.static(__dirname + '/public'));
});

app.configure('development', function(){
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});

app.configure('production', function(){
  app.use(express.errorHandler());
});

/**
  Add our routes. Breaking these out into separate files to keep things clean.
*/
(function(app) {
  //set our base directory for routes
  var baseDir = __dirname + '/routes';

  //read the contents. Using sync here because we need to get results before moving on with server setup.
  var files = require('fs').readdirSync(baseDir);
  for(var i in files) {
    //if the file ends with .js, then load it. require will return a function for our route script, so just run it right away passing app to it.
    if(files[i].match('\.js$')) require(baseDir + '/' + files[i])(app);
  }
})(app);

app.listen(envConfig.port);
util.log("Express server listening on port "+app.address().port+" in "+app.settings.env+" mode");

