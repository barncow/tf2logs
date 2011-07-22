module.exports = {
  'complete walkthrough of site': function() {
  process.env.NODE_ENV = 'test';
  var conf = require('../conf/conf.js')()
      , soda = require('soda');

    //init browser obj
    var browser = soda.createClient({
        host: 'localhost'
      , port: 4444
      , url: conf.baseUrl
      , browser: 'firefox'
    });

    browser.on('command', function(cmd, args){
      console.log(' \x1b[33m%s\x1b[0m: %s', cmd, args.join(', '));
    });

    browser.chain.session();

    //run our tests, give each test the browser object.
    for(var t in tests) {
      tests[t](browser);
    }

    //tests complete - close browser
    browser
      .testComplete()
      .end(function(err){
        if (err) throw err;
      });
  }
}

var tests = {
  //BEGIN NOT LOGGED IN TESTING
  'front page when not logged in': function(browser) {
    browser
      .open('/')
      .assertTitle('Not logged in');
  }
}

