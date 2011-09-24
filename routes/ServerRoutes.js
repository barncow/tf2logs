/**
  Routes for uploading, editing, and viewing log data.
*/

var Validate = require('../lib/validate');

module.exports = function(app, conf, mongoose, WebHook) {
  var rm = require('../lib/routemiddleware').init(app, conf, mongoose)
      , rf = require('../lib/routefunctions')
      , util = require('util')
      , NEW_SERVER_TITLE = 'Add a Server or Server Group';

  /**
    If the user is logged in, allow them to get the form to create a new server
  */
  app.get('/servers/new', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    res.render('servers/new', {title: NEW_SERVER_TITLE, errors: null, params: {}});
  });

  /**
    If the user is logged in, allow them to create a new server
  */
  app.post('/servers/new', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    //perform validation on form data.
    var errors = validateNewServer(req);
    if(errors.length > 0) {
      //we have errors, let's go no further.
      res.render('servers/new', {title: NEW_SERVER_TITLE, errors: errors, params: req.body});
      return;
    }

    //still here - validation was successful. Try to save the data.
    mongoose.model('ServerMeta').createSingleServer(req.body, function(err, serverMeta) {
      if(err) {
        //since we are doing validation outside of Mongoose, if we get an error its because of an unrecoverable condition.
        util.log('Error ocurred when saving new server');
        util.log(util.inspect(err, false, null));

        res.render('servers/new', {
            title: NEW_SERVER_TITLE
          , errors: ['An unexpected error ocurred when saving your information. We have been notified and will try to fix the issue as soon as possible.']
          , params: req.body
        });
      } 
      else {
        outputServerChangeEvent(req.body.ip, req.body.port);
        res.redirect('/servers/'+serverMeta.slug); //save success, go to new server page
      }
    });
  });

  /**
    Show main server page.
  */
  app.get('/servers/:groupSlug/:serverSlug?', rm.loadUser, function(req, res){
    res.end('server page'); //todo elaborate
  });

  function outputServerChangeEvent(serverIP, serverPort) {
    WebHook.emit('serverChange', {ip: serverIP, port: serverPort});
  }
};

/**
  Helper function to validate new server information
*/
function validateNewServer(req) {
  var v = new Validate()
    .addMessage(function() {req.check('name', 'Name must be between 3-50 characters').len(3, 50);})
    .addMessage(function() {
      req.check('slug', 'URL can only consist of letters, numbers, underscores (_), and dashes (-), and must start and end with a letter or a number, and can only be 3-50 characters')
        .len(3, 50).regex(/^[a-zA-Z0-9][a-zA-Z0-9_\-]*[a-zA-Z0-9]$/); //todo test for uniqueness - will require doing a callback
    })
    .addMessage(function() {req.check('ip', 'IP must be a valid IP address.').isIP();}) //todo test that IP/port combination is unique among active servers
    .addMessage(function() {req.check('port', 'Port must be a number and must be 5 or less characters').isInt().len(1, 5);});
  return v.errors;
}

