//Default index route.

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose)
    , rf = require('../lib/routefunctions.js');

  app.get('/.:format?', rm.loadUser, function(req, res){
    var reqHandler = rf.getReqHandler(req, res);

    mongoose.model('Log').getRecentlyAddedLogs(10, function(err, logs) {
      if(err) {
        util.log(err);
        return reqHandler({title: 'Welcome', recentlyAddedLogs:[], error: 'Could not retrieve data.'}, {render: 'index'});
      }

      return reqHandler({title: 'Welcome', recentlyAddedLogs: logs}, {render: 'index'});
    });
  });
}

