<?php use_helper('Log') ?>
<?php use_helper('Implode') ?>
<?php use_stylesheet('jquery.tooltip.css'); ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
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

<div id="score" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header"><?php echo $log['name'] ?></div>
  <div class="content">
    <span class="Red teamName">Red</span> <span class="red"><?php echo $log['redscore'] ?></span>
    <span class="winSeparator"><?php echo getWinSeparator($log['redscore'], $log['bluescore']) ?></span> 
    <span class="Blue teamName">Blue</span> <span class="blue"><?php echo $log['bluescore'] ?></span>
    <br/>
    <span class="subInfo">
      Total Time: <?php echo outputSecondsToHumanFormat($log['elapsed_time']) ?><br/>
      Uploaded <?php echo $log['created_at'] ?>
    <?php if($log['created_at'] != $log['updated_at']): ?>
      <br/><span title="The Last Generated date represents when an admin last re-generated this log. This can happen when features are added.">Last Generated <?php echo $log['updated_at'] ?></span>
    <?php endif ?>
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<br class="hardSeparator"/>

<?php if(mapExists($log['map_name'])): ?>
<div id="mapViewerContainer">
  <canvas id="mapViewer" class="ui-widget-content"></canvas>
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
</div>
<?php endif ?>

<?php $miniStats = array() ?>
<?php echo outputStatPanel($log['Stats'], $miniStats) ?>

<?php echo outputMedicStats($log['Stats']) ?>

<?php echo outputWeaponStats($weapons, $miniStats, $weaponStats) ?>

<?php echo outputPlayerStats($miniStats, $playerStats) ?>

<?php if(mapExists($log['map_name'])): ?>
<script type="application/x-javascript">
  var gameMapObj;
  var mapViewerObj;
  <?php echo outputPlayerCollection($log['Stats']); ?>
  <?php echo outputWeaponCollection($weapons); ?>

/////////////////////////////////////////////////////////////////////////////////////
// Doc ready
/////////////////////////////////////////////////////////////////////////////////////
$(function (){  
	  mvc = $("#mapViewerControls");
	  mapViewerObj = new MapViewer(gameMapObj, playerCollection, logEventCollection, weaponCollection, $("#mapViewer"), mvc, $("#playPauseButton"), $("#playbackProgress"), $("#chatBox"), $("#playbackSpeed"), $("#isCumulitive"));
});
</script>
<?php endif ?>

<div id="canvasTooltip"></div>
