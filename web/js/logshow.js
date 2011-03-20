var qtipopts = {
  position: {
	  viewport: $(window),
	  my: "bottom center",
	  at: "top center"
  },
  style: {
    classes: "ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tf2"
  }
}

$(function() {    
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
  
  $('#statPanel').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "string", "aTargets": [ 1 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ]
	});
  
  $('#playerStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ]
	});
	
	$('#playerHealStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ]
	});
	
	$('#itemPickupStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ]
	});
	
	/*var wsdt = $('#weaponStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
		  { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ],
    "sScrollX": "800px",
		"bScrollCollapse": true,
		"fnDrawCallback": function() {
      $('#weaponStats_wrapper th').qtip(qtipopts);//on each draw, the DOM is destroyed, need to reattach.
    }
	});
	new FixedColumns(wsdt);*/
	
	$('#medicStats').dataTable({
		"bJQueryUI": true,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false,
		"bSortClasses": false,
		"aaSorting": [[1,'desc'], [2,'desc'], [3,'desc'], [4,'desc'], [5,'desc'], [6,'desc'], [7,'desc'], [8,'desc'], [9,'desc'], [10,'desc']],
		"aoColumnDefs": [
		  { "sType": "html", "aTargets": [ 0 ] }, //must be in this order
      { "bSearchable": true, "aTargets": [ 0 ] },
      { "sType": "num-html", "aTargets": [ "_all" ] },
      { "bSearchable": false, "aTargets": [ "_all" ] }
    ]
	});
	
	$('.dataTables_wrapper .statTable > caption').each(function(index,obj){
	  obj = $(obj);
	  html = obj.html();
	  obj.html("");
	  var tb = obj.closest(".dataTables_wrapper").children(".fg-toolbar:first");
	  if(tb.children('.statTableCaption').length == 0) {
	    //the datatables scrolling tables doubles up on captions. we only want to insert one caption, so if one is already inserted, don't insert again.
	    tb.prepend('<div class="statTableCaption css_left">'+html+'</div>');
	  }
	});
	
	$('.dataTables_filter').children(':text').addClass("ui-widget-content ui-corner-all").attr('title', 'Enter a player name to narrow the results.');
	
	//this must be last since the above code generates HTML.
	$('th[title], img[title], span[title], input[title], label[title], td[title]').qtip(qtipopts);
});
