/**
UDP Server - based on nodejs
*/

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
    pool.query(queryString, queryParams, function(err, results, fields){
      --numQueries;
      queryCallback(err, results, fields);
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
  
  //these taken from Server model in main code base.
  this.SERVER_STATUS_ACTIVE = "A";
  this.SERVER_STATUS_INACTIVE = "I";
  this.SERVER_STATUS_RECORDING = "R";
  this.SERVER_STATUS_PROCESSING = "P";
  
  //public methods
  /*
    This will do an insert of parsed data to the database, iff the server is in recording status. The timestamp variables should be pulled from the line to save directly.
    The IP and port are from the sender of the line, and data is the actual complete log line that was sent.
    Callback is optional, and will be called when the save is complete.
  */
  this.insertLogLine = function(year, month, day, hour, minute, second, server_ip, server_port, data, callback) {
    //note - this query gets the server_id from a subselect - based on ip, port, and if it is in recording status. If these three conditions are not met, null is put into a non-null field, which will cause an error.
    //just ignore this error - should be fine.
    dbDriver.query('insert into log_line (line_year, line_month, line_day, line_hour, line_minute, line_second, created_at, server_id, line_data) values(?, ?, ?, ?, ?, ?, current_timestamp, (select id from server where ip = ? and port = ? and status = ?), ?)',
      [year, month, day, hour, minute, second, server_ip, server_port, this.SERVER_STATUS_RECORDING, data], function(err, results, fields){
        if(callback) callback(err, results, fields);
      });
      
    //doing the timestamp update here to ensure that it gets done when the message is entered.
    this.updateLastMessageTimestamp(server_ip, server_port);
  };
  
  /*
    This will update the server record for the given IP, port, and verify_key so that the server is verified (verify_key is null, status is updated).
    If the given IP, port, and verify_key combo does not match anything, nothing is done.
  */
  this.verifyServer = function(server_ip, server_port, verifyKey, callback) {
    dbDriver.query('update server set verify_key = null, status = ? where ip = ? and port = ? and verify_key = ?',
      [this.SERVER_STATUS_ACTIVE, server_ip, server_port, verifyKey], callback);
  };
  
  /**
    This will update the server record for the given server_ip and server_port (if any match) with CURRENT_TIMESTAMP for the last_message column in the server table.
  */
  this.updateLastMessageTimestamp = function(server_ip, server_port, callback) {
    dbDriver.query('update server set last_message = CURRENT_TIMESTAMP where ip = ? and port = ? and status != ?',
      [server_ip, server_port, this.SERVER_STATUS_INACTIVE], callback);
  };
  
  /**
    This will update the server record for the given server_ip and server_port (if any match) with the current map.
  */
  this.updateCurrentMap = function(server_ip, server_port, server_map, callback) {
    dbDriver.query('update server set current_map = ? where ip = ? and port = ? and status != ?',
      [server_map, server_ip, server_port, this.SERVER_STATUS_INACTIVE], callback);
  };
  
  /**
    Updates server status with the given status code.
  */
  this.updateStatus = function(server_ip, server_port, server_status, callback) {
    dbDriver.query('update server set status = ? where ip = ? and port = ? and status != ?',
      [server_status, server_ip, server_port, this.SERVER_STATUS_INACTIVE], callback);
  };
  
  this.getStatus = function(server_ip, server_port, callback) {
    dbDriver.query('select status from server where ip = ? and port = ? and status != ?',
      [server_ip, server_port, this.SERVER_STATUS_INACTIVE], function(err, results, fields){
        status = results[0]['status'];
        callback(status);
      });
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
  };
  
  /**
    Gets the verify key, if one is found. Otherwise the value false is returned.
  */
  this.getVerifyKey = function(logLineDetails) {
    var matches = logLineDetails.match(/^"Console<0><Console><Console>" say ".*(tf2logs:[a-zA-Z0-9]+)/);
    if(!matches || matches.length == 0) return false; //corrupt line
    return matches[1];
  };
  
  /**
    checks that the logLineDetails string given indicates a round start event or not.
  */
  this.isRoundStart = function(logLineDetails) {
    return this.isWorldTriggeredEvent(logLineDetails, "Round_Start");
  };
  
  /**
    returns true if the logLineDetails string represents a world triggered line.
    Event, if specified (optional) will check for the event type.
  */
  this.isWorldTriggeredEvent = function(logLineDetails, event) {
    if(!event) event = ".+?";
    var regex = new RegExp('^World triggered "'+event+'"');
    
    var matches = logLineDetails.match(regex);
    return matches && matches.length > 0;
  }
  
  /**
    returns true if the game has ended - at least for this half.
  */
  this.isGameOver = function(logLineDetails) {
    /*
    conditions for a game over:
    world triggered game_over event
    log file closed
    no messages for a time period?
    game_appears_over from logparser?
    */
    
    return this.isWorldTriggeredEvent(logLineDetails, "Game_Over")
      || logLineDetails == "Log file closed";
  }
  
  this.getMap = function(logLineDetails) {
    var matches = logLineDetails.match(/^Loading map "(.+?)"/);
    if(!matches || matches.length == 0) {
      //no matches, check other version
      matches = logLineDetails.match(/^Started map "(.+?)"/);
      if(!matches || matches.length == 0) {
        return false; //still no matches, therefore not a map line.
      }
    }
    return matches[1];
  };
  
  return this;
}

/**
  The purpose of this object is to handle setting up the UDP connection, parsing the log lines that come through, and saving to the database.
  This is the main object to be used.
*/
exports.LogUDPServer = function(SERVER_PORT, dbDriver, SITE_BASE_DIR, SITE_ENV) {
  //public methods (being defined first since init needs these references to exist
  
  /**
    starts listening for UDP packets. The node process will "hang" after this point,
    so make sure any initialization work is done before calling!
  */
  this.start = function() {
    //initializing server here because even if not bound, it will be listening for events (and prevent node from closing naturally)
    server = udp.createSocket("udp4");
    
    //set up udp server event handlers
    server.on("message",  this._onMessage);
    
    server.on("listening", function() {
      var address = server.address();
      util.log("server listening " + address.address + ":" + address.port);
      udpStatus = 1; //set to running
    });
    
    server.on("close", function() {
      udpStatus = 0; //set to not running;
    });
    
    //provide some cleanup upon exit
    process.on('exit', function(){this.stop();});
    process.on('uncaughtException', function(err) { util.log(err); });
    
    //start listening on this port
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
  };  
  
  /**
    This is the onMessage handler, which gets called whenever a UDP message is received.
    This should not be used outside this object - only provided for ease of testing.
  */
  this._onMessage = function(msg, rinfo) {
    try{
      //convert message to string, stripping uneeded chars.
      var logLine = msg.toString('utf8', START_INDEX, msg.length - END_DECREMENT);
      
      var ts = parsingUtils.getTimestamp(logLine);
      if(!ts) return this.STATUS_INVALID; //timestamp is corrupt, no need to continue
      
      var logLineDetails = parsingUtils.getLogLineDetails(logLine);
      if(!logLineDetails) return this.STATUS_INVALID; //logLineDetails are corrupt, no need to continue
      
      //todo now that we know that we probably have a valid line, should get server fields object here. do updates to object, then save whole object back.////////////////////////////
      //will possibly reduce queries.
      
      //if we get a verify key line, we need to update the server record, if any.
      var verifyKey = parsingUtils.getVerifyKey(logLineDetails);
      if(verifyKey) {
        dao.verifyServer(rinfo.address, rinfo.port, verifyKey);
      }
      
      //if we have a map line, need to update server record, if any.
      var map = parsingUtils.getMap(logLineDetails);
      if(map) {
        dao.updateCurrentMap(rinfo.address, rinfo.port, map);
      }
      
      //if there is a round_start event, update server status to recording, which will allow us to save loglines.
      if(parsingUtils.isRoundStart(logLineDetails)) {
        dao.getStatus(rinfo.address, rinfo.port, function(status){
          if(status != dao.SERVER_STATUS_RECORDING) {
            //this update status must be here in order to start parsing
            dao.updateStatus(rinfo.address, rinfo.port, dao.SERVER_STATUS_RECORDING);
            dao.insertLogLine(ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second, rinfo.address, rinfo.port, logLine); //todo race condition/////////////////////////////////////////////////
            startLineByLine(rinfo.address, rinfo.port);
          }
        });
      }
      
      //insert the line into the log_line table, if server is in recording mode.
      dao.insertLogLine(ts.year, ts.month, ts.day, ts.hour, ts.minute, ts.second, rinfo.address, rinfo.port, logLine);
      
      //this is being done last so that when this updates the status, we will have still saved the logLine for game over.
      if(parsingUtils.isGameOver(logLineDetails)) {
      //todo - should this set a game over flag or something, and wait until team score lines come through? i believe they are "required" and provide better data.//////////////////////////////
        dao.updateStatus(rinfo.address, rinfo.port, dao.SERVER_STATUS_ACTIVE);
      }
      
      return this.STATUS_SUCCESS; //still here, must be success.
    } catch(e) {
        util.log('Error in _onMessage:');
        util.log(e);
    }
  };
  
  //initialization - done last because init needs the function references above.
  var 
    dao = new exports.LogDAO(dbDriver),
    udp = require('dgram'),
    server, //init done in .start() method.
    parsingUtils = new exports.ParsingUtils(),
    START_INDEX = 5, //where the udp message should start - garbage? data before this point.
    END_DECREMENT = 2, //the end of the UDP message -> msg.length - END_DECREMENT (2 gets rid of strange char, plus line break
    udpStatus = 0, //0 is not running, 1 is running
    exec = require('child_process').exec,
    util = require('util'),
    self = this,
    doProcessing = function(ip, port) {
      /**
        this will start a process that will call a php symfony task to collect all the lines that were recorded, and parse them. This is done at the end of the game, not during.
      */
      exec('php '+SITE_BASE_DIR+'/symfony tf2logs:processlines --env='+SITE_ENV+' --ip='+ip+' --port='+port,
        function (error, stdout, stderr) {
          
      })
    },
    startLineByLine = function(ip, port) {
      /**
        this will start a process that will call a php symfony task to collect all the lines that were recorded, and parse them as the log is generated.
      */
      exec('php '+SITE_BASE_DIR+'/symfony tf2logs:linebyline --env='+SITE_ENV+' --ip='+ip+' --port='+port,
        function (error, stdout, stderr) {
          
      });
    };
  
  this.STATUS_INVALID = -1;
  this.STATUS_SUCCESS = 1;
  
  return this;
}
