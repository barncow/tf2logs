module.exports = function(mongoose, conf) {
	var Schema = mongoose.Schema
    , util = require('util');

  /**
    Schema for getting ID numbers via findAndModify. Creates better URLs for users.
    You should really only use the getSequence static method.
  */
  var SequenceSchema = new Schema({
      name: {type: String, required: true, index: true} 
    , sequence: {type: Number, required: true}
  });

  /**
    Gets a sequence value atomically for the given name.
    @param name of the sequence (ie. 'logid')
    @param callback(err, sequenceNumber) function to call when complete. 
  */
  SequenceSchema.statics.getSequence = function(name, callback) {
  	this.collection.findAndModify({name: name}, [['name', 'asc']], {$inc: {sequence: 1}}, {'upsert': true, 'new': true}, function(err, sequence) {
      var s = null
      if(sequence) s = sequence.sequence;
  		callback(err, s);
  	});
  };

  mongoose.model('Sequence', SequenceSchema);
};