/**
  Routes for uploading, editing, and viewing log data.
*/

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose)
      , rf = require('../lib/routefunctions.js')
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
  app.post('/logs/upload.:format', rm.mustBeLoggedIn, function(req, res){
    //specify different handlers for each request type (default is HTML)
    var reqHandler = rf.getReqHandler(req, res);
    
    //when form is uploaded do...
    req.form.complete(function(err, fields, files){
      if (err) {
        util.log(err);
        reqHandler({error: 'An error ocurred while uploading. Please try again later.'}, '/');
      } else {
        var parser = TF2LogParser.create();//todo verify that got file, and that log metadata is valid

        parser.on('done', function(log) {
          var logModel = mongoose.model('Log')
            , meta = {
              logName: fields.logName || files.logfile.filename
              , mapName: fields.mapName, 
            };
          logModel.createLog(log, meta, function(err, savedLog){
            if(err) {
              util.log(err);
              reqHandler({error: 'An error ocurred while saving your log. Please try again later.'}, '/');
            } else {
              reqHandler({info: 'Log Uploaded - id: '+savedLog._id, id:savedLog._id}, '/logs/upload');
            }
          });
        });

        parser.on('error', function(err) {
          util.log(err);
          reqHandler({error: 'An error ocurred while processing your log. Please try again later.'}, '/');
        });

        parser.parseLogFile(files.logfile.path); //todo remove log file from tmp
      }
    });
  });
}

