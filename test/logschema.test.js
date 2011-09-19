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

            stats.roleSpread.should.eql({
              "scout" : {
                  "secondsPlayed" : 5445
                , "key" : "scout"
              }
              , "pyro" : {
                  "secondsPlayed" : 8
                , "key" : "pyro"
              }
            });

            stats.itemSpread.should.eql({
                "ammopack_small" : 4
              , "ammopack_medium" : 8
              , "medkit_medium" : 23
              , "tf_ammo_pack" : 51
              , "medkit_small" : 14
            });

            stats.healSpread.should.eql({});

            stats.weaponSpread.should.eql({
              "maxgun" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "maxgun"
              },
              "sniperrifle_hs" : {
                "deaths" : 2,
                "kills" : 0,
                "key" : "sniperrifle_hs"
              },
              "minigun" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "minigun"
              },
              "tf_projectile_pipe" : {
                "deaths" : 3,
                "kills" : 0,
                "key" : "tf_projectile_pipe"
              },
              "sniperrifle" : {
                "deaths" : 2,
                "kills" : 0,
                "key" : "sniperrifle"
              },
              "obj_sentrygun2" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "obj_sentrygun2"
              },
              "degreaser" : {
                "deaths" : 4,
                "kills" : 0,
                "key" : "degreaser"
              },
              "world" : {
                "deaths" : 1,
                "kills" : 1,
                "key" : "world"
              },
              "knife_bs" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "knife_bs"
              },
              "paintrain" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "paintrain"
              },
              "tf_projectile_pipe_remote" : {
                "deaths" : 7,
                "kills" : 0,
                "key" : "tf_projectile_pipe_remote"
              },
              "iron_curtain" : {
                "deaths" : 3,
                "kills" : 0,
                "key" : "iron_curtain"
              },
              "shotgun_primary" : {
                "deaths" : 1,
                "kills" : 0,
                "key" : "shotgun_primary"
              },
              "tf_projectile_rocket" : {
                "deaths" : 12,
                "kills" : 0,
                "key" : "tf_projectile_rocket"
              },
              "force_a_nature" : {
                "deaths" : 0,
                "kills" : 34,
                "key" : "force_a_nature"
              },
              "scattergun" : {
                "deaths" : 9,
                "kills" : 2,
                "key" : "scattergun"
              }
            });

            stats.playerSpread.should.eql({
              "STEAM_0:1:8656857" : {
                "deaths" : 0,
                "kills" : 3,
                "steamid" : "STEAM_0:1:8656857",
              },
              "STEAM_0:0:521077" : {
                "deaths" : 3,
                "kills" : 2,
                "steamid" : "STEAM_0:0:521077",
              },
              "STEAM_0:1:15466986" : {
                "deaths" : 4,
                "kills" : 4,
                "steamid" : "STEAM_0:1:15466986",
              },
              "STEAM_0:1:12152866" : {
                "deaths" : 5,
                "kills" : 3,
                "steamid" : "STEAM_0:1:12152866",
              },
              "STEAM_0:1:17186868" : {
                "deaths" : 0,
                "kills" : 2,
                "steamid" : "STEAM_0:1:17186868",
              },
              "STEAM_0:0:16250003" : {
                "deaths" : 2,
                "kills" : 4,
                "steamid" : "STEAM_0:0:16250003",
              },
              "STEAM_0:0:206754" : {
                "deaths" : 12,
                "kills" : 3,
                "steamid" : "STEAM_0:0:206754",
              },
              "STEAM_0:0:14295714" : {
                "deaths" : 12,
                "kills" : 6,
                "steamid" : "STEAM_0:0:14295714",
              },
              "STEAM_0:0:1300065" : {
                "deaths" : 4,
                "kills" : 3,
                "steamid" : "STEAM_0:0:1300065",
              },
              "STEAM_0:0:13365050" : {
                "deaths" : 6,
                "kills" : 5,
                "steamid" : "STEAM_0:0:13365050",
              },
              "STEAM_0:1:9852193" : {
                "deaths" : 1,
                "kills" : 1,
                "steamid" : "STEAM_0:1:9852193",
              },
              "STEAM_0:1:16481274" : {
                "deaths" : 0,
                "kills" : 1,
                "steamid" : "STEAM_0:1:16481274",
              }
            });

            callback();
          } catch (err) {
            callback(err);
          }
        });
      });
    }
    , 'testing healspread': function(callback) {
      //getting player stats for barncow
      logModel.getPlayerStatsByFriendId('76561197993228277', function(err, stats) {
        try {
          should.not.exist(err);
          stats.should.be.ok;

          stats.healSpread.should.eql({
            "STEAM_0:1:17557682" : {
              "healing" : 2820,
              "steamid" : "STEAM_0:1:17557682"
            },
            "STEAM_0:0:1939017" : {
              "healing" : 942,
              "steamid" : "STEAM_0:0:1939017"
            },
            "STEAM_0:0:8581157" : {
              "healing" : 4747,
              "steamid" : "STEAM_0:0:8581157"
            },
            "STEAM_0:1:10977141" : {
              "healing" : 586,
              "steamid" : "STEAM_0:1:10977141"
            },
            "STEAM_0:1:16208935" : {
              "healing" : 317,
              "steamid" : "STEAM_0:1:16208935"
            },
            "STEAM_0:0:6845279" : {
              "healing" : 880,
              "steamid" : "STEAM_0:0:6845279"
            },
            "STEAM_0:0:946908" : {
              "healing" : 5167,
              "steamid" : "STEAM_0:0:946908"
            },
            "STEAM_0:0:20079783" : {
              "healing" : 2719,
              "steamid" : "STEAM_0:0:20079783"
            }
          });

          callback();
        } catch (err) {
          callback(err);
        }
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