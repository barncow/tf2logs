/**
  TF2Logs.js
  All javascript necessary to view the website
*/

//todo remove all console.log

//prevent accidental global creation. Not using jQuery ready event because we need the tf2logs namespace available instantly.
(function() {
  //create namespaces and singletons. Do not add classes here.
  var tf2logs = window.tf2logs = {
      models: {}
    , collections: {}
    , views: {}
    , routers: {}
    , functions: {}
    , templates: {}
    , state: {
          contentView: null
          , initialLoad: true //tracks if the page is loaded. Some pages do not need any further setup on first launch of app, others do.
    }
  };

  /*#################   HELPER FUNCTIONS   #####################*/

  /**
    Changes the content view.
    usage: tf2logs.functions.changeContentView(tf2logs.views.Home);
    Note, do not pass an instantiated view, just the "class"
    @param data object to pass to render function
  */
  tf2logs.functions.changeContentView = function(newContentView, data) {
    var contentView = tf2logs.state.contentView
      , initialLoad = tf2logs.state.initialLoad; //shortcuts
    //todo clear flash messages
    if(contentView) contentView.remove();
    contentView = new newContentView();
    contentView.render(data);
    initialLoad = false;
  };

  /**
    Changes all current and future links to call backbone's navigate opposed
    to calling new pages.
  */
  tf2logs.functions.convertLinksToRoutes = function() {
    $('body').delegate('a:not(.externalLink)', 'click', function(e) {
      e.preventDefault();
      var href = $(this).attr("href");
      if(href.charAt(0) === '/') href = href.substring(1); //need to chop off starting "/" to work with Router
      console.log("click href", href);
      Backbone.history.navigate(href, true); 
    });
  };

  /**
    Returns value of initial load state. Sets it to false when this function is used.
  */
  tf2logs.functions.getInitialLoad = function() {
    var initialLoad = false;
    if(tf2logs.state.initialLoad) initialLoad = true;
    tf2logs.state.initialLoad = false;
    return initialLoad;
  }

  /*#####################   MODELS  #########################*/

  tf2logs.models.FrontPage = Backbone.Model.extend({
    url: '/.json'
  });

  /*#####################   VIEWS   #########################*/

  /**
    Specify's a view that takes over the #content div.
  */
  var BaseContentView = tf2logs.views.BaseContentView = Backbone.View.extend({
    el: '#content'
    , renderTemplate: function(templateName, locals) {
      return tf2logs.templates[templateName](locals);
    }
  });

  tf2logs.views.Home = BaseContentView.extend({
    template: 'index'
    , render: function(data) {
      $(this.el).html(this.renderTemplate(this.template, data));
    }
  });

  tf2logs.views.LogUpload = BaseContentView.extend({
    template: 'log_upload'
    , render: function() {
      $(this.el).html(this.renderTemplate(this.template));

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
        el: '#logUploader'

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
            , url : '/logs/upload.json'
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

          self.uploader.bind('FileUploaded', function(up, file, response) {
            var logFile = self.collection.getByPluploadFileId(file.id);
            var obj = jQuery.parseJSON(response.response);
            logFile.set({status: obj.info});
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
    }
  });

  /*#####################   ROUTES   ########################*/
  tf2logs.routers.Website = Backbone.Router.extend({
    routes: {
      "":  "home"
    }

    , "home": function() {
      console.log('home route');
      if(tf2logs.functions.getInitialLoad()) return; //do nothing more if this page was already loaded
      new tf2logs.models.FrontPage().fetch({success: function(model) {
        console.log('fetch from home');
        tf2logs.functions.changeContentView(tf2logs.views.Home, model.toJSON());
      }, error: function(err) {console.log(err);}});
    }
  });

  tf2logs.routers.Logs = Backbone.Router.extend({
    routes: {
      "logs/upload": "upload"
    }

    , "upload": function(id) {
      console.log('upload route');
      tf2logs.functions.changeContentView(tf2logs.views.LogUpload);
    }
  });

  /*#####################   INIT   ########################*/
  $(function() {
    //create router objects, then start history
    new tf2logs.routers.Website();
    new tf2logs.routers.Logs();
    var isRoute = Backbone.history.start({pushState: true});

    //convert links to call js routes instead of new page requests
    tf2logs.functions.convertLinksToRoutes();
  });
})();