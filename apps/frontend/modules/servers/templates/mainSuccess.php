<?php
include_partial('navigation', array('serverGroup' => $serverGroup, 'sf_user' => $sf_user, 'server' => $server));
$sf_response->setTitle($serverGroup->getName().' - TF2Logs.com');
use_helper('PageElements');
$s = "";
$serverTitle = "to this Server";

if($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER) {
  if(!isset($server) || !$server) $serverTitle = "to this Group";
  foreach($serverGroup->getServers() as $server) {
    $liveLogLink = "";
    if($server->getLiveLogId()) {
      $liveLogLink = link_to('LIVE', '@server_multi_live?server_slug='.$server->getSlug().'&group_slug='.$serverGroup->getSlug());
    }
  
    $serverName = link_to($server->getName(), '@server_main_group?group_slug='.$serverGroup->getSlug().'&server_slug='.$server->getSlug());
    $s .= <<<EOD
    $serverName $liveLogLink<br/>
EOD;
  } 
} else if($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_SINGLE_SERVER) {
  $server = $serverGroup->getServers();
  $server = $server[0];
  
  $liveLogLink = "";
  if($server->getLiveLogId()) {
    $liveLogLink = link_to('LIVE', '@server_single_live?server_slug='.$server->getSlug())."<br/>";
  }
  
  $status = url_for("@server_single_status?server_slug=".$server->getSlug());
  $s .= <<<EOD
  <a href="$status">Server Status</a><br/>
  $liveLogLink
EOD;
}

echo '<div id="contentContainer">';
echo outputInfoBox("mainGroup", $serverGroup->getName(), $s);
echo '</div><br class="hardSeparator"/>';



?>

<div id="infoBoxContainer">
  <div id="topViewedServer" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Top Viewed Logs Added in Past Week <?php echo $serverTitle ?></div>
    <div class="content">
      <table width="100%">
      <?php if(count($topViewedLogs) == 0): ?>
        No logs added yet.
      <?php endif ?>
      <?php foreach($topViewedLogs as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td style="white-space: nowrap;"><?php echo $l->getViews() ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
  
  <br class="hardSeparator"/>
  
  <div id="recentlyAdded" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Recently Added <?php echo $serverTitle ?></div>
    <div class="content">
      <table width="100%">
      <?php if(count($recentlyAdded) == 0): ?>
        No logs added yet.
      <?php endif ?>
      <?php foreach($recentlyAdded as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td style="white-space: nowrap;"><?php echo getHumanReadableDate($l->getDateTimeObject('created_at')) ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
</div>
