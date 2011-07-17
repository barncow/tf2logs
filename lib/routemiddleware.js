/**
  Common route middleware functions.
*/
var rm = module.exports;
var app, config, mongoose;
var util = require('util');

/**
  Initializes the module with needed variables.
*/
rm.init = function(_app, _config, _mongoose) {
  app = _app;
  config = _config;
  mongoose = _mongoose;
  return rm; //for chaining
}

/**
  Loads the current user to the request object, if a friendid is specified.
*/
rm.loadUser = function(req, res, next){
  req.user = null;
  if(req.session.friendid) {
    mongoose.model('Player').findByFriendId(req.session.friendid, function(err, player){
      if(err) next(err);
      if(!player) next(new Error('Friendid given to loaduser could not be found! id: '+req.session.friendid));
      else req.user = player;
      next();
    });
  } else next();
}

/**
  Restricts the route to only a logged in user, regardless of the user's role.
  Use: app.get('/myroute', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){doStuff();});
*/
rm.andRestrictToLoggedInUser = function(req, res, next) {
  if(req.session.friendid && req.user) next();
  else {
    req.flash('error', 'You must be logged in to perform that action.');
    res.redirect('/');
  }
}

/**
  Restricts the route to only a non-logged in user
  Use: app.get('/myroute', rm.loadUser, rm.andRestrictToNonLoggedInUser, function(req, res){doStuff();});
*/
rm.andRestrictToNonLoggedInUser = function(req, res, next) {
  if(!req.session.friendid && !req.user) next();
  else res.redirect('/'); //if we have a session, just redirect to the front page.
}

