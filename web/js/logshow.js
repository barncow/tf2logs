$(function() {  
  $('th[title], img[title], span[title]').qtip({
    style: {
      classes: "ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tf2"
    }
  });
  
  jQuery.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
	  var x = a.replace( /<.*?>/g, "" );
	  var y = b.replace( /<.*?>/g, "" );
	  x = parseFloat( x );
	  y = parseFloat( y );
	  return ((x < y) ? -1 : ((x > y) ?  1 : 0));
  };

  jQuery.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
    var x = a.replace( /<.*?>/g, "" );
    var y = b.replace( /<.*?>/g, "" );
    x = parseFloat( x );
    y = parseFloat( y );
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
  };
  
  $('#statPanel, #playerStats, #weaponStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
      { "sType": "num-html", "aTargets": [ "_all" ] }
    ]
	});
	
	$('#medicStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aaSorting": [[1,'desc'], [2,'desc'], [3,'desc'], [4,'desc'], [5,'desc'], [6,'desc'], [7,'desc']],
		"aoColumnDefs": [
      { "sType": "num-html", "aTargets": [ "_all" ] }
    ]
	});
	
	$('.statTable').children("caption").each(function(index,obj){
	  obj = $(obj);
	  html = obj.html();
	  obj.html("");
	  obj.closest(".dataTables_wrapper").children(".fg-toolbar:first").prepend('<div class="statTableCaption css_left">'+html+'</div>');
	});
	
	$('.dataTables_filter').children(':text').addClass("ui-widget-content ui-corner-all");
});
