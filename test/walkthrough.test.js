/**
  This is the main test suite for testing the site. Using this, we will walkthrough the different functionality
  of the website, and verify that everything is working.
  Note - this will not test more intensive portions of the website, such as uploading and live.
  Doing this as one file instead of spawning multiple windows.

  Tests should be added to the 'tests' variable, and will be run in sequence. Take note of where in the list
  that you are adding your test, a part will be not logged in, and another will be logged in.
*/

var tests = {
  //BEGIN NOT LOGGED IN TESTING
  'front page when not logged in': function(browser, conf) {
    browser
      .open('/')
      .assertTitle('Not logged in');
  }
  , 'certain pages are not accessible when not logged in': function(browser, conf) {
    browser
      .open('/players/logout')
      .assertTextPresent('You must be logged in to perform that action.')
      .open('/logs/upload')
      .assertTextPresent('You must be logged in to perform that action.')
  }
  //END NOT LOGGED IN TESTING
  , 'can log in through Steam': function(browser, conf) {
    browser
      .open('/players/login')
      .assertTitle('Steam Community')
      .type('id=steamAccountName', conf.steamUser)
      .type('id=steamPassword', conf.steamPass)
      .clickAndWait('id=imageLogin')
      .assertTextPresent('You were successfully logged in.')
  }
  //BEGIN LOGGED IN TESTING
  , 'able to view certain pages when logged in': function(browser, conf) {
    browser
      .open('/logs/upload')
      .assertTextPresent('Upload a Log');
  }

  //must be done last
  , 'able to logout': function(browser, conf) {
    browser
      .open('/players/logout')
      .assertTextPresent('You were successfully logged out.');
  }
}

//bootstrap the tests
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
      var jargs = args.join(', ');
      //mask sensitive info
      if(cmd === 'type' && args[0] === 'id=steamAccountName' || args[0] === 'id=steamPassword') jargs = args[0]+", *secret*";
      console.log(' \x1b[33m%s\x1b[0m: %s', cmd, jargs);
    });

    browser.chain.session();

    //run our tests, give each test the browser object.
    for(var t in tests) {
      tests[t](browser, conf);
    }

    //tests complete - close browser
    browser
      .testComplete()
      .end(function(err){
        if (err) throw err;
      });
  }
}

