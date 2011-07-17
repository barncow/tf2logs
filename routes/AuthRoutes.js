//Routes for handling authentication

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose);
  var verifyRoute = '/players/verify'; //using a variable here so that the openID and our route match
  var openid = require('openid');
  var relyingParty = new openid.RelyingParty(
    conf.baseUrl+verifyRoute, // Verification URL
    conf.baseUrl, // Realm (optional, specifies realm for OpenID authentication)
    true, // Use stateless verification
    false, // Strict mode
    null); // List of extensions to enable and include

  /**
    Decides if the user needs to log in, then sends to Steam to log in.
  */
  app.get('/players/login', rm.loadUser, rm.andRestrictToNonLoggedInUser, function(req, res){
    relyingParty.authenticate(conf.steamOpenIdProviderUrl, false, function(error, authUrl) {
      if (error) {
        req.flash('error', "Authentication failed.");
        console.log(error); //logging intended
      }
      else if (!authUrl) {
        req.flash('error', "Authentication failed.");
      }
      else res.redirect(authUrl);
    });
  });

  /**
    This is the return URL from OpenID authentication. We check if the authentication was a success, and if so, mark the user as logged in.
  */
  app.get(verifyRoute, rm.loadUser, rm.andRestrictToNonLoggedInUser, function(req, res){
    relyingParty.verifyAssertion(req, function(error, result) {
      if(!error && result.authenticated) {
        markSessionAsLoggedIn(req, result, conf, mongoose, function(err){
          if(err) req.flash('error', 'Could not save to database.');
          else req.flash('info', "You were successfully logged in.");
          res.redirect('/');
        });
      } else {
        req.flash('error', "An error ocurred while logging you in. Please try again later.");
        res.redirect('/');
      }
    });
  });

  /**
    If the user is logged in, log them out and return to the front page.
  */
  app.get('/players/logout', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    markSessionAsLoggedOut(req);
    req.flash('info', 'You were successfully logged out.');
    res.redirect('/');
  });

  /**
    A page to inform the user to log in through Steam.
    Mainly for users that try to access routes that require logging in, but are not logged in.
  */
  app.get('/players/login/interstitial', rm.loadUser, rm.andRestrictToNonLoggedInUser, function(req, res){
    res.render('players/interstitial', {title: 'Login Required'});
  });
}

/**
  Helper method to actually mark the user as logged in.
*/
function markSessionAsLoggedIn(req, answer, conf, mongoose, callback) {
  var friendid = answer.claimedIdentifier.match(/(\d+)$/)[1]
    , Player = mongoose.model('Player');

  req.session.friendid = friendid;
  Player.markAsLoggedIn(friendid, callback);
}

/**
  Helper method to logout the user.
*/
function markSessionAsLoggedOut(req) {
  delete req.session.friendid;
}

