var argv = require('optimist').argv,
  cfg = require('./regen_config.js').regenConfig;
  spawn = require('child_process').spawn,
  util = require('util'),
  idsToRegen = [], 
  perProcess = argv.perprocess || 1, //how many logs should be given to single process - min of 1 - code below assumes 1
  numProcesses = argv.numprocesses || 1, //how many processes should be run at once. - min of 1
  calledClearCache = false, //tracking whether we have cleared the cache or not.
  currentNumProcesses = 0, //tracks the current number of open processes.
  canStart = true, //may have to go to db, which needs a callback. don't want to start until that is complete.
  maxId = 0, //when doing all logs, this is the current maxid value.
  lastScheduledId = 0;
  
  //TODO - when SIGINT'ing (Ctrl-C) this will kill children automatically. Need to find a way to let children finish on their own.
  //otherwise, data corruption will occur.

/**
  Function will check that we can spawn more processes, and then go ahead and spawn them.
*/
function checkSpawn(numProcessesToSpawn) {
  if(!canStart) return;
  
  //if there are no more ids to do, 
  //and this is the last process to complete,
  //and we are doing all logs,
  //check, and add new ids if needed.
  if(idsToRegen.length == 0 && currentNumProcesses == 0 && argv.ids == 'all') {
    explodeIds(argv.ids);
  }
  
  //this will generally always be 1. Only during startup with this be > 1.
  numProcessesToSpawn = numProcessesToSpawn || 1;
  
  //each exit of each process calls this function.
  //if there are no more ids to do, 
  //and this is the last process to complete,
  //and clear cache has not been done,
  //do it now.
  if(idsToRegen.length == 0 && currentNumProcesses == 0 && !calledClearCache) {
    clearCache();
  }
  
  for(var i = 0; i < numProcessesToSpawn; ++i) {
    if(idsToRegen.length == 0) {
      break;
    }
    
    //if we are doing multiple logs per process, gather up some IDs to do.
    var ids = [];
    for(var x = 0; x < perProcess && idsToRegen.length > 0; ++x) {
      ids.push(idsToRegen.shift());
    }
    
    spawnRegen(ids);
  }
}

/**
  Calls the symfony task to clear the cache.
*/
function clearCache() {
  calledClearCache = true;
  var child = spawn('php', [cfg.SITE_BASE_DIR+'/symfony', 'cache:clear', '--type=template', '--env='+cfg.SITE_ENV]);
  child.stdout.setEncoding('utf8');
	child.stderr.setEncoding('utf8');

	child.stdout.on('data', function (data) {
		 util.log(trim(data));
	});

	child.stderr.on('data', function (data) {
		util.log('stderr: ' + trim(data));
	});
}

/**
  Function will actually do the spawn child process, and set up some callbacks.
*/
function spawnRegen(logids) {
  var ids = logids.join(',');
  //util.log("Now scheduling: "+ids);
  ++currentNumProcesses;
  lastScheduledId = logids[logids.length-1];
	var child = spawn('php', [cfg.SITE_BASE_DIR+'/symfony', 'tf2logs:regenerate-quiet', '--ids='+ids, '--env='+cfg.SITE_ENV]);
	child.stdout.setEncoding('utf8');
	child.stderr.setEncoding('utf8');

	child.stdout.on('data', function (data) {
		 util.log(trim(data));
	});

	child.stderr.on('data', function (data) {
		util.log('stderr: ' + trim(data));
	});

	child.on('exit', function (code) {
	  --currentNumProcesses;
		checkSpawn();
	});
}

/**
  function will start the processing.
  Note, explodeIds, if 'all' is selected, will halt execution of this method.
  It will call checkSpawn afterwards. If changing this method, change the db callback as well.
*/
function start() {
  explodeIds(argv.ids);
  checkSpawn(numProcesses);
}

function trim(str) {
  return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

/**
  Retrieves the max id from the database.
*/
function getMaxId() {
  util.log("Getting Maxid");
  canStart = false;
  
  var Client = require('mysql').Client,
  client = new Client();
  client.user = cfg.DB_USER;
  client.password = cfg.DB_PASS;
  client.connect();
  client.query('USE '+cfg.DB_DATABASE);
  
  client.query('select max(id) as maxid from log where error_log_name is null', function(err, results) {
    if(err) throw err;
    
    var newmaxid = (results[0]).maxid;
    if(newmaxid != maxId) {
      var start = 1;
      if(idsToRegen.length != 0 && lastScheduledId != 0) start = idsToRegen[idsToRegen.length-1]+1;
      else if(idsToRegen.length == 0 && lastScheduledId != 0) start = lastScheduledId;
      
      for(var i = start; i <= newmaxid; ++i) {
        idsToRegen.push(i);
      }  
      
      util.log("New Maxid: "+newmaxid);
      maxId = newmaxid;
      canStart = true;
      checkSpawn(numProcesses);
    } else util.log("Maxid Unchanged");
  });
  
  client.end();
}

/**
  will parse single int, integer interval, and comma list of both into actual numbers to iterate. 
  If "all", gets max id from mysql, then creates array of integers from 1-maxid. If array is full, go from maxArrayLogId-maxid
*/
function explodeIds(idStr) {
  if(idStr == 'all') {
    getMaxId();
    
  } else if(!isNaN(idStr)){
    //idStr is a number, doing a single log.
    idsToRegen = [idStr];
    
  } else if(trim(idStr) == '') {
    throw new Exception('--ids not specified');
    
  } else {
    idsToRegen = [];
    //have a complex arrangement of ID's.
    
    var params;
    if(idStr.indexOf(',') == -1) {
      params = [idStr];
    } else {
      params = idStr.split(',');
    }
    
    for(var x = 0; x < params.length; ++x) {
      var p = params[x];
      if(!p || p == '') continue;
      
      if(!isNaN(p)) {
        //is a number, push to ids array
        idsToRegen.push(p);
      } else {
        //we have a dash interval (ie. 1-3).
        var interval = p.split('-');
        if(interval.length != 2) {
          throw new Exception("invalid interval specified: "+p);
        }
        
        var start = parseInt(interval[0], 10), end = parseInt(interval[1], 10);
        
        for(var i = start; i <= end; ++i) {
          idsToRegen.push(i);
        }
      }
    }
  }
}

//start the process.
start();
