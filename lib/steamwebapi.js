var steam = require('steam');

/**
  factory function to init the steam api.
*/
var getSteam = function(conf) {
  return new steam({
    apiKey: conf.steamWebAPIKey,
    format: 'json'
  });
}

module.exports.getPlayerName = function(conf, friendid, callback) {
  var friendIdArray, steam = getSteam(conf);
  if(typeof friendid == 'string') friendIdArray = [friendid];
  else friendIdArray = friendid;

  try{
    steam.getPlayerSummaries({steamids: friendIdArray, callback: function(data){
      if(data.response.players.length == 1) callback(null, data.response.players[0].personaname);
      else callback(new Error("Player with friendid '"+friendid+"' could not be found."));
    }});
  } catch(err) {
    callback(err);
  }
}

