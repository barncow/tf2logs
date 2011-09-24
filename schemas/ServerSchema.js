/**
  Represents a single server, or a group of servers.
*/
var crypto = require('crypto');

//todo indexes

var createVerificationCode = function(salt) {
  var algo = crypto.createHash('sha1');
  algo.update(Date.now()+salt);
  var sha1 = algo.digest('hex');
  return 'tf2logs:'+sha1.substring(sha1.length-15);
};

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
    , map: {type: String}
    , verificationCode: {type: String}
    //todo additional info? like map name? current players?
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
    //, owner: PlayerSchema //todo see DBRef
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
    server.verificationCode = createVerificationCode(obj.slug+obj.ip+obj.port);
    serverMeta.servers = [server];

    serverMeta.save(function(err){
      if(err) callback(err);
      else callback(null, serverMeta);
    });
  });

  ServerMetaSchema.static('getServerForIPAndPort', function(ip, port, callback) {
    this.findOne({'servers.ip': ip, 'servers.port': port, 'servers.active': 'A'}, callback);
  });

  ServerMetaSchema.methods.getServer = function(ip, port) {
    var servers = this.get('servers');
    for(var i = 0; i < servers.length; ++i) {
      var s = servers[i];
      if(s.get('ip') == ip && s.get('port') == port) return s;
    }

    //still here, did not find what we wanted.
    return null;
  }

  ServerMetaSchema.methods.verifyServer = function(ip, port, verifyCode, callback) {
    var server = this.getServer(ip, port)
      , self = this;

    if(server && server.get('verificationCode') && server.get('verificationCode') == verifyCode) {
      server.set('verificationCode', null);
      self.save(function(err) {
        if(err) callback(err);
        else callback(null, self);
      });
      return true;
    }
  };
  
  mongoose.model('ServerMeta', ServerMetaSchema);
  mongoose.model('Server', ServerSchema);
};


