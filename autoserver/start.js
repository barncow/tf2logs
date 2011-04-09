//retrieve configuration
var cfg = require('./udpserverconfig.js').udpServerConfig;
var lib = require('./udpserver.js');
//start the server
var logUDPServer = new lib.LogUDPServer(cfg.SERVER_PORT, new lib.DBDriver(cfg.DB_USER, cfg.DB_PASS, cfg.DB_DATABASE, cfg.DB_CONNECTIONS), cfg.SITE_BASE_DIR, cfg.SITE_ENV).start();
