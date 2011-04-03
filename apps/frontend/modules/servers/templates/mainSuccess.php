<?php
$sf_response->setTitle($server->getName().' - TF2Logs.com');
use_helper('PageElements');

$status = url_for("@server_status?slug=".$server->getSlug());
$s = <<<EOD
Server landing page for {$server->getName()}<br/>
<a href="$status">Status Page</a>
EOD;
echo '<div id="contentContainer">';
echo outputInfoBox("mainServer", $server->getName(), $s);
echo '</div><br class="hardSeparator"/>';
?>
