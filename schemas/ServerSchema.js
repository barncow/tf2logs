/**
  Represents a single server, or a group of servers.
*/

/**
  called by app.js to initialize our schemas as models.
  @param mongoose - standard mongoose lib object
*/
module.exports = function(mongoose, conf) {
  //defining some shortcuts
  var Schema = mongoose.Schema
    , util = require('util');

  /**
    Holds information about the server itself
  */
  var ServerSchema = new Schema({
      name: {type: String} //only required for groups
    , slug: {type: String} //only required for groups
    , ip: {type: String, required: true}
    , port: {type: Number, required: true}
    , createdAt: {type: Date, default: Date.now}
    , lastLineReceived: {type: Date, default: null}
    , active: {type: String, enum: ['A', 'I'], default: 'A'} //Active or Inactive
    //todo , verificationCode: {type: String, default: createRandomCode} 
        //where createRandomCode generates a random SHA1 key in the form of tf2logs:123456789abcde (15 char key) for the user to RCON in an verify they own the server
    //todo additional info? like map name? current players?
  });

  ServerSchema.static('getServerForIPAndPort', function(ip, port, callback) {
    mongoose.model('Server').findOne({ip: ip, port: port, active: 'A'}, callback);
  });

  /**
    Holds information about whether or not this represents a group or a single server
  */
  var ServerMetaSchema = new Schema({
      type: {type: String, enum: ['S', 'G'], default: 'S'} //S is single server, G is group of servers
    , name: {type: String, required: true}
    , slug: {type: String, required: true} //todo validate format
    , createdAt: {type: Date, default: Date.now}
    , servers: [ServerSchema]
    //, owner: PlayerSchema //see DBRef
  });

  /**
    Creates a single server, using the parameters given
    @param obj - req.body - basically an object with properties matching the ServerMeta object (ie. has name, slug, etc)
    @param callback - function that will be called when server is saved. 
      First parameter is "err", which if truthy has an error, otherwise it will be null. Second parameter is the saved serverMeta object.
  */
  ServerMetaSchema.static('createSingleServer', function(obj, callback) {
    var serverMeta = new (mongoose.model('ServerMeta'))()
      , server = new (mongoose.model('Server'))();

    serverMeta.type = 'S';
    serverMeta.name = obj.name;
    serverMeta.slug = obj.slug;
    server.ip = obj.ip;
    server.port = obj.port;
    serverMeta.servers = [server];

    serverMeta.save(function(err){
      if(err) callback(err);
      else callback(null, serverMeta);
    });
  });
  
  mongoose.model('ServerMeta', ServerMetaSchema);
  mongoose.model('Server', ServerSchema);
};


