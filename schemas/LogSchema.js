/**
  Represents a log file.
*/

//todo create indexes where needed

/**
  called by app.js to initialize our schemas as models.
  @param mongoose - standard mongoose lib object
*/
module.exports = function(mongoose, conf) {
  //defining some shortcuts
  var Schema = mongoose.Schema;

  var LogSchema = new Schema({ //todo specify _id field, use a pre-save middleware to generate ID using findAndModify - findAndModify must be used on another schema (LogIDSchema?)
      name: {type: String, required: true}
    , mapName: {type: String}
    , log: {type: mongoose.SchemaTypes.Mixed, required: true} //main log document. Saving raw object instead of embedded doc schema for now.
    , createdAt: {type: Date, default: Date.now}
    , metaUpdatedAt: {type: Date, default: Date.now} //when meta information was updated, like name, tags, etc.
    , regeneratedAt: {type: Date, default: null} //when the log was last regenerated. If never regenerated, use null.
  });

  /**
    Takes a log object and saves it.
    @param log log object retrieved from tf2logparser
    @param meta Object with metadata about the uploaded log.
    @param callback function to call after data is updated. Will have err and the resulting log object passed to callback.
  */
  LogSchema.static('createLog', function(log, meta, callback) {
      var logModel = new (mongoose.model('Log'))(); //todo can we use "this"
      logModel.log = log;
      logModel.name = meta.logName;
      logModel.mapName = meta.mapName || log.mapName;
      logModel.save(function(err){
        if(err) callback(err);
        else callback(null, logModel);
      });
  });

  /**
    Gets a player's stats for all logs (ie. all kills)
    @param friendid player's friendid
    @param callback - called when complete. Parameters given: (err, stats)
  */
  LogSchema.static('getPlayerStatsByFriendId', function(friendid, callback) {
    var mapFunction = (function() {
      this.log.players.forEach(function(p){
        if(p.friendid === friendid) {
          emit(friendid, {
              damage: p.damage
            , kills: p.kills
            , deaths: p.deaths
            , assists: p.assists
            , longestKillStreak: p.longestKillStreak
            , longestDeathStreak: p.longestDeathStreak
            , headshots: p.headshots
            , backstabs: p.backstabs
            , pointCaptures: p.pointCaptures
            , pointCaptureBlocks: p.pointCaptureBlocks
            , flagDefends: p.flagDefends
            , flagCaptures: p.flagCaptures
            , dominations: p.dominations
            , timesDominated: p.timesDominated
            , revenges: p.revenges
            , extinguishes: p.extinguishes
            , ubers: p.ubers
            , droppedUbers: p.droppedUbers
            , healing: p.healing
            , medPicksTotal: p.medPicksTotal
            , medPicksDroppedUber: p.medPicksDroppedUber
            , roleSpread: p.roleSpread || {}
            , itemSpread: p.itemSpread || {}
            , healSpread: p.healSpread || {}
            , weaponSpread: p.weaponSpread || {}
            , playerSpread: p.playerSpread || {}
          });
        }
      });
    }).toString();

    var reduceFunction = (function(k, values) {
      var result = {
        damage: 0
        , kills: 0
        , deaths: 0
        , assists: 0
        , longestKillStreak: 0
        , longestDeathStreak: 0
        , headshots: 0
        , backstabs: 0
        , pointCaptures: 0
        , pointCaptureBlocks: 0
        , flagDefends: 0
        , flagCaptures: 0
        , dominations: 0
        , timesDominated: 0
        , revenges: 0
        , extinguishes: 0
        , ubers: 0
        , droppedUbers: 0
        , healing: 0
        , medPicksTotal: 0
        , medPicksDroppedUber: 0
        , roleSpread: {}
        , itemSpread: {}
        , healSpread: {}
        , weaponSpread: {}
        , playerSpread: {}
      }

      values.forEach(function(v) {
        result.damage += v.damage;
        result.kills += v.kills;
        result.deaths += v.deaths;
        result.assists += v.assists;
        if(v.longestKillStreak > result.longestKillStreak) result.longestKillStreak = v.longestKillStreak;
        if(v.longestDeathStreak > result.longestDeathStreak) result.longestDeathStreak = v.longestDeathStreak;
        result.headshots += v.headshots;
        result.backstabs += v.backstabs;
        result.pointCaptures += v.pointCaptures;
        result.pointCaptureBlocks += v.pointCaptureBlocks;
        result.flagDefends += v.flagDefends;
        result.flagCaptures += v.flagCaptures;
        result.dominations += v.dominations;
        result.timesDominated += v.timesDominated;
        result.revenges += v.revenges;
        result.extinguishes += v.extinguishes;
        result.ubers += v.ubers;
        result.droppedUbers += v.droppedUbers;
        result.healing += v.healing;
        result.medPicksTotal += v.medPicksTotal;
        result.medPicksDroppedUber += v.medPicksDroppedUber;

        for(var roleKey in v.roleSpread) {
          var role = v.roleSpread[roleKey];

          if(typeof result.roleSpread[roleKey] !== 'undefined') {
            result.roleSpread[roleKey].secondsPlayed += role.secondsPlayed;
          } else {
            result.roleSpread[roleKey] = {
                key: role.key
              , secondsPlayed: role.secondsPlayed
            };
          }
        }

        for(var itemKey in v.itemSpread) {
          if(typeof result.itemSpread[itemKey] !== 'undefined') {
            result.itemSpread[itemKey] += v.itemSpread[itemKey];
          } else {
            result.itemSpread[itemKey] = v.itemSpread[itemKey];
          }
        }

        for(var patientKey in v.healSpread) {
          var patient = v.healSpread[patientKey];

          if(typeof result.healSpread[patientKey] !== 'undefined') {
            result.healSpread[patientKey].healing += patient.healing;
          } else {
            result.healSpread[patientKey] = {
                steamid: patientKey
              , healing: patient.healing
            };
          }
        }

        for(var weaponKey in v.weaponSpread) {
          var weapon = v.weaponSpread[weaponKey];

          if(typeof result.weaponSpread[weaponKey] !== 'undefined') {
            result.weaponSpread[weaponKey].kills += weapon.kills;
            result.weaponSpread[weaponKey].deaths += weapon.deaths;
          } else {
            result.weaponSpread[weaponKey] = {
                key: weaponKey
              , kills: weapon.kills
              , deaths: weapon.deaths
            };
          }
        }

        for(var playerKey in v.playerSpread) {
          var player = v.playerSpread[playerKey];

          if(typeof result.playerSpread[playerKey] !== 'undefined') {
            result.playerSpread[playerKey].kills += player.kills;
            result.playerSpread[playerKey].deaths += player.deaths;
          } else {
            result.playerSpread[playerKey] = {
                steamid: playerKey
              , kills: player.kills
              , deaths: player.deaths
            };
          }
        }
      });

      return result;
    }).toString();

    this.collection.mapReduce(mapFunction, reduceFunction, {
        out: {inline: 1}
      , scope: {friendid: friendid}
      , query: {"log.players.friendid": friendid} //only want to process log data for logs with our player
    }, function(err, result) {
      if(err) callback(err);
      else {
        var value = null;
        if(result && result[0] && result[0].value) value = result[0].value;
        callback(null, value);
      }
    });
  });

  mongoose.model('Log', LogSchema);
};

