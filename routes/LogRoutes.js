/**
  Routes for uploading, editing, and viewing log data.
*/

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose);

  /**
    If the user is logged in, allow them to get the form to upload a log.
  */
  app.get('/logs/upload', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    res.render('logs/upload', {title: 'Upload a Log'});
  });

  /**
    If the user is logged in, allow them to do the actual upload
    Using mustBeLoggedIn to only check the session, not call mongodb which would block the upload.
  */
  app.post('/logs/upload', rm.mustBeLoggedIn, function(req, res){
    req.form.complete(function(err, fields, files){
      if (err) {
        require('util').log(err);
        req.flash('error', 'An error ocurred while uploading. Please try again later.');
        res.redirect('/');
      } else {
        console.log('\nuploaded %s to %s'
          ,  files.logfile.filename
          , files.logfile.path);
        req.flash('info', 'Log Uploaded');
        res.redirect('/logs/upload');
      }
    });
  });
}

