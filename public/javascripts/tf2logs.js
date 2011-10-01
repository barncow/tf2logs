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
      , firstRequest = tf2logs.state.firstRequest; //shortcuts
    //todo clear flash messages
    if(contentView) contentView.remove();
    contentView = new newContentView();
    contentView.render(data);
  };

  /**
    Changes all current and future links to call backbone's navigate opposed
    to calling new pages.
  */
  tf2logs.functions.convertLinksToRoutes = function() {
    $('body').delegate('a:not(.externalLink)', 'click', function(e) {
      var href = $(this).attr("href");
      console.log("click href", href);
      Backbone.history.navigate(href, true);
      e.preventDefault();
    });
  };

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
      return window.tf2logs.templates[templateName](locals);
    }
  });

  tf2logs.views.Home = BaseContentView.extend({
    template: 'index'
    , render: function(data) {
      $(this.el).html(this.renderTemplate(this.template, data));
    }
  });

  /*#####################   ROUTES   ########################*/
  tf2logs.routers.Website = Backbone.Router.extend({
    routes: {
      "/":  "home"
    }

    , "home": function() {
      console.log('home route');
      new tf2logs.models.FrontPage().fetch({success: function(model) {
        tf2logs.functions.changeContentView(tf2logs.views.Home, model.toJSON());
      }});
    }
  });

  tf2logs.routers.Logs = Backbone.Router.extend({
    routes: {
      "/logs/:id": "show"
    }

    , "show": function(id) {
      
    }
  });

  /*#####################   INIT   ########################*/
  $(function() {
    //create router objects, then start history
    new tf2logs.routers.Website();
    Backbone.history.start({pushState: true});

    //convert links to call js routes instead of new page requests
    tf2logs.functions.convertLinksToRoutes();
  });
})();