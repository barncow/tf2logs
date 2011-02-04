<?php use_helper('Log') ?>
<?php use_helper('Implode') ?>
<?php use_stylesheet('jquery.tooltip.css'); ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_stylesheet('jquery-ui-1.8.9.custom.css'); ?>
<?php use_stylesheet('demo_table_jui.css'); ?>
<?php use_javascript('jquery.dataTables.min.js'); ?>

<?php if(mapExists($log['map_name'])): ?>
<?php use_stylesheet('canvas.css'); ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<?php use_javascript('class.js'); ?>
<?php use_javascript('mapviewer.js'); ?>
<?php use_javascript('/maps/'.$log['map_name'].'/map.js'); ?>
<?php use_dynamic_javascript(url_for('@events_by_id?id='.$log['id'])); ?>
<?php endif ?>


<?php use_javascript('jquery.dimensions.js'); ?>
<?php use_javascript('jquery.tooltip.min.js'); ?>
<?php use_javascript('logshow.js'); ?>
<div id="logName"><?php echo $log['name'] ?></div>

<div id="score">
  <span class="Red teamName">Red</span> <span class="red"><?php echo $log['redscore'] ?></span>
   <span class="winSeparator"><?php echo getWinSeparator($log['redscore'], $log['bluescore']) ?></span> 
   <span class="Blue teamName">Blue</span> <span class="blue"><?php echo $log['bluescore'] ?></span>
</div>

<?php if(mapExists($log['map_name'])): ?>
<canvas id="mapViewer" class="ui-state-default ui-corner-all"></canvas>
<div id="mapViewerControls">
	<button id="playPauseButton"></button>
	<div id="playbackProgress"><span id="totalTime"></span></div>
	<div style="clear: both">
	  <label for="playbackSpeed">Playback Speed</label>
	  <select id="playbackSpeed">
	    <option value="1">1x</option>
	    <option value="5" selected>5x</option>
	    <option value="20">20x</option>
	  </select>
	  
	  <label for="isCumulitive">Cumulitive</label>
	  <input type="checkbox" id="isCumulitive"/>
	</div>
</div>
<div id="chatBox" class="ui-widget-content ui-corner-all"><ul></ul></div>
<?php endif ?>

<div id="totalTime">Total Time: <span class="time"><?php echo outputSecondsToHumanFormat($log['elapsed_time']) ?></span></div>
<?php $miniStats = array() ?>
<?php echo outputStatPanel($log['Stats'], $miniStats) ?>

<?php echo outputMedicStats($log['Stats']) ?>

<?php echo outputWeaponStats($weapons, $miniStats, $weaponStats) ?>

<?php echo outputPlayerStats($miniStats, $playerStats) ?>

Created  <?php echo $log['created_at'] ?><br/>
<?php if($log['created_at'] != $log['updated_at']): ?>
  Last Generated <?php echo $log['updated_at'] ?> 
<?php endif ?>


<script type="application/x-javascript">
<?php if(mapExists($log['map_name'])): ?>
  var gameMapObj;
  var mapViewerObj;
  <?php echo outputPlayerCollection($log['Stats']); ?>
  <?php echo outputWeaponCollection($weapons); ?>
<?php endif ?>

/////////////////////////////////////////////////////////////////////////////////////
// Doc ready
/////////////////////////////////////////////////////////////////////////////////////
$(function (){  
  <?php if(mapExists($log['map_name'])): ?>
	  mvc = $("#mapViewerControls");
	  mapViewerObj = new MapViewer(gameMapObj, playerCollection, logEventCollection, weaponCollection, $("#mapViewer"), mvc, $("#playPauseButton"), $("#playbackProgress"), $("#chatBox"), $("#playbackSpeed"), $("#isCumulitive"));
	<?php endif ?>
	
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
});
</script>

<div id="canvasTooltip"></div>
