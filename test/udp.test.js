process.env.NODE_ENV = 'test';
var should = require('should')
  , util = require('util');

module.exports = {
  'message received with no client, and has server - client created, taking messages': function() {
    var udp = require('../udp.js')
      , Client = udp.Client
      , ip = '255.255.255.255'
      , port = 1000
      , clientKey = ip + ":" + port
      , now = Date.now();
    
    mockMongooseCallReturnServerObj(Client)

    udp.onMessage({received: now, logLine: "asdf"}, ip, port);

    var client = udp.clients[clientKey];
    client.should.be.ok;
    client._parser.should.be.ok;
    client._queuedLines.length.should.be.eql(1);
  }
  , 'message received with no client, and has no server - client created, not taking messages': function() {
    var udp = require('../udp.js')
      , Client = udp.Client
      , ip = '255.255.255.255'
      , port = 2000
      , clientKey = ip + ":" + port
      , now = Date.now();
    
    mockMongooseCallReturnNullServerObj(Client)

    udp.onMessage({received: now, logLine: "asdf"}, ip, port);

    var client = udp.clients[clientKey];
    client.should.be.ok;
    should.not.exist(client._parser);
    client._queuedLines.length.should.be.eql(0);
  }
  , 'message received with client, and has server - should have queued messages': function() {
    var udp = require('../udp.js')
      , Client = udp.Client
      , ip = '255.255.255.255'
      , port = 3000
      , clientKey = ip + ":" + port
      , now = Date.now();
    
    mockMongooseCallReturnServerObj(Client)

    udp.onMessage({received: now, logLine: "asdf"}, ip, port); //creates the client
    udp.onMessage({received: now+1, logLine: "asdf"}, ip, port); //now client is created, we can verify that this is queued

    var client = udp.clients[clientKey];
    client._queuedLines.length.should.be.eql(2);
  }
  , 'message received with client, and has no server - should have no queued messages': function() {
    var udp = require('../udp.js')
      , Client = udp.Client
      , ip = '255.255.255.255'
      , port = 4000
      , clientKey = ip + ":" + port
      , now = Date.now();
    
    mockMongooseCallReturnNullServerObj(Client)

    udp.onMessage({received: now, logLine: "asdf"}, ip, port); //creates the client
    udp.onMessage({received: now+1, logLine: "asdf"}, ip, port); //now client is created, we can verify that it is not queued

    var client = udp.clients[clientKey];
    client._queuedLines.length.should.be.eql(0);
  }
}

function mockMongooseCallReturnServerObj(Client) {
  //normally this calls mongoose to get the server, we just want to return a mock object.
  Client.prototype._getServerInfo = function() {
    util.log('mocked _getServerInfo for (returning server obj): '+this.getIpAndPort());
    this._onGetServerInfo(null, {});
  };
}

function mockMongooseCallReturnNullServerObj(Client) {
  //normally this calls mongoose to get the server, we just want to return a mock object.
  Client.prototype._getServerInfo = function() {
    util.log('mocked _getServerInfo for (returning null server obj): '+this.getIpAndPort());
    this._onGetServerInfo(null, null);
  };
}