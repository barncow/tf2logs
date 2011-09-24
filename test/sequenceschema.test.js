process.env.NODE_ENV = 'test';
var should = require('should')
  , util = require('util')
  , conf = require('../conf/conf.js')()
  , mongoose = require('mongoose')
  , async = require('async')
  , Sequence = null;

mongoose.connect(conf.dataDbUrl);
require('../schemas/SequenceSchema')(mongoose, conf);
Sequence = mongoose.model('Sequence');

module.exports.tests = function() {
  async.series({
    'remove previous data': function(callback) {
      Sequence.collection.remove(callback);
    }
    , 'get first sequence value': function(callback) {
      Sequence.getSequence('logid', function(err, sequence) {
        try {
          sequence.should.be.equal(1);
        } catch(failure) {
          callback(failure);
          return;
        }
        callback(null);
      });
    }
    , 'get second sequence value': function(callback) {
      Sequence.getSequence('logid', function(err, sequence) {
        try {
          sequence.should.be.equal(2);
        } catch(failure) {
          callback(failure);
          return;
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