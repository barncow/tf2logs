var zombie = require('zombie')
  , should = require('should')
    conf = require('../conf/conf.js')('test')
    baseURL = conf.baseUrl+":"+conf.port;

module.exports = {
  'can retrieve html from developement environment': function() {
    zombie.visit(baseURL, function (err, browser, status) {
      should.not.exist(err);
      browser.html().should.be.ok;
    });
  }
}

