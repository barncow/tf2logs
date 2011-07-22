
/**
 * Module dependencies.
 */

var express = require('express')
  , stylus = require('stylus')
  , util = require('util')
  , MongoStore = require('connect-mongo')
  , _ = require('underscore')
  , mongoose = require('mongoose')
  , form = require('connect-form');

var app = module.exports = express.createServer();

//set default environment
process.env.NODE_ENV = process.env.NODE_ENV || 'development';
var conf = require('./conf/conf.js')();

// Configuration
app.configure(function(){
  app.set('views', __dirname + '/views');
  app.set('view engine', 'jade');
  app.use(express.bodyParser()); //handles regular forms
  app.use(form({ keepExtensions: true })); //handles multipart forms (could also do regular)
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
  app.use(stylus.middleware({
      src: __dirname + '/views'
    , dest: __dirname + '/public'
    , compile: function (str, path) {
      return stylus(str)
        .set('filename', path)
        .set('compress', true);
    }
  }));
  app.use(express.static(__dirname + '/public'));
  app.use(app.router);
  app.use(function(err, req, res, next){
    util.log(err.status+": "+err);
    res.render('500', {
        status: err.status || 500
      , error: err
      , title: 'Unrecoverable Error'
    });
  });
  app.use(function(req, res, next){
    res.render('404', { status: 404, url: req.url, title: 'Not Found' });
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
    if(files[i].match(fileRegEx)) require(baseDirectory + '/' + files[i]).apply(this, _.toArray(arguments).slice(2));
  }
}

