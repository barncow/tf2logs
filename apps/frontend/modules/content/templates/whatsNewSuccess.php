<?php 
$sf_response->setTitle("What's New - TF2Logs.com");
use_helper('PageElements');

$statpagehref = url_for('@plugins').'#suppstats';
?>

<div id="whatsNewContainer">
<?php
/**************************
WHEN ADDING AN ENTRY BELOW, UPDATE THE DATE TITLE OF THE INFOBOX,
AND UPDATE THE WHAT'S NEW DATE IN layout.php!
***************************/
?>

<?php
$ShwanLink = link_to('Shwan', '@player_by_numeric_steamid?id=76561198003897562');
$s = <<<EOD
<h3>Updated Front Page with How-to Video</h3>
<p>Added a link on the front page to the excellent <a href="http://www.youtube.com/watch?v=knoyakbrTok">video</a> that $ShwanLink made. It shows you how to upload a log file and install the Supplemental Stats Plugin. Check it out!</p>
EOD;
echo outputInfoBox("entry13", "Updates for May 21, 2011", $s);
?>

<br/>

<?php
$s = <<<EOD
<h3>Fixed Uploading with Chrome 11</h3>
<p>Chrome 11 should now be able to upload. Thanks to $ShwanLink for bringing this issue to my attention.</p>
EOD;
echo outputInfoBox("entry12", "Updates for April 30, 2011", $s);
?>

<br/>

<?php
$snowieLink = link_to('-9m- snowie', '@player_by_numeric_steamid?id=76561197976448876');
$s = <<<EOD
<h3>Fixed Some Bugs</h3>
<p>Humiliation rounds no longer count toward playable minutes or any stats. Thanks to $snowieLink for pointing that out. Due to the sheer number of logs that have been uploaded, I will have to work out a new strategy to get all of the old logs to regenerate and get the new stats. New logs that are uploaded will have the updated numbers.</p>
<p>The front page now caches every 5 minutes. When downloading a log file, the filename will default to tf2logs_id.log, where id is the number for the log (ie. tf2logs.com/logs/1 would download as tf2logs_1.log by default).</p>
<p>Updates will continue to be slow. I'm working on a big addition to the site, and it is also getting nice outside. While updates will be slow, the site should be in good working order in the meantime.</p>
EOD;
echo outputInfoBox("entry11", "Updates for April 17, 2011", $s);
?>

<br/>

<?php
$s = <<<EOD
<h3>Added Chat Log Table, ctf_impact2 Support</h3>
<p>Some league admins requested the ability to show the chat messages for a log in a table, even if the log does not have a map that is supported. Coming soon, the capture times will be output, but I need to rearrange some stuff in the backend before I want to go ahead with that. Also added is Log Viewer support for the ctf_impact2 map, which means that all maps in the UGC Highlander Regular Season are now supported!</p>
EOD;
echo outputInfoBox("entry10", "Updates for April 1, 2011", $s);
?>

<br/>

<?php
$tzakaru = link_to('fFww.Tzakaru', '@player_by_numeric_steamid?id=76561198004585626');
$s = <<<EOD
<h3>Fixed Upload Issues with Firefox 4</h3>
<p>I was able to (hopefully) fix issues for users uploading log files using the new Firefox 4 browser. If you are still having issues, try refreshing the page. Thanks to $tzakaru for bringing this issue to my attention.</p>
EOD;
echo outputInfoBox("entry9", "Updates for March 31, 2011", $s);
?>

<br/>

<?php
$annuitLink = link_to('Annuit Coeptis', '@player_by_numeric_steamid?id=76561197983800058');
$s = <<<EOD
<h3>Fixed a few Issues and Added some Docs</h3>
<p>Did numerous fixes around the site. The biggest addition though is some documentation at the FAQ link at the top left (still a work in progress). Also added some help buttons on the log view page to help clarify what is going on.</p>
EOD;
echo outputInfoBox("entry8", "Updates for March 26, 2011", $s);
?>

<br/>

<?php
$annuitLink = link_to('Annuit Coeptis', '@player_by_numeric_steamid?id=76561197983800058');
$s = <<<EOD
<h3>Fixed Some Issues with Corrupt Lines and Map Recognition</h3>
<p>There seem to be more cases where line breaks are being erroneously entered into the log than expected. When coming upon one of these lines, the site would just reject the whole log file. Now, only the offending line will be ignored.</p>
<p>There is also a fix to a somewhat hidden feature - automatic map recognition. TF2 will log what map the current log file is for, but generally too soon for it to show up in the log that gets saved. This will be something that will be added to the <a href="$statpagehref">Supplemental Stats Plugin</a>. But, sometimes it will get saved to the log. Originally, the site would not recognize this line outside of the tournament mode - now it does.</p>
<p>Thanks to $annuitLink for bringing these issues to my attention.</p>
EOD;
echo outputInfoBox("entry7", "Updates for March 25, 2011", $s);
?>

<br/>

<?php
$log314Link = link_to('Here is a sample log with these new features added.', '@log_by_id?id=314');
$s = <<<EOD
<h3>Multiple Features Added, Bugs Removed</h3>
<p>There were some small, minor bugfixes, and you will probably not notice them. There have also been some updates to the <a href="$statpagehref">Supplemental Stats Plugin</a> by Cinq and Annuit Coeptis, which has just been released! These updates include tracking game pauses within the log, tracking how much healing is done per player, and what items, such as medkits, were picked up. Because pause logging is included, each log now has Playable Time calculated, which is the total time for the game, without time for pauses (if they are in the log) and without time between halves. This allows for the calculation of Per Minute stats, along with their Per Death counterparts.</p> <p>$log314Link</p>
EOD;
echo outputInfoBox("entry6", "Updates for March 20, 2011", $s);
?>

<br/>

<?php
$s = <<<EOD
<h3>Added Support for New Maps</h3>
<p>I have added Log Viewer support for cp_steel, pl_badwater, and cp_dustbowl.</p>
EOD;
echo outputInfoBox("entry5", "Updates for March 15, 2011", $s);
?>

<br/>

<?php
$s = <<<EOD
<h3>Added Support for Cinq's Damage Plugin</h3>
<p>Cinq has made a <a href="$statpagehref">damage plugin</a>. There will be some revisions of this, but TF2Logs.com will maintain support for this feature.</p>
EOD;
echo outputInfoBox("entry4", "Updates for March 13, 2011", $s);
?>

<br/>

<?php
$s = <<<EOD
<h3>Mirroring on Certain Maps</h3>
<p>Some map images, like pl_goldrush, cp_gravelpit, and pl_upward were mirrored making them hard to read. This is now fixed. (Hit refresh in your browser if you do not see a change.)</p>
EOD;
echo outputInfoBox("entry3", "Updates for March 10, 2011", $s);
?>

<br/>

<?php
$bizLink = link_to('Biz', '@player_by_numeric_steamid?id=76561198004686658');
$s = <<<EOD
<h3>Disconnected Due to No Steam Login - Throwing Error</h3>
<p>It appears that a log line for a user that disconnects due to a "No Steam logon" has a line break which causes a fragment of the line to go to another line. The parser would then cause an error on this line because it is invalid. The parser will now ignore this corrupted line. (Thanks to $bizLink for helping me fix this issue)</p>
EOD;
echo outputInfoBox("entry2", "Updates for March 9, 2011", $s);
?>

<br/>

<?php
$seekerLink = link_to('seeker', '@player_by_numeric_steamid?id=76561197972521134');
$s = <<<EOD
<p>So, I whipped this page up real quick to let you all know what changes I have been making. This page is somewhat a work in progress. Onward with the changes:</p>
<h3>Fixed issue with Dead Ringer Deaths being Counted</h3>
<p>Before, Dead Ringer spies that "die" were being tracked for kills, deaths and assists, which in some cases would significantly inflate the numbers. All existing logs have been re-generated to update the numbers from this change.</p>
<h3>Logs with Illegal Chars</h3>
<p>There was a small issue where some character's in a player's name could not be handled properly (not just in the log parser itself, but some text editors even had a hard time with them). These characters have been replaced with the '?' character. (Thanks to $seekerLink for helping me fix this issue)</p>
<h3>Top Viewed - Only for Past Week</h3>
<p>On the front page, the Top Viewed box looked like it was going to do nothing but show some of my demonstration log files, so I changed it so that it will only show the Top Viewed logs for logs added in the past week.</p>
<br/>
<p>There were also some minor bugfixes.</p>
EOD;
echo outputInfoBox("entry1", "Updates for March 8, 2011", $s);
?>

</div>
