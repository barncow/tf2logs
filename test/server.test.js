var zombie = require('zombie'), should = require('should');

module.exports = {
  'can retrieve html from developement environment': function() {
    zombie.visit("http://localhost:3001", function (err, browser, status) {
      should.not.exist(err);
      browser.html().should.be.ok;
    });
  }
}

