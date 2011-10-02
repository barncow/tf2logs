//Route for generating compiled templates from Jade for use in single page app.
//todo add minifier?

var jade = require('jade')
  , fs = require('fs')
  , helpers = require('../lib/helpers');

/**
  Add templates here to compile for use on client.
  It is an array of objects, with keys:
  name -> name to reference in window.tf2logs.templates namespace.
  file -> location of template, relative to views directory.
*/
var templates = [
        {name: "index", file: 'index.jade'}
      , {name: "log_show", file: 'logs/show.jade'}
      , {name: "log_upload", file: 'logs/upload.jade'}
    ];

module.exports = function(app, conf, mongoose) {
  app.get('/templates.js', function(req, res){
    var numTemplates = templates.length
      , numCompiled = 0
      , viewsDir = app.set("views");

    res.setHeader("Content-Type", "text/javascript");

    var js = "(function(){var tmpl=window.tf2logs.templates;";

    //make helpers available to the templates.
    Object.keys(helpers.helpers).forEach(function(method) {
      js += "var "+method+"="+helpers.helpers[method].toString()+";";
    });


    templates.forEach(function(tmpl) {
      var filename = viewsDir+"/"+tmpl.file;
      fs.readFile(filename, function (err, data) {
        if(err) throw err;
        js += "tmpl."+tmpl.name+"="+jade.compile(data, {filename: filename, compileDebug: false}).toString()+";";
        ++numCompiled;

        if(numCompiled === templates.length) {
          js += "})();";
          res.end(js);
        }
      });
    });
  });
}

