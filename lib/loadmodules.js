var _ = require('underscore');
/**
  Convenience function to batch load a bunch of modules from a directory, and call the function with supplied arguments.
  This only works for modules that are in the form of "module.exports = function(arg1){doStuff;}"
  After specifying the arguments that are required, you may optionally pass additional arguments that should be given to the module.
  @param baseDir - baseDirectory directory to load modules from (ie. "/routes")
  @param fileRegEx - regular expression that matches the files to include (ie. /\.js$/ to grab any file that ends with ".js")
*/
module.exports = function (baseDir, fileRegEx) {
  //set our base directory for routes
  var baseDirectory = __dirname + '/../' + baseDir;

  //read the contents. Using sync here because we need to get results before moving on with server setup.
  var files = require('fs').readdirSync(baseDirectory);
  for(var i in files) {
    //if the file matches the regex, then load it. require will return a function.
    //use the extra arguments given to this function to call the function
    if(files[i].match(fileRegEx)) require(baseDirectory + '/' + files[i]).apply(this, _.toArray(arguments).slice(2));
  }
}