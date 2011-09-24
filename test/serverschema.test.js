process.env.NODE_ENV = 'test';
var should = require('should')
  , util = require('util')
  , conf = require('../conf/conf.js')()
  , mongoose = require('mongoose')
  , async = require('async')
  , ServerMeta = null;

mongoose.connect(conf.dataDbUrl);
require('../schemas/ServerSchema')(mongoose, conf);
ServerMeta = mongoose.model('ServerMeta');

module.exports.tests = function() {
  async.series({
    'remove previous data': function(callback) {
      ServerMeta.collection.remove(callback);
    }
    , 'create single server': function(callback) {
      ServerMeta.createSingleServer({
          name: 'test1'
        , slug: 'test1-slug'
        , ip: '1.1.1.1'
        , port: 1000
      }, function(err, server) {
        if(err) callback(err);
        else {
          try {
            should.exist(server);
            server.servers[0].verificationCode.should.match(/^tf2logs:[0-9a-f]{15}$/);
          } catch(failure) {
            callback(failure);
          }

          callback(null);
        }
      });
    }
    , 'create single server - no name': function(callback) {
      ServerMeta.createSingleServer({
        slug: 'test1-slug'
        , ip: '1.1.1.1'
        , port: 1000
      }, function(err, server) {
          try {
            should.exist(err);
          } catch(failure) {
            callback(failure);
          }

          callback(null);
      });
    }
    , 'create single server - no slug': function(callback) {
      ServerMeta.createSingleServer({
          name: 'test1'
        , ip: '1.1.1.1'
        , port: 1000
      }, function(err, server) {
          try {
            should.exist(err);
          } catch(failure) {
            callback(failure);
          }

          callback(null);
      });
    }
    , 'create single server - no ip': function(callback) {
      ServerMeta.createSingleServer({
          name: 'test1'
        , slug: 'test1-slug'
        , port: 1000
      }, function(err, server) {
          try {
            should.exist(err);
          } catch(failure) {
            callback(failure);
          }

          callback(null);
      });
    }
    , 'create single server - no port': function(callback) {
      ServerMeta.createSingleServer({
          name: 'test1'
        , slug: 'test1-slug'
        , ip: '1.1.1.1'
      }, function(err, server) {
          try {
            should.exist(err);
          } catch(failure) {
            callback(failure);
          }

          callback(null);
      });
    }
  }
  , function(err, results) {
    mongoose.disconnect();
    if(err) throw err;
  });
};