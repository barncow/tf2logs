var ACSource = {
  delay: 0,
  minLength: 0,
  close: function(event, ui) {
    $(this).next('.openAC').removeClass('open');
  },
  open: function(event, ui) {
    $(this).next('.openAC').addClass('open');
  },
  create: function(event, ui) {
    var self = $(this);
    self.after('<button class="openAC"></button>');
    self.next('.openAC').button( {
					text: false,
					icons: {
						primary: "ui-icon-triangle-1-s"
					}
				})
				.click(function(e) {
				  e.preventDefault();
					var self = $(this);
					if(self.hasClass('open')) {
					  self.removeClass('open').prev('.ui-autocomplete-input').autocomplete("close");
					} else {
					  //list all results
					  self.addClass('open').prev('.ui-autocomplete-input').autocomplete("search", "");
					}
				});
  },
  source: []
};
