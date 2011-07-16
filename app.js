
/**
 * Module dependencies.
 */

var express = require('express');

var app = module.exports = express.createServer();

var util = require('util');

var MongoStore = require('connect-mongo');

var _u = require('underscore');

var mongoose = require('mongoose');

//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';
var confAll = require('./conf/conf.js'), conf = {};
_u.extend(conf, confAll.def, confAll.env[process.env.NODE_ENV]);

// Configuration
app.configure(function(){
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(express.cookieParser()); //make sure this is before session
  app.use(express.session({
    secret: conf.sessionSecret
    , key: conf.sessionCookieKey
    , store: new MongoStore({
        url: conf.sessionDbUrl
      })
  })); //make sure this is before app.router
  app.helpers(require('./lib/helpers.js').helpers);
  app.dynamicHelpers(require('./lib/helpers.js').dynamicHelpers);
  app.use(app.router);
  app.use(express.static(__dirname + '/public'));
});

app.configure('development', function(){
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});

app.configure('production', function(){
  app.use(express.errorHandler());
});

//setup Mongoose
mongoose.connect(conf.dataDbUrl);
require('./lib/schemas.js')(mongoose); //pull in models

/**
  Add our routes. Breaking these out into separate files to keep things clean.
*/
(function(app, conf, mongoose) {
  //set our base directory for routes
  var baseDir = __dirname + '/routes';

  //read the contents. Using sync here because we need to get results before moving on with server setup.
  var files = require('fs').readdirSync(baseDir);
  for(var i in files) {
    //if the file ends with .js, then load it. require will return a function for our route script, so just run it right away passing app to it.
    if(files[i].match('\.js$')) require(baseDir + '/' + files[i])(app, conf, mongoose);
  }
})(app, conf, mongoose);

app.listen(conf.port);
util.log("Express server listening on port "+app.address().port+" in "+app.settings.env+" mode");

