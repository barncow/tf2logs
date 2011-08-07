/**
  Represents a log file.
*/

/**
  called by app.js to initialize our schemas as models.
  @param mongoose - standard mongoose lib object
*/
module.exports = function(mongoose, conf) {
  //defining some shortcuts
  var Schema = mongoose.Schema;

  var LogSchema = new Schema({
      name: {type: String, required: true}
    , mapName: {type: String}
    , log: {type: mongoose.SchemaTypes.Mixed, required: true} //main log document. Saving raw object instead of embedded doc schema for now.
    , createdAt: {type: Date, default: Date.now}
    , metaUpdatedAt: {type: Date, default: Date.now} //when meta information was updated, like name, tags, etc.
    , regeneratedAt: {type: Date, default: null} //when the log was last regenerated. If never regenerated, use null.
  });

  /**
    Takes a log object and saves it.
    @param log log object retrieved from tf2logparser
    @param meta Object with metadata about the uploaded log.
    @param callback function to call after data is updated. Will have err and the resulting log object passed to callback.
  */
  LogSchema.static('createLog', function(log, meta, callback) {
      var logModel = new (mongoose.model('Log'))();
      logModel.log = log;
      logModel.name = meta.logName;
      logModel.mapName = meta.mapName || log.mapName;
      logModel.save(function(err){
        if(err) callback(err);
        else callback(null, logModel);
      });
  });

  mongoose.model('Log', LogSchema);
};

