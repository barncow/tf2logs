var Hook = require('hook.io').Hook
	util = require('util');

var Web = exports.Web = function (options) {

  var self = this;
  Hook.call(this, options);
  
  this.on('hook::ready', function () {
    var app = require('../../app.js')(this);	
  });
};

util.inherits(Web, Hook);

var web = new Web({name:'web'});
web.start();