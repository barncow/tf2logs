<?php
$sf_response->setTitle($serverGroup->getName().' - TF2Logs.com');
use_helper('PageElements');
$s = "";

if($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER) {
  foreach($serverGroup->getServers() as $server) {
    $status = url_for("@server_multi_status?server_slug=".$server->getSlug().'&group_slug='.$serverGroup->getSlug());
    $s .= <<<EOD
    {$server->getName()} <a href="$status">Status</a><br/>
EOD;
  } 
} else if($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_SINGLE_SERVER) {
  $server = $serverGroup->getServers();
  $server = $server[0];
  
  $status = url_for("@server_single_status?server_slug=".$server->getSlug());
  $s .= <<<EOD
  <a href="$status">Server Status</a><br/>
EOD;
}

echo '<div id="contentContainer">';
echo outputInfoBox("mainGroup", $serverGroup->getName(), $s);
echo '</div><br class="hardSeparator"/>';
?>
