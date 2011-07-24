/**
  Routes for uploading, editing, and viewing log data.
*/

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose)
      , TF2LogParser = require('tf2logparser').TF2LogParser
      ,util = require('util');

  /**
    If the user is logged in, allow them to get the form to upload a log.
  */
  app.get('/logs/upload', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    res.render('logs/upload', {title: 'Upload a Log'});
  });

  /**
    If the user is logged in, allow them to do the actual upload
    Using mustBeLoggedIn to only check the session, not call mongodb which would block the upload.
    TODO what is the memory usage for generating a log object, then passing it to save, then retrieving it via a callback?
  */
  app.post('/logs/upload', rm.mustBeLoggedIn, function(req, res){
    req.form.complete(function(err, fields, files){
      if (err) {
        util.log(err);
        req.flash('error', 'An error ocurred while uploading. Please try again later.');
        res.redirect('/');
      } else {
        var parser = TF2LogParser.create();//todo verify that got file, and that log metadata is valid
        parser.parseLogFile(files.logfile.path, function(err, log) {
          if(err) {
            util.log(err);
            req.flash('error', 'An error ocurred while processing your log. Please try again later.');
            res.redirect('/');
          }

          var logModel = mongoose.model('Log');
          logModel.createLog(log, files.logfile.filename, function(err, savedLog){
            if(err) {
              util.log(err);
              req.flash('error', 'An error ocurred while saving your log. Please try again later.');
              res.redirect('/');
            } else {
              req.flash('info', 'Log Uploaded - id: '+savedLog._id);
              res.redirect('/logs/upload');
            }
          });
        });
      }
    });
  });
}

