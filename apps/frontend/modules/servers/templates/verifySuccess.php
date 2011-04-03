<?php
$sf_response->setTitle('Verify '.$server->getName().' - TF2Logs.com');
use_helper('PageElements');

$address = sfConfig::get('app_auto_server_address');
$url;
if($server->getServerGroup()->getGroupType() == ServerGroup::GROUP_TYPE_SINGLE_SERVER) {
  $url = url_for("@server_single_status?server_slug=".$server->getSlug());
} else if($server->getServerGroup()->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER){
  $url = url_for("@server_multi_status?server_slug=".$server->getSlug().'&group_slug='.$server->getServerGroup()->getSlug());
}

$s = <<<EOD
<p>Let's go through and update your server configuration, and verify ownership of the server (and make sure that everything works). Follow the next steps carefully to get started:</p>
<ol>
  <li>Add the following to the bottom of your tf/cfg/server.cfg file, on its own line: <code>logaddress_add $address;</code></li>
  <li>Restart your server, either through your game server provider's control panel or by logging in to your server's rcon and doing <code>rcon quit;</code></li>
  <li>When the server is restarted, verify that the configuration that we have done is still there. Log back in to rcon. Make sure that "$address" is on the list brought up by the following command: <code>rcon logaddress_list;</code></li>
  <li>Next, while still logged in to rcon, do the following command: <code>rcon log on; rcon say "{$server->getVerifyKey()}"; rcon log off;</code></li>
  <li>When that is done, <a href="$url">click this link</a>.</li>
</ol>
EOD;
echo '<div id="contentContainer">';
echo outputInfoBox("verifyServer", "Verify ".$server->getName(), $s);
echo '</div><br class="hardSeparator"/>';
?>
