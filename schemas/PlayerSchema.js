/**
  Represents a player, and a user of the system
*/
var steam = require('../lib/steamwebapi');

/**
  called by app.js to initialize our schemas as models.
  @param mongoose - standard mongoose lib object
*/
module.exports = function(mongoose, conf) {
  //defining some shortcuts
  var Schema = mongoose.Schema;

  /**
    Represents a player, and a user of the system
  */
  var PlayerSchema = new Schema({
      friendid: String
    , name: {type: String, default: ''}
    , appRole: {type: String, enum: ['owner', 'user'], default: 'user'}
    , lastLogin: {type: Date, default: null}
  });

  /**
    Retrieves a Player by its friendid.
    @param friendid string of friendid of player to retrieve
    @param callback function to call after data is retrieved.
  */
  PlayerSchema.static('findByFriendId', function(friendid, callback) {
    this.findOne({friendid: friendid}, callback);
  });

  /**
    Inserts/Updates a Player, and updates the lastLogin timestamp.
    @param friendid string of friendid of player to retrieve
    @param callback function to call after data is updated.
  */
  PlayerSchema.static('markAsLoggedIn', function(friendid, callback) {
    this.findByFriendId(friendid, function(err, player){
      if(!player) {
        player = new this(); //create a new Player instance from a Schema instance (this)
        player.friendid = friendid;
      }

      steam.getPlayerName(conf, friendid, function(err, playerName){
        if(err) console.log(err);
        else player.name = playerName;

        player.lastLogin = Date.now();
        player.save(callback);
      });
    });
  });

  mongoose.model('Player', PlayerSchema);
};

