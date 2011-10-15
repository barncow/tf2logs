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
      , {name: "logfile_queue_view", file: 'logs/upload/logFileQueueView.jade'}
      , {name: "logfile_meta_view", file: 'logs/upload/logFileMetaView.jade'}
      , {name: "log_uploader_view", file: 'logs/upload/logUploaderView.jade'}
    ];

//cache our compiled templates
var js = "";

module.exports = function(app, conf, mongoose) {
  js = compile(app.set("views"));

  app.get('/templates.js', function(req, res){
    res.setHeader("Content-Type", "text/javascript");
    res.end(js);
  });
}

function compile(viewsDir) {
  var numTemplates = templates.length
      , numCompiled = 0;

  var js = "(function(){var tmpl=window.tf2logs.templates;";
  //make helpers available to the templates.
  Object.keys(helpers.helpers).forEach(function(method) {
    js += "var "+method+"="+helpers.helpers[method].toString()+";";
  });

  templates.forEach(function(tmpl) {
    var filename = viewsDir+"/"+tmpl.file;
    var data = fs.readFileSync(filename, "utf8");
    var fn = jade.compile(data, {filename: filename, compileDebug: false, client: true});
    js += "tmpl."+tmpl.name+"="+fn.toString()+";";
    ++numCompiled;

    if(numCompiled === templates.length) {
      js += "})();";
    }
  });
  return js;
}