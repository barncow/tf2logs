<?php 
$sf_response->setTitle("Plugins - TF2Logs.com");
use_helper('PageElements');
?>

<div id="whatsNewContainer">

<?php
$log314Link = link_to('Here is a sample log with these features active', '@log_by_id?id=314');
$s = <<<EOD
<a name="suppstats"/>
<p>
  This SourceMod plugin was created by Cinq and Annuit Coeptis. Its original intent was to only output damage done statistics, but it has grown beyond that. Here is what the plugin currently supports:
  <ul>
    <li>Damage Done</li>
    <li>Heals Received per Player</li>
    <li>Items Picked Up - such as medkits and ammo boxes</li>
    <li>Pause/Unpause logging</li>
  </ul>
</p>
<p>In order to use this plugin on your server, you will need to have <a href="http://www.sourcemod.net">SourceMod</a> <a href="http://wiki.alliedmods.net/Installing_SourceMod">installed</a> and running on your server. Then, just put the plugin from the download link below into your SourceMod plugins directory.</p>
<h3><a href="/files/supstats.smx">DOWNLOAD SUPPLEMENTAL STATS PLUGIN</a></h3>
$log314Link<br/>
<a href="https://github.com/qpingu/tf2.pug.na-game-server/blob/master/addons/sourcemod/scripting/supstats.sp">Source on Github</a>
EOD;
echo outputInfoBox("suppstatscontent", "Supplemental Stats Plugin", $s);
?>
</div>
