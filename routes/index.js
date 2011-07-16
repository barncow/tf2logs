//Default index route.

module.exports = function(app, config) {
  app.get('/', function(req, res){
    req.session.count = req.session.count || 0;
    ++req.session.count;

    res.render('index', {
      title: 'Express +'+req.session.count+': '+req.session.friendid
    });
  });
}

