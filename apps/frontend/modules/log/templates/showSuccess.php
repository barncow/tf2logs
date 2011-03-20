<?php
$sf_response->setTitle($log['name'].' - TF2Logs.com');
use_helper('Log');
use_helper('PageElements');
use_stylesheet('demo_table_jui.css'); 
use_javascript('jquery.dataTables.min.js'); 
use_javascript('FixedColumns.min.js');

if(mapExists($log['map_name'])) {
use_stylesheet('canvas.css'); 
use_javascript('jquery-ui-1.8.9.custom.min.js'); 
use_javascript('class.js'); 
use_javascript('mapviewer.min.js'); 
use_javascript('/maps/'.$log['map_name'].'/map.js'); 
use_dynamic_javascript(url_for('@events_by_id?id='.$log['id'])); 
}

use_javascript('jquery.qtip.min.20110205.js'); 
use_stylesheet('jquery.qtip.min.20110205.css'); 
use_javascript('logshow.min.js');
?>

<?php if($sf_user->isAuthenticated() && ($sf_user->getAttribute(sfConfig::get('app_playerid_session_var')) == $log['submitter_player_id'] || $sf_user->hasCredential('owner'))): ?>
<div class="center">
<?php echo link_to('Edit this Log File', '@log_edit?id='.$log['id']) ?>
</div>
<br class="hardSeparator"/>
<?php endif ?>

<div id="score" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header"><?php echo $log['name'] ?></div>
  <div class="content">
    <span class="Red teamName">Red</span> <span class="red"><?php echo $log['redscore'] ?></span>
    <span class="winSeparator"><?php echo getWinSeparator($log['redscore'], $log['bluescore']) ?></span> 
    <span class="Blue teamName">Blue</span> <span class="blue"><?php echo $log['bluescore'] ?></span>
    <br/>
    <span class="subInfo">
      Total Time: <?php echo outputSecondsToHumanFormat($log['elapsed_time']) ?><br/>
      Uploaded: <?php echo getHumanReadableDate($log['created_at']) ?><br/>
      By: <?php echo link_to($log['Submitter']['name'], 'player/showNumericSteamId?id='.$log['Submitter']['numeric_steamid']) ?><br/>
      Views: <?php echo $log['views'] ?>
    <?php if($log['created_at'] != $log['updated_at']): ?>
      <br/><span title="The Last Generated date represents when the log submitter made changes to the name and map, or when an admin regenerates the log.">Last Generated: <?php echo getHumanReadableDate($log['updated_at']) ?></span>
    <?php endif ?>
      <br/><?php echo link_to('Download Log File', '@logfile?id='.$log['id'], array('target' => '_blank')) ?>
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<br class="hardSeparator"/>

<?php include_component('log', 'showLog', array('log' => $log)) ?>

