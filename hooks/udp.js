var Hook = require('hook.io').Hook
	util = require('util');

var Udp = exports.Udp = function (options) {

  var self = this;
  Hook.call(this, options);
  
  this.on('hook::ready', function () {
    require('../udp').start();
  });
};

util.inherits(Udp, Hook);

var udp = new Udp({name:'udp'});
udp.start();