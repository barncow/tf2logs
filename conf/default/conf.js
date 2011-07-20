/**
  Application configuration. Defaults should go to the 'def' namespace. Environment specific variables should be in 'env' - these will override defaults.
*/

var _ = require('underscore');

/**
  Retrieves the configuration for the given environment.
  @param environment to run in. If not specified, it will use NODE_ENV, if that is not specified, it will use 'test'
*/
module.exports = function(env) {
  env = env || process.env.NODE_ENV || 'development';
  ret = {};
  _.extend(ret, conf.def, conf.env[env]);
  return ret;
}

var conf = {
  'env': {
    'development': {
        port: 3001 //server port
      , sessionDbUrl: 'mongodb://localhost/tf2logs_dev/sessions' //database location for session information
      , dataDbUrl: 'mongodb://localhost/tf2logs_dev' //base database location
    },

    'test': {
        port: 2999 //server port
      , sessionDbUrl: 'mongodb://localhost/tf2logs_test/sessions' //database location for session information
      , dataDbUrl: 'mongodb://localhost/tf2logs_test' //base database location
      , baseUrl: 'http://localhost' //domain where the app sits - running tests locally
    },

    'qa': {
        port: 3002 //server port
      , sessionDbUrl: 'mongodb://localhost/tf2logs_qa/sessions' //database location for session information
      , dataDbUrl: 'mongodb://localhost/tf2logs_qa' //base database location
    },

    'production': {
        port: 3000 //server port
      , sessionDbUrl: 'mongodb://localhost/tf2logs/sessions' //database location for session information
      , dataDbUrl: 'mongodb://localhost/tf2logs' //base database location
    }
  },
  'def': {
      sessionSecret: 'secret' //replace with a string with random characters. This is used to hash the cookie.
    , sessionCookieKey: 'blah.sid' //this is what the cookie is named
    , baseUrl: 'http://www.mysite.com' //domain where the app sits
    , steamOpenIdProviderUrl: 'http://steamcommunity.com/openid/' //what Valve's Steam OpenID Provider URL is
    , steamWebAPIKey: 'XXXXXXXXXXXXXXXXXXXXXXXX' //key to access the steam web API
  }
}

