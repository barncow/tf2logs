<?php
$sf_response->setTitle($log['name'].' - TF2Logs.com');
use_helper('Log');
use_helper('PageElements');
use_stylesheet('demo_table_jui.css'); 
use_javascript('jquery.dataTables.min.js'); 
use_javascript('FixedColumns.min.js');
use_javascript('jquery-ui-1.8.9.custom.min.js'); 

if(mapExists($log['map_name'])) {
use_stylesheet('canvas.css'); 
use_javascript('class.js'); 
use_javascript('mapviewer.min.js'); 
use_javascript('/maps/'.$log['map_name'].'/map.js'); 
use_dynamic_javascript(url_for('@events_by_id?id='.$log['id'])); 
}

use_javascript('jquery.qtip.min.20110205.js'); 
use_stylesheet('jquery.qtip.min.20110205.css'); 
use_javascript('logshow.min.js');
?>

<div id="score" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header"><?php echo $log['name'] ?></div>
  <div class="content">
    <span class="Red teamName">Red</span> <span class="red"><?php echo $log['redscore'] ?></span>
    <span class="winSeparator"><?php echo getWinSeparator($log['redscore'], $log['bluescore']) ?></span> 
    <span class="Blue teamName">Blue</span> <span class="blue"><?php echo $log['bluescore'] ?></span>
    <br/>
    <span class="subInfo">
      Total Time: <?php echo outputSecondsToHumanFormat($log['elapsed_time']) ?><br/>
      <span title="Playable Time is Total Time with the time spent during halftime removed. Also, if this server used the Supplemental Stats plugin, this will remove time spent during pausing of the game. This value is used to calculate the per minute stats below.">Playable Time: <?php echo outputSecondsToHumanFormat($log['game_seconds']) ?></span><br/>
      Map: <?php
        if(isset($log['map_name']) && strlen($log['map_name']) > 0) {
          echo $log['map_name'];
        } else {
          echo "None Specified";
        }
        echo "<br/>";
      ?>
      Uploaded: <?php echo getHumanReadableDate($log['created_at']) ?><br/>
      By: <?php echo link_to($log['Submitter']['name'], 'player/showNumericSteamId?id='.$log['Submitter']['numeric_steamid']) ?><br/>
      Views: <?php echo $log['views'] ?>
    <?php if($log['created_at'] != $log['updated_at']): ?>
      <br/><span title="The Last Generated date represents when the log submitter made changes to the name and map, or when an admin regenerates the log.">Last Generated: <?php echo getHumanReadableDate($log['updated_at']) ?></span>
    <?php endif ?>
      <br/><?php echo link_to('Download Log File', '@logfile?id='.$log['id'], array('target' => '_blank')) ?>
      
      <?php if($sf_user->isAuthenticated() && ($sf_user->getAttribute(sfConfig::get('app_playerid_session_var')) == $log['submitter_player_id'] || $sf_user->hasCredential('owner'))): ?>
        <br/><?php echo link_to('<strong class="yellowText">Edit this Log File</strong>', '@log_edit?id='.$log['id'], array('class' => 'yellowText')) ?>
      <?php endif ?>
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<br class="hardSeparator"/>

<?php include_component('log', 'showLog', array('log' => $log)) ?>

<div id="healSpreadHelp" title="Heal Spread">
  <p>The heal spread lists the players that played in the log in the first column. The other columns represent what healer did the healing, and how much healing they gave the player listed in the first column. This can help the players see how heals are spread around, and be able to make adjustments as needed.</p>
  <p>You can see who the healer was by hovering your mouse over the player's avatar. You can also click on the avatar to sort the table.</p>
  <p>This is only available when using the <a href="<?php echo url_for('@plugins') ?>#suppstats">Supplemental Stats SourceMod plugin</a> on the server that the log was recorded.</p>
</div>

<div id="itemsPickedUpHelp" title="Items Picked Up">
  <p>The Items Picked Up table lists the players that played in the log in the first column. The other columns represent what item was picked up, and how many times the player listed in the first column picked the item up.</p>
  <p>You can see what the item was by hovering your mouse over the icon of the item. You can also click on the icons to sort the table.</p>
  <p>This is only available when using the <a href="<?php echo url_for('@plugins') ?>#suppstats">Supplemental Stats SourceMod plugin</a> on the server that the log was recorded.</p>
</div>

<div id="playerStatsHelp" title="Player Stats">
  <p>The Player Stats table lists all the players down the first column, and also lists them by their avatar going across the table. You can see who the player's avatar represents by hovering your mouse over the avatar which will show the name of the player. You can also sort the table by clicking on an avatar.</p>
  <p>As you read the table lengthwise, you can see who that player killed, and how many times the other player was killed. As you read the table going down, you can see who killed that player, and how many times.</p>
</div>

<div id="logStatsHelp" title="Log Stats">
  <p>The log stats table lists the players that played in the log in the first column. The other columns show the player's stats for the log file. You can see what the acronym for the stat represents by hovering your mouse over the acronym.</p>
  <p>The two stats DA (Damage) and DAPM (Damage per Minute) represent how much hurt the player inflicted on the other team. This stat allows a team to see who outputs the most damage, but not necessarily the most kills, thus indicating more accurate performance in the game. Damage stats are only available when using the <a href="<?php echo url_for('@plugins') ?>#suppstats">Supplemental Stats SourceMod plugin</a> on the server that the log was recorded.</p>
</div>

<div id="medicComparisonHelp" title="Medic Comparison">
  <p>The Medic Comparison table lists the medics for the log in the first column. The other columns show the medic's stats for the log file. You can see what the acronym for the stat represents by hovering your mouse of the acronym. This table is auto-sorted by all the stats in descending order, with the exception of DU (Dropped Ubers) which is sorted in ascending order (since less is better). It is an easy way for the medic to gauge their performance compared to the other team's medic.</p>
</div>

<div id="weaponStatsHelp" title="Weapon Stats">
  <p>The Weapon Stats table lists the players that played in the log in the first column. The weapons are listed by their in-game kill icon as you go across the table lengthwise. You can see the weapon name by hovering your mouse over the icon. The row just beneath the icons lists the kills (K) and deaths (D) for the player listed in the first column.</p>
</div>

<div id="logViewerHelp" title="Log Viewer">
  <p>The Log Viewer, when active, allows you to see the kills and deaths that occurred in the log on an overhead view of the map. The Log Viewer is only active when the user specifies a map for the log, or if not specified, is listed in the log file (which is rare, since the log file's save point generally starts after this line is output). The Log Viewer also will only be active when TF2Logs.com supports the map that was entered, if any. New maps are being added all the time, check the <?php echo link_to("What's New",'@plugins') ?> page to keep informed! TF2Logs.com will generally only support finalized maps due to the work involved in generating support for the map.</p>
  <p>When the Log Viewer is active, you will first see an overhead view of the map used in the log. On the map, you will see circles indicating capture points and/or intelligence locations. The circles will be colored blue if the point is owned by the Blue team, or colored red if owned by the Red team. Gray coloring indicates that point is neutral. You can hover your mouse over the capture points to see what the point is called. If the point was captured by a team, the players that captured the point are listed as well.</p>
  <p>On the bottom left corner of the map image, you will see red and blue numbers which represent the current score for each team at that point in the playback. The score information can move to another corner if they will block the map's playable area.</p>
  <p>Next you will see a play/pause button, which will start or pause playback of the events in the log file. Next to it is a slider that indicates the current position of playback. You can also move the slider anywhere in the log to jump to a specific point quicker.</p>
  <p>After the playback controls, you will see a box indicating the current playback speed. By default, it is 5x, or 5 times faster than real time. After that, you will see a button called cumulative which, when enabled, will allow you to see all the kills in the log file, up to the current playback position.</p>
  <p>Lastly, there is a box that holds all of the chat comments during game play. This indicates the team by color of the person doing the chat, as well as elapsed time within the log that the chat was done. If the player has (team) after their name, it indicates that it was team-only communication. If it is not there, the chat message went to all players.</p>
</div>
