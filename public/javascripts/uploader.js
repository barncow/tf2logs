;(function($){
  //big todo, use templates instead of direct HTML manipulation

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
    }
  });

  /**
    A collection of log files, this will be our queue of files to upload
  */
  var LogFileCollection = Backbone.Collection.extend({
    model: LogFile
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
      var html = '<form><label>File Name:</label>'+this.model.get('logFileName') //todo XSS
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
      'click #addLogFiles':  'addLogFiles'
    }

    , initialize: function(){
      _.bindAll(this, 'render', 'addLogFiles');
      this.render();
      this.logQueueView = new LogQueueView({collection: this.collection});
      this.logFileMetaView = null;
    }

    , render: function(){
      //todo each view should render its own interface
      $(this.el).html('<div id="logUploadQueue"><ul></ul></div><button id="addLogFiles">Add Logs</button><button id="uploadLogFiles">Upload Logs</button>');
    }

    , addLogFiles: function() {
      var logFile = new LogFile();
      var logFileName = 'l'+tempIndex+'.log'; //todo remove tempindex
      logFile.set({
        logFileName: logFileName
        , logName: logFileName
      });
      this.collection.add(logFile);

      this.changeMetaView(logFile);

      ++tempIndex; //todo remove
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

