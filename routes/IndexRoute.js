//Default index route.

module.exports = function(app, conf, mongoose) {
  var rm = require('../lib/routemiddleware.js').init(app, conf, mongoose);

  app.get('/', rm.loadUser, function(req, res){
    var playerName = 'Not logged in';
    if(req.user) playerName = req.user.name;

    res.render('index', {
      title: playerName
    });
  });
}

