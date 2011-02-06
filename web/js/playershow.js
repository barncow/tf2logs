$(function() {
  $('th[title], img[title], span[title], td[title]').qtip({
    position: {
		  viewport: $(window),
		  my: "bottom center",
		  at: "top center"
	  },
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
  
  var datatables = '#playerClassStats, #playerWeaponStats';
  $(datatables).dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "sType": "num-html", "aTargets": [ 1, 2 ] }
    ]
	});
	

  $(datatables).children("caption").each(function(index,obj){
	  obj = $(obj);
	  html = obj.html();
	  obj.html("");
	  obj.closest(".dataTables_wrapper").children(".fg-toolbar:first").prepend('<div class="statTableCaption css_left">'+html+'</div>');
	});
	
	$('.dataTables_filter').children(':text').addClass("ui-widget-content ui-corner-all");
});
