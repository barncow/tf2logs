/**
  Routes for uploading, editing, and viewing log data.
*/

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose)
      , rf = require('../lib/routefunctions.js')
      , tf2lib = require('tf2logparser')
      , TF2LogParser = tf2lib.TF2LogParser
      , View = tf2lib.View
      , util = require('util');

  /**
    If the user is logged in, allow them to get the form to upload a log.
  */
  app.get('/logs/upload', rm.loadUser, rm.andRestrictToLoggedInUser, function(req, res){
    res.render('logs/upload', {title: 'Upload a Log'});
  });

  /**
    If the user is logged in, allow them to do the actual upload
    Using mustBeLoggedIn to only check the session, not call mongodb which would block the upload. DO NOT USE rm.loadUser!!!
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

  /**
    The main route to display a log. This should be toward the bottom to prevent :id from being caught earlier.
    This should be accessible regardless of login status.
  */
  app.get('/logs/:id', rm.loadUser, function(req, res) {
    mongoose.model('Log').findById(req.params.id, {}, [], function(err, log) {
      if(err) { //todo what happens when not found
        util.log(err);
        rf.getReqHandler(req, res)({error: 'An error ocurred while retrieving the log. Please try again later.'}, '/');
      } else {
        res.render('logs/show', {
            title: log.name
          , playerStats: View.playerStats(log.log.players, log.log.playableSeconds)
          , medicSpread: View.medicSpread(log.log.players, log.log.playableSeconds)
          , healSpread: View.healSpread(log.log.players)
          , weaponSpread: View.weaponSpread(log.log.players, log.log.weapons)
          , playerSpread: View.playerSpread(log.log.players)
          , itemSpread: View.itemSpread(log.log.players)
          , chatLog: View.chatLog(log.log.events)
        });
      }
    });
  });
}

