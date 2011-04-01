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
    pool = new MySQLPool({database: DB_DATABASE}),
    numQueries = 0,
    noop = function(err){if(err)throw err;};

  pool.properties.user = DB_USER;
  pool.properties.password = DB_PASS;

  //this will always keep this many connections open. 
  //There is no ability to set a min and max and have the connections float.
  pool.connect(DB_CONNECTIONS);
  
  //public methods
  /**
    performs query on the database.
    queryCallback is optional. if not specified, a callback that throws any error that is returned is used.
  */
  this.query = function(queryString, queryParams, queryCallback) {
    if(!queryCallback) queryCallback = noop;
    ++numQueries;
    pool.query(queryString, queryParams, function(err){
      --numQueries;
      queryCallback(err);
    });
  };
  
  /**
    shuts down the pool and closes all connections.
  */
  this.close = function() {
    if(numQueries <= 0) {
      pool.end(noop);
    } else {
      //all queries have not completed - we will wait
      setTimeout(this.close, 200);
    }
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
  /**
    Gets the timestamp out of the logLine.
    If the logLine is corrupt, this function will return the value false.
  */
  this.getTimestamp = function(logLine) {
    var matches = logLine.match(/^L (\d\d)\/(\d\d)\/(\d\d\d\d) - (\d\d):(\d\d):(\d\d)/);
    if(!matches || matches.length == 0) return false; //corrupt line
    return {
      month: parseInt(matches[1],10),
      day: parseInt(matches[2],10),
      year: parseInt(matches[3],10),
      hour: parseInt(matches[4],10),
      minute: parseInt(matches[5],10),
      second: parseInt(matches[6],10)
    };
  };
  
  /**
    Gets the log line details, which is the string of chars after the timestamp.
    If there is nothing after the timestamp, this will return the value false.
  */
  this.getLogLineDetails = function(logLine) {
    var START_INDEX = 25;
    if(logLine.length <= START_INDEX) return false; //corrupt line
    return logLine.substring(START_INDEX, logLine.length);
  }
  
  return this;
}

/**
  The purpose of this object is to handle setting up the UDP connection, parsing the log lines that come through, and saving to the database.
  This is the main object to be used.
*/
exports.LogUDPServer = function(SERVER_PORT, dbDriver) {
  //public methods (being defined first since init needs these references to exist
  
  /**
    starts listening for UDP packets. The node process will "hang" after this point,
    so make sure any initialization work is done before calling!
  */
  this.start = function() {
    //initializing server here because even if not bound, it will be listening for events (and prevent node from closing naturally)
    server = udp.createSocket("udp4");
    
    //set up udp server event handlers
    server.on("message", this._onMessage);
    
    server.on("listening", function() {
      var address = server.address();
      console.log("server listening " + address.address + ":" + address.port);
      udpStatus = 1; //set to running
    });
    
    server.on("close", function() {
      udpStatus = 0; //set to not running;
    });
    
    //provide some cleanup upon exit
    process.on('exit', function(){this.stop();});
    
    server.bind(SERVER_PORT);
    
    return this; //for chaining
  };
  
  /**
    stops listening for packets, and closes the db connection pool.
  */
  this.stop = function() {
    if(this.isRunning()) server.close();
    dao.dbDriver.close();
    
    return this; //for chaining
  };
  
  /**
    Specifies if UDP listening is active or not.
  */
  this.isRunning = function() {
    return udpStatus == 1;
  }
  
  /**
    This is the onMessage handler, which gets called whenever a UDP message is received.
    This should not be used outside this object - only provided for ease of testing.
  */
  this._onMessage = function(msg, rinfo) {
    //convert message to string, stripping uneeded chars.
    var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
    
    var ts = parsingUtils.getTimestamp(logLine);
    if(!ts) return this.STATUS_INVALID; //timestamp is corrupt, no need to continue
    
    var logLineDetails = parsingUtils.getLogLineDetails(logLine);
    if(!logLineDetails) return this.STATUS_INVALID; //logLineDetails are corrupt, no need to continue
    
    //insert the line into the log_line table.
    dao.insertLogLine(ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second, rinfo.address, rinfo.port, logLine);
    
    return this.STATUS_SUCCESS; //still here, must be success.
  }
  
  //initialization - done last because init needs the function references above.
  var 
    dao = new exports.LogDAO(dbDriver),
    udp = require('dgram'),
    server, //init done in .start() method.
    parsingUtils = new exports.ParsingUtils(),
    START_INDEX = 5, //where the udp message should start - garbage? data before this point.
    END_DECREMENT = 2, //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
    udpStatus = 0; //0 is not running, 1 is running
  
  this.STATUS_INVALID = -1;
  this.STATUS_SUCCESS = 1;
  
  return this;
}
