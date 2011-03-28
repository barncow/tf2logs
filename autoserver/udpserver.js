//keeping this for later 
//var sys = require("sys");
//process.addListener('uncaughtException', function(err) { sys.p(err); });
 
 
 
 
/**
  This object is responsible for creating connections and doing underlying database work.
  Business DB access should be done in the DAO.
*/
exports.DBDriver = function(DB_USER, DB_PASS, DB_DATABASE, DB_CONNECTIONS) {
  //create connection pool.
  var 
    MySQLPool = require("mysql-pool").MySQLPool,
    pool = new MySQLPool({database: DB_DATABASE})
    noop = function(err){if(err)throw err;};

  pool.properties.user = DB_USER;
  pool.properties.password = DB_PASS;

  //this will always keep this many connections open. 
  //There is no ability to set a min and max and have the connections float.
  pool.connect(DB_CONNECTIONS);
  
  //public methods
  this.query = function(queryString, queryParams, queryCallback) {
    if(!queryCallback) queryCallback = noop;
    pool.query(queryString, queryParams, queryCallback);
  };
  
  this.close = function() {
    pool.end(function(err){if(err) throw err});
  };
  
  return this;
}

/**
  The purpose of this object is to provide more business end functions, such as adding LogLines to the database.
  It will use the DBDriver above to do the work.
  Example:
  var dao = new LogDAO(new DBDriver(DB_USER, DB_PASS, DB_DATABASE, DB_CONNECTIONS));
*/
exports.LogDAO = function(dbDriver) {
  this.dbDriver = dbDriver;
  
  //public methods
  /*
    This will do an insert of parsed data to the database. The timestamp variables should be pulled from the line to save directly.
    The IP and port are from the sender of the line, and data is the actual complete log line that was sent.
    Callback is optional, and will be called when the save is complete.
  */
  this.insertLogLine = function(year, month, day, hour, minute, second, server_ip, server_port, data, callback) {
    dbDriver.query('insert into log_line (line_year, line_month, line_day, line_hour, line_minute, line_second, created_at, server_ip, server_port, line_data) values(?, ?, ?, ?, ?, ?, current_timestamp, ?, ?, ?)',
      [year, month, day, hour, minute, second, server_ip, server_port, data], callback);
  };
  
  return this;
}

/**
  Object to provide some log parsing utility functions.
*/
exports.ParsingUtils = function() {
  this.getTimestamp = function(logLine) {
    var matches = logLine.match(/^L (\d\d)\/(\d\d)\/(\d\d\d\d) - (\d\d):(\d\d):(\d\d)/);
    if(!matches || matches.length == 0) return false;
    return {
      month: parseInt(matches[1],10),
      day: parseInt(matches[2],10),
      year: parseInt(matches[3],10),
      hour: parseInt(matches[4],10),
      minute: parseInt(matches[5],10),
      second: parseInt(matches[6],10)
    };
  };
  
  return this;
}

/**
  The purpose of this object is to handle setting up the UDP connection, parsing the log lines that come through, and saving to the database.
*/
exports.LogUDPServer = function(SERVER_PORT, dbDriver) {
  //public methods (being defined first since init needs these references to exist
  
  /**
    starts listening for UDP packets. The node process will "hang" after this point,
    so make sure any initialization work is done before calling!
  */
  this.start = function() {
    server.bind(SERVER_PORT);
    
    return this; //for chaining
  };
  
  /**
    stops listening for packets, and closes the db connection pool.
  */
  this.stop = function() {
    server.close();
    dao.dbDriver.close();
    
    return this; //for chaining
  };
  
  /**
    This is the onMessage handler, which gets called whenever a UDP message is received.
    This should not be used outside this object - only provided for ease of testing.
  */
  this._onMessage = function(msg, rinfo) {
    //convert message to string, stripping uneeded chars.
    var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
    
    var ts = parsingUtils.getTimestamp(logLine);
    if(!ts) return; //timestamp is corrupt, no need to continue
    
    //insert the line into the log_line table.
    dao.insertLogLine(ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second, rinfo.address, rinfo.port, logLine);
  }
  
  var 
    dao = new exports.LogDAO(dbDriver),
    udp = require('dgram'),
    server = udp.createSocket("udp4"),
    parsingUtils = new exports.ParsingUtils(),
    START_INDEX = 5, //where the udp message should start - garbage? data before this point.
    END_DECREMENT = 2; //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
    
  //set up udp server event handlers
  server.on("message", this._onMessage);
  
  server.on("listening", function() {
    var address = server.address();
    console.log("server listening " + address.address + ":" + address.port);
  });
  
  //provide some cleanup upon exit
  process.on('exit', function(){this.stop();});
  
  return this;
}
