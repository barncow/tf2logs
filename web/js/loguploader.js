$(function() {
	$("#uploader").plupload({
		runtimes : 'html5',
		url : uploadurl,
		max_file_size : '2mb',
		headers: { 'X-REQUESTED-WITH':'XMLHttpRequest' },
		resize: false,

		multipart: true,
		multipart_params: {
			'log[_csrf_token]': csrftoken
		},
		file_data_name: 'log[logfile]',
		
		preinit: function(up) {
		  up.logMetaAttributes = {};
		  
		  up.bind('QueueChanged', _cacheFileInfos);
		  
		}
	});
	$("#uploader_container").attr('title', ''); //clearing the ui's runtime title
	var droptext = "Drag and drop log files here, or click the Add Files button.";
	$(".plupload_droptext").html(droptext);
	$(".plupload_header_title").html('Upload Log Files <button id="uploadHelpButton"></button>');
	$(".plupload_header_text").html('Click the Add Files button to add a log file to the queue. You can enter in an optional name and map name for the log file. In order to view the events of the log with the Log Viewer, a map must be specified. When you are ready, click Start Upload. If you make a mistake, you can always edit your log files at <a href="'+$("#mycplink").attr("href")+'" style="text-decoration: underline;">My TF2Logs</a>.');
	
	$('#helpMessage').dialog({ 
	  autoOpen: false, 
	  modal: true,
	  height: 300,
		width: 500,
		buttons: {
			Close: function() {
				$(this).dialog("close");
			}
		}
	});
	$('#uploadHelpButton').button({
	  icons: {
      primary: "ui-icon-help",
      text: false,
      label: ''
    }
	}).click(function(e){
	  e.preventDefault();
	  $('#helpMessage').dialog('open');
	}).removeClass("ui-button-text-icon-primary").addClass("ui-button-icon-only"); //adjust styling
	
	var uploader = $('#uploader').plupload('getUploader');
	//since we updated the text of the uploader, in order for the upload button to work, we need to refresh its position.
	uploader.refresh();
	
	//this callback must be added after the upload queue is initialized, since it has to be fired after the ui
	//has added its html.
	uploader.bind('QueueChanged', _queueChanged);
	    
  uploader.bind('BeforeUpload', function(up, file) {
    $('#'+file.id+' .plupload_file_name .logInfo .status').html("<strong>Uploading...</strong>");
    $('#'+file.id+' .plupload_file_name :text').attr('disabled', true);
    up.settings.multipart_params['log[name]'] = $('#logName' + file.id).val();
    up.settings.multipart_params['log[map_name]'] = $('#logMapName' + file.id).val();
    _cacheFileInfo(up, file); //caching queue status for rebuilding when uploads are complete
  });
  
  uploader.bind('FileUploaded', function(up, file, response) {
    console.log('FIleUploaded');
    //$('#'+file.id+' .openAC').button( "option", "icons", {primary:'ui-icon-triangle-1-s'});
    var obj = jQuery.parseJSON(response.response);
    var status = $('#'+file.id+' .plupload_file_name .logInfo .status');
    
    /*
      plupload changed from 1.4 to 1.4.2
      when the upload queue is finished, the entire queue would be rebuilt, without doing a queuechanged event.
      so, all form info, links, status, etc would be cleared, but only on the last completed upload. We can tell
      that the queue has been rebuilt because the status object will not have any data. If this is true, we will
      rebuild the queue, and find the status for the last file.
    */
    if(!status || status.length == 0) {
      _queueChanged.call(this,up); //since we are calling this outside of a normal event handler, we need to set the "this" variable manually.
      
      //since the queue is rebuilt, we need to re-disable our text boxes for all files.
      $('.plupload_file_name :text').attr('disabled', true);
      
      status = $('#'+file.id+' .plupload_file_name .logInfo .status'); //with queue rebuilt, re-find status.
    }
    if(obj.url) {
      status.html('<a href="'+obj.url+'" class="viewLogLink">View the Log</a>');
    } else {
      status.html('<span class="error">'+obj.msg+'</span>');
    }

    _cacheFileInfo(up, file);//caching queue status for rebuilding when uploads are complete
  });
  
  uploader.bind('UploadComplete', function(up, files) {
    console.log('uploadcomplete');
  });
  
  //plupload removes everything on DONE status, want to re-draw when plupload is done.
  uploader.bind('StateChanged', function(up) {
    if(!up.previousState) up.previousState = null;
    
    if(up.state === plupload.STOPPED && up.previousState === plupload.STARTED) {
      //stopped can be an initial state. we only want to do work if the stopped is after a started state, so we can redraw our info.
      _queueChanged.call(this,up); //since we are calling this outside of a normal event handler, we need to set the "this" variable manually.
      
      //since the queue is rebuilt, we need to re-disable our text boxes for all files.
      $('.plupload_file_name :text').attr('disabled', true);
    }
    
    up.previousState = up.state;
  });
  
  uploader.bind('UploadProgress', function(up, file) {
    console.log(file.status);
    switch (file.status) {			
			case plupload.FAILED:
				$('#'+file.id+' .plupload_file_name .logInfo .status').html("An Error Occurred.");
				break;

			case plupload.UPLOADING:
			  //$('#'+file.id+' .openAC').button( "option", "icons", {primary:'ui-icon-triangle-1-s'});
			  if(file.percent == 100) {
			    //file has been uploaded, just waiting for response.
				  $('#'+file.id+' .plupload_file_name .logInfo .status').html("<strong>Processing...</strong>");
				  _cacheFileInfo(up, file);//caching queue status for rebuilding when uploads are complete
				}
				break;
		}
	});
	
	//the purpose of this is to fire before the ui clears its file list, and store any values that were entered
  //by the user. These will then be repopulated later.
	function _cacheFileInfos(up) {
    $.each(this.files, function(i, file) {
      _cacheFileInfo(up, file);
    });
  }
  
  function _cacheFileInfo(up, file) {
    up.logMetaAttributes['logName' + file.id] = $('#logName' + file.id).val();
    up.logMetaAttributes['logMapName' + file.id] = $('#logMapName' + file.id).val();
    up.logMetaAttributes['logInfo' + file.id] = $('#'+file.id+' .plupload_file_name .logInfo').html();
  }
	
	function _queueChanged(up) {
	  if(up.logMetaAttributes.length == 0) return;
    $.each(this.files, function(i, file) {
      logNameVal = "";
      logMapNameVal = "";
      logInfo = '<span class="status">Ready to Upload</span>';
      if(up.logMetaAttributes['logName' + file.id]) {
        logNameVal = ' value="'+up.logMetaAttributes['logName' + file.id]+'"';
      }
      if(up.logMetaAttributes['logMapName' + file.id]) {
        logMapNameVal = ' value="'+up.logMetaAttributes['logMapName' + file.id]+'"';
      }
      if(up.logMetaAttributes['logInfo' + file.id]) {
        logInfo = up.logMetaAttributes['logInfo' + file.id];
      }
      
	    $('#'+file.id+' .plupload_file_name').append(
	      '<div class="logMetaData ui-priority-secondary">' +
		    '<div class="logFormFieldContainer"><label for="logName' + file.id + '">Log Name</label> <input type="text" maxlength="100" id="logName' + file.id + '" class="ui-widget-content ui-corner-all"'+logNameVal+' title="Optional. By default, the name of the log is the log file name. You can specify a different name here."/></div>' +
		    '<div class="logFormFieldContainer"><label for="logMapName' + file.id + '">Map Name</label> <input type="text" maxlength="25" id="logMapName' + file.id + '" title="Optional. In order to take advantage of Log Playback, you must specify the map name of the log. A list of sample maps is provided, but you can enter another map if yours is not listed." class="log_map_name ui-widget-content ui-corner-all"'+logMapNameVal+'/></div>' +
	    '</div>');
	    
	    $('#'+file.id+' .plupload_file_name').append('<div class="logInfo ui-priority-secondary">' + logInfo + '</div>');
    });
    up.logMetaAttributes = {};
    $(".log_map_name").autocomplete(ACSource);
    $(".plupload_file_name label").inFieldLabels();
    $(".plupload_droptext").html(droptext);
    $('input[title]').qtip({
      position: {
        my: "bottom center",
		    at: "top center"
      },
      style: {
        classes: "ui-tooltip-rounded ui-tooltip-shadow ui-tooltip-tf2"
      }
    });
  }
});
