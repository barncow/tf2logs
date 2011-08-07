;(function($){
  //big todo, use templates instead of direct HTML manipulation
  //todo remove any lingering console.log

  //overriding .sync here to prevent errors on model.destroy()
  Backbone.sync = function(method, model, success, error){
    success();
  }

  var tempIndex = 0; //todo remove - only here to show diff files

  /**
    Model - Represents a LogFile and all of its data (name, map, tags, etc.)
  */
  var LogFile = Backbone.Model.extend({
    defaults: {
      logName: 'My Log'
      , mapName: ''
      , status: 'Waiting to upload.'
    }
  });

  /**
    A collection of log files, this will be our queue of files to upload
  */
  var LogFileCollection = Backbone.Collection.extend({
    model: LogFile

    , getByPluploadFileId: function(fileId) {
      return this.find(function(logFile){
        return (logFile.get('fileId') === fileId);
      });
    }
  });

  /**
    This represents the LogFile model in the queue panel. It is a miniature version of the model.
  */
  var LogFileQueueView = Backbone.View.extend({
    tagName: 'li'

    , events: {
      'click span.remove':  'removeLogFile'
      , 'click span.logName': 'viewLogFile'
    }

    , initialize: function(){
      _.bindAll(this, 'render', 'unrender', 'removeLogFile');

      this.model.bind('change', this.render);
      this.model.bind('remove', this.unrender);
    }

    , render: function(){
      $(this.el).html('<span class="logFileName">'+this.model.get('logFileName')+'</span><span class="logName">'+this.model.get('logName')+'</span><span class="remove">x</span>');//todo XSS
      return this;
    }

    , unrender: function(){
      $(this.el).remove();
    }

    , removeLogFile: function(){
      logUploaderView.removeFile(this.model.fileId);
      this.model.destroy();
    }

    , viewLogFile: function() {
      logUploaderView.changeMetaView(this.model);
    }
  });

  /**
    This represents the form to make changes to the log file's map, tags, etc.
  */
  var LogFileMetaView = Backbone.View.extend({
    el: '#logFileMetaEditor'

    , events: {
      "change input": "saveChanges"
    }

    , initialize: function(){
      _.bindAll(this, 'render', 'saveChanges');
      this.model.bind('change', this.render);
      this.render();
    }

    , render: function(){
      var html = '<span class="status">'+this.model.get('status')+'</span>'
        +'<form><label>File Name:</label>'+this.model.get('logFileName') //todo XSS
        +'<br/> <label for="logName">Log Name:</label><input type="text" class="logName" value="'+this.model.get('logName')+'"/></form>'
        +'<br/> <label for="mapName">Map Name:</label><input type="text" class="mapName" value="'+this.model.get('mapName')+'"/></form>'

      $(this.el).html(html);
      return this;
    }

    , saveChanges: function(e) {
      var props = {};
      props[e.target.className] = e.target.value;
      this.model.set(props);
    }
  });

  /**
    This represents the entire queue of files
  */
  var LogQueueView = Backbone.View.extend({
    el: '#logUploadQueue'

    , initialize: function(){
      _.bindAll(this, 'render', 'appendLogFile');

      this.collection.bind('add', this.appendLogFile);
    }

    , render: function(){
      _(this.collection.models).each(function(logFile){
        appendLogFile(logFile);
      }, this);
      return this;
    }

    , appendLogFile: function(logFile) {
      var logFileQueueView = new LogFileQueueView({
        model: logFile, collection: this.collection
      });
      logUploaderView.changeMetaView(logFile);
      this.$('ul').append(logFileQueueView.render().el);
    }
  });

  /**
    Main application view.
  */
  var LogUploaderView = Backbone.View.extend({
    el: $('#logUploader')

    , events: {
      //addLogFiles is handled by plupload
      'click #uploadLogFiles':  'uploadLogFiles'
    }

    , initialize: function(){
      var self = this;
      _.bindAll(self, 'render', 'addLogFile', 'removeFile', 'uploadLogFiles');
      self.render();
      self.logQueueView = new LogQueueView({collection: self.collection});
      self.logFileMetaView = null;

      self.uploader = new plupload.Uploader({
		    runtimes : 'html5'
		    , browse_button : 'addLogFile'
		    , container : 'logUploadQueue'
		    , drop_element: 'logUploadQueue'
		    , file_data_name: 'logfile'
		    , max_file_size : '5mb'
		    , url : '/logs/upload'
		    , multipart: true
		    , multipart_params: {}
	    });
      self.uploader.init();
      self.uploader.bind('FilesAdded', function(up, files) {
		    _.each(files, function(file) {
		      self.addLogFile(file.name, file.id, plupload.formatSize(file.size));
		    });

		    up.refresh(); //realign file open button
	    });

	    self.uploader.bind('UploadProgress', function(up, file) {
		    var status;
		    switch (file.status) {
			    case plupload.FAILED:
				    status = "An Error Occurred.";
				    break;

			    case plupload.UPLOADING:
			      if(file.percent === 100) {
			        //file has been uploaded, just waiting for response.
				      status = "<strong>Processing...</strong>";
				    } else {
				      status = "Uploading: "+file.percent + "%";
				    }
				    var logFile = self.collection.getByPluploadFileId(file.id);
	          logFile.set({status: status});
				    break;
		    }
	    });

	    self.uploader.bind('Error', function(up, err) {
	      var logFile = self.collection.getByPluploadFileId(file.id);
	      logFile.set({status: "Error: " + err.code + ", Message: " + err.message});
	    });

	    self.uploader.bind('FileUploaded', function(up, file) {
	      var logFile = self.collection.getByPluploadFileId(file.id);
	      logFile.set({status: 'Done.'});
	    });

	    self.uploader.bind('BeforeUpload', function(up, file) {
	      //plupload will handle upload, however we need to pass our extra model fields along with the request.
	      var logFile = self.collection.getByPluploadFileId(file.id);
	      self.changeMetaView(logFile);
		    up.settings.multipart_params['logName'] = logFile.get('logName');
		    up.settings.multipart_params['mapName'] = logFile.get('mapName');
	    });
    }

    , render: function(){
      //todo each view should render its own interface
      $(this.el).html('<div id="logUploadQueue"><div class="helper">To upload logs, click the Add Logs button or drag your log files from the desktop here.</div><div><ul></ul></div></div><button id="addLogFiles">Add Logs</button><button id="uploadLogFiles">Upload Logs</button>');
    }

    , addLogFile: function(fileName, fileId, fileSize) {
      var logFile = new LogFile();
      logFile.set({
        logFileName: fileName
        , logName: fileName
        , fileId: fileId
        , fileSize: fileSize
      });
      this.collection.add(logFile);

      this.changeMetaView(logFile);
    }

    , removeFile: function(id) {
      var file = this.uploader.getFile(id);
      if(file) this.uploader.removeFile(file);
    }

    , uploadLogFiles: function() {
      this.uploader.start();
    }

    , changeMetaView: function(model){
      if(this.logFileMetaView) {
        this.logFileMetaView.remove();
        $(this.el).append('<div id="logFileMetaEditor"></div>');
      }
      this.logFileMetaView = new LogFileMetaView({model: model, collection: this.collection});
    }
  });

  var logFileCollection = new LogFileCollection();
  var logUploaderView = new LogUploaderView({collection: logFileCollection});
})(jQuery);

