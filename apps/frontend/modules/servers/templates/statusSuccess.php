<?php
$sf_response->setTitle('Status - '.$server->getName().' - TF2Logs.com');
use_helper('PageElements');

$status = Server::getDescriptionForStatus($server->getStatus());
$lastmessage = getHumanReadableTimestamp($server->getLastMessage());
if(!$lastmessage || strlen($lastmessage) == 0) {
  $lastmessage = "No messages received from server";
}
$s = <<<EOD
Status: $status<br/>
Last Message Received: $lastmessage<br/>
EOD;

if($server->getStatus() == Server::STATUS_NOT_VERIFIED && checkAccess($sf_user, $server->getServerGroup()->getOwnerPlayerId())) {
  $verifylink;
  if($server->getServerGroup()->getGroupType() == ServerGroup::GROUP_TYPE_SINGLE_SERVER) {
    $verifylink = link_to('this page', '@server_verify_single?slug='.$server->getSlug());
  } else if($server->getServerGroup()->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER) {
    $verifylink = link_to('this page', '@server_verify_group?slug='.$server->getServerGroup()->getSlug().'&server_slug='.$server->getSlug());
  }

  $s .= '<p>This server has not yet been verified. Please follow the instructions on '.$verifylink.' to find out how to get this server verified and active. If you have already followed those instructions, try refreshing this page in a few moments.</p><p>Otherwise, check the instructions and make sure that the server address in your configuration for the logaddress_add line is correct, and that you have entered the proper verification code.</p>';
}

echo '<div id="contentContainer">';
echo outputInfoBox("verifyServer", "Status - ".$server->getName(), $s);
echo '</div><br class="hardSeparator"/>';
?>
