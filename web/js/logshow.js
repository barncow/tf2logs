$(function() {
  $('.statTable th, #playerStats thead .avatarImage, .killIcon, .classIcon, span[title]').tooltip({ 
    track: true, 
    delay: 0, 
    showURL: false, 
    showBody: " - ", 
    fade: 250 
  });
  
  $('#statPanel, #playerStats, #weaponStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false
	});
	
	$('#medicStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aaSorting": [[1,'desc'], [2,'desc'], [3,'desc'], [4,'desc'], [5,'desc'], [6,'desc'], [7,'desc']]
	});
	
	$('.statTable').children("caption").each(function(index,obj){
	  obj = $(obj);
	  html = obj.html();
	  obj.html("");
	  obj.closest(".dataTables_wrapper").children(".fg-toolbar:first").prepend('<div class="statTableCaption css_left">'+html+'</div>');
	});
	
	$('.dataTables_filter').children(':text').addClass("ui-widget-content ui-corner-all");
});
