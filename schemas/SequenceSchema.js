module.exports = function(mongoose, conf) {
	var Schema = mongoose.Schema
    , util = require('util');

  /**
    Schema for getting ID numbers via findAndModify. Creates better URLs for users.
    You should really only use the getSequence static method.
  */
  var SequenceSchema = new Schema({
      name: {type: String, required: true} 
    , sequence: {type: Number, required: true}
  });

  SequenceSchema.statics.getSequence = function(name, callback) {
  	this.collection.findAndModify({name: name}, [['name', 'asc']], {$inc: {sequence: 1}}, {'upsert': true, 'new': true}, function(err, sequence) {
  		callback(err, sequence.sequence);
  	});
  };

  mongoose.model('Sequence', SequenceSchema);
};