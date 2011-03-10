<?php 
$sf_response->setTitle("What's New - TF2Logs.com");
use_helper('PageElements');
?>

<div id="whatsNewContainer">

<?php
$bizLink = link_to('Biz', '@player_by_numeric_steamid?id=76561198004686658');
$s = <<<EOD
<h3>Disconnected Due to No Steam Login - Throwing Error</h3>
<p>It appears that a log line for a user that disconnects due to a "No Steam logon" has a line break which causes a fragment of the line to go to another line. The parser would then cause an error on this line because it is invalid. The parser will now ignore this corrupted line. (Thanks to $bizLink for helping me fix this issue)</p>
EOD;
echo outputInfoBox("entry1", "Updates for March 9, 2010", $s);
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
echo outputInfoBox("entry1", "Updates for March 8, 2010", $s);
?>

</div>
