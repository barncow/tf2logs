
module.exports = function(WebHook) {
  /**
   * Module dependencies.
   */

  var express = require('express')
    , stylus = require('stylus')
    , util = require('util')
    , MongoStore = require('connect-mongo')
    , _ = require('underscore')
    , mongoose = require('mongoose')
    , form = require('connect-form')
    , expressValidate = require('express-validate')
    , loadModules = require('./lib/loadmodules')
    , nowjs = require('now');

  var app = module.exports = express.createServer();

  //set default environment
  process.env.NODE_ENV = process.env.NODE_ENV || 'development';
  var conf = require('./conf/conf.js')()
    , sessionStore = new MongoStore({
    url: conf.sessionDbUrl
  });

  // Configuration
  app.configure(function(){
    app.set('views', __dirname + '/views');
    app.set('view engine', 'jade');
    app.use(express.bodyParser()); //handles regular forms
    app.use(form({ keepExtensions: true })); //handles multipart forms (could also do regular)
    app.use(expressValidate);
    app.use(express.methodOverride());
    app.use(express.cookieParser()); //make sure this is before session
    app.use(express.session({
      secret: conf.sessionSecret
      , key: conf.sessionCookieKey
      , store: sessionStore
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
  loadModules('/schemas', /Schema(s?)\.js$/, mongoose, conf, WebHook); //pull in models

  /**
    Add our routes. Breaking these out into separate files to keep things clean.
  */
  loadModules('/routes', /Route(s?)\.js$/, app, conf, mongoose, WebHook);

  app.listen(conf.port);
  util.log("Express server listening on port "+app.address().port+" in "+app.settings.env+" mode");

  //session support inspired by: http://www.danielbaulig.de/socket-ioexpress/
  var parseCookie = require('connect').utils.parseCookie
    , Session = require('connect').middleware.session.Session
    , everyone = nowjs.initialize(app, {
      socketio: {
        'authorization': function(data, accept) {
          if (data.headers.cookie) {
              data.cookie = parseCookie(data.headers.cookie);
              data.sessionID = data.cookie[conf.sessionCookieKey];
              // save the session store to the data object 
              // (as required by the Session constructor)
              data.sessionStore = sessionStore;
              sessionStore.get(data.sessionID, function (err, session) {
                  if (err) {
                      accept(err.message, false);
                  } else {
                      // create a session object, passing data as request and our
                      // just acquired session data
                      data.session = new Session(data, session);
                      accept(null, true);
                  }
              });
          } else {
             return accept('No cookie transmitted.', false);
          }
        }
      }
    });

  nowjs.on('connect', function() {
    var self = this;

    //set an interval to reload the session every minute and keep it active.
    self.user.sessionReloadInterval = setInterval(function () {
      self.socket.handshake.session.reload( function () { 
          self.socket.handshake.session.touch().save();
      });
    }, 60 * 1000);
  });

  nowjs.on('disconnect', function() {
    clearInterval(this.user.sessionReloadInterval);
  });
};