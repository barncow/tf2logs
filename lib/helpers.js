exports.helpers = {
  minutes: function(secs) {
    var tmins = 0;
    var tsecs = 0;

    if(secs > 60) {
      tmins = Math.floor(secs/60).toString();
      tsecs = secs%60;
    } else tsecs = secs;

    var padZeroes = function(num) {
      if(num < 10) return '0'+num;
      if(num.toString().length == 1) return num+'0';
      return num;
    };

    return padZeroes(tmins)+':'+padZeroes(tsecs);
  }
  , score: function(blueScore, redScore) {
    var equality;

    if(blueScore > redScore) equality = "&gt;";
    else if(blueScore < redScore) equality = "&lt;";
    else equality = "==";

    return '<span id="score"><span class="blue">'+blueScore+'</span> <span class="equality">'+equality+'</span> <span class="red">'+redScore+'</span></span>';
  }
};

/**
Flash messages code from https://github.com/alexyoung/nodepad
*/
function FlashMessage(type, messages) {
  this.type = type;
  this.messages = typeof messages === 'string' ? [messages] : messages;
}

FlashMessage.prototype = {
  get icon() {
    switch (this.type) {
      case 'info':
        return 'ui-icon-info';
      case 'error':
        return 'ui-icon-alert';
    }
  },

  get stateClass() {
    switch (this.type) {
      case 'info':
        return 'ui-state-highlight';
      case 'error':
        return 'ui-state-error';
    }
  },

  toHTML: function() {
    return '<div class="ui-widget flash">' +
           '<div class="' + this.stateClass + ' ui-corner-all">' +
           '<p><span class="ui-icon ' + this.icon + '"></span>' + this.messages.join(', ') + '</p>' +
           '</div>' +
           '</div>';
  }
};

exports.dynamicHelpers = {
    flashMessages: function(req, res) {
      var html = '';
      if(!req.session) return html;
      ['error', 'info'].forEach(function(type) {
        var messages = req.flash(type);
        if (messages.length > 0) {
          html += new FlashMessage(type, messages).toHTML();
        }
      });
      return html;
    }
  , isLoggedIn: function(req, res) {
      return (req.session && req.session.friendid && req.user);
  }
};

