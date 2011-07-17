
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
  app.use(function(err, req, res, next){
    res.render('500', {
        status: err.status || 500
      , error: err
      , title: 'Unrecoverable Error'
    });
  });
});

app.configure('development', function(){
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});

app.configure('production', function(){
  app.use(express.errorHandler());
});

//setup Mongoose
mongoose.connect(conf.dataDbUrl);
loadModules('/schemas', /Schema(s?)\.js$/, mongoose, conf); //pull in models

/**
  Add our routes. Breaking these out into separate files to keep things clean.
*/
loadModules('/routes', /Route(s?)\.js$/, app, conf, mongoose);

app.listen(conf.port);
util.log("Express server listening on port "+app.address().port+" in "+app.settings.env+" mode");

/**
  Convenience function to batch load a bunch of modules from a directory, and call the function with supplied arguments.
  This only works for modules that are in the form of "module.exports = function(arg1){doStuff;}"
  After specifying the arguments that are required, you may optionally pass additional arguments that should be given to the module.
  @param baseDir - baseDirectory directory to load modules from (ie. "/routes")
  @param fileRegEx - regular expression that matches the files to include (ie. /\.js$/ to grab any file that ends with ".js")
*/
function loadModules(baseDir, fileRegEx) {
  //set our base directory for routes
  var baseDirectory = __dirname + baseDir;

  //read the contents. Using sync here because we need to get results before moving on with server setup.
  var files = require('fs').readdirSync(baseDirectory);
  for(var i in files) {
    //if the file matches the regex, then load it. require will return a function.
    //use the extra arguments given to this function to call the function
    if(files[i].match(fileRegEx)) require(baseDirectory + '/' + files[i]).apply(this, _u.toArray(arguments).slice(2));
  }
}

