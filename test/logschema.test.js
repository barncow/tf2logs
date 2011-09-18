process.env.NODE_ENV = 'test';
var should = require('should')
  , util = require('util')
  , conf = require('../conf/conf.js')()
  , mongoose = require('mongoose')
  , logModel = null
  , TF2LogParser = require('tf2logparser').TF2LogParser
  , async = require('async');

mongoose.connect(conf.dataDbUrl);
require('../schemas/LogSchema')(mongoose, conf);
logModel = mongoose.model('Log');

module.exports.tests = function() {
  async.series({
    'remove previous data': function(callback) {
      logModel.collection.remove(callback);
    }
    , 'get stats for player': function(callback) {
      addLogFiles(['mini.log', 'freight_vs_mixup.log'], function() {
        //getting player stats for target
        logModel.getPlayerStatsByFriendId('76561197973956286', function(err, stats) {
          try {
            should.not.exist(err);
            stats.should.be.ok;

            stats.damage.should.eql(11032);
            stats.kills.should.eql(37);
            stats.deaths.should.eql(49);
            stats.assists.should.eql(18);
            stats.longestKillStreak.should.eql(3);
            stats.longestDeathStreak.should.eql(5);
            stats.headshots.should.eql(0);
            stats.backstabs.should.eql(0);
            stats.pointCaptures.should.eql(6);
            stats.pointCaptureBlocks.should.eql(1);
            stats.flagDefends.should.eql(1);
            stats.flagCaptures.should.eql(1);
            stats.dominations.should.eql(2);
            stats.timesDominated.should.eql(6);
            stats.revenges.should.eql(4);
            stats.extinguishes.should.eql(0);
            stats.ubers.should.eql(0);
            stats.droppedUbers.should.eql(0);
            stats.healing.should.eql(0);
            stats.medPicksTotal.should.eql(3);
            stats.medPicksDroppedUber.should.eql(0);

            callback();
          } catch (err) {
            callback(err);
          }
        });
      });
    }
    , 'cannot get stats for invalid player': function(callback) {
      logModel.getPlayerStatsByFriendId('INVALID', function(err, stats) {
        try {
          should.not.exist(err);
          should.not.exist(stats);

          callback();
        } catch (err) {
          callback(err);
        }
      });
    }
  }
  , function(err, results) {
    mongoose.disconnect();
    if(err) throw err;
  });
};

function addLogFiles(files, cb) {
    var parsers = 0
    , onDone = function(log) {
      var meta = {
          logName: parsers
          , mapName: null, 
        };
      logModel.createLog(log, meta, function(err, savedLog){
        --parsers;
        if(err) throw err;
        if(parsers === 0) cb();
      });
    }
    , onError = function(err) {
      if(err) throw err;
    };

  files.forEach(function(file) {
    var parser = new TF2LogParser();
    parser.on('done', onDone);
    parser.on('error', onError);
    ++parsers;
    parser.parseLogFile('./test/fixtures/'+file);  
  });
}