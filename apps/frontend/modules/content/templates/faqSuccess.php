<?php
$sf_response->setTitle('FAQ - TF2Logs.com'); 
use_helper('PageElements');

$whatsnewlink = link_to("What's New",'@plugins');
$suppstatslink = '<a href="'.url_for('@plugins').'#suppstats">Supplemental Stats SourceMod plugin</a>';

$s = <<<EOD
<ul>
<li><a href="#whatis">What is TF2Logs.com?</a></li>
<li>
  The Log Page
  <ul>
    <li>What is the Log Viewer?</li>
    <li>What is the Log Stats Table?</li>
    <li>What is the Heal Spread Table?</li>
    <li>What is the Medic Comparison Table?</li>
    <li>What is the Weapon Stats Table?</li>
    <li>What is the Player Stats Table?</li>
    <li>What is the Items Picked Up Table?</li>
  </ul>
</li>
</ul>

<a name="whatis"/>
<h3>What is TF2Logs.com?</h3>
<p>TF2Logs.com is a Team Fortress 2 server log parser. When you play games in TF2, such as 6v6 or Highlander, the server will output a log file into the tf/logs folder of the server, and will have a name similar to l03454.log. The filesize will also be a few hundred kilobytes, but probably will not be too much more than 600kB (although it could be bigger).</p>
<p>You can take that log from the server, and upload it to this website and get stats such as kills and deaths, plus if you specify what map the log is from, and TF2Logs.com supports it, you can see your kills and deaths on an overhead image of that map.</p>

<h3>The Log Page</h3>

<h4>What is the Log Viewer?</h4>
<p>The Log Viewer, when active, allows you to see the kills and deaths that occurred in the log on an overhead view of the map. The Log Viewer is only active when the user specifies a map for the log, or if not specified, is listed in the log file (which is rare, since the log file's save point generally starts after this line is output). The Log Viewer also will only be active when TF2Logs.com supports the map that was entered, if any. New maps are being added all the time, check the $whatsnewlink page to keep informed! TF2Logs.com will generally only support finalized maps due to the work involved in generating support for the map.</p>
<p>When the Log Viewer is active, you will first see an overhead view of the map used in the log. On the map, you will see circles indicating capture points and/or intelligence locations. The circles will be colored blue if the point is owned by the Blue team, or colored red if owned by the Red team. Gray coloring indicates that point is neutral. You can hover your mouse over the capture points to see what the point is called. If the point was captured by a team, the players that captured the point are listed as well.</p>
<p>On the bottom left corner of the map image, you will see red and blue numbers which represent the current score for each team at that point in the playback. The score information can move to another corner if they will block the map's playable area.</p>
<p>Next you will see a play/pause button, which will start or pause playback of the events in the log file. Next to it is a slider that indicates the current position of playback. You can also move the slider anywhere in the log to jump to a specific point quicker.</p>
<p>After the playback controls, you will see a box indicating the current playback speed. By default, it is 5x, or 5 times faster than real time. After that, you will see a button called cumulative which, when enabled, will allow you to see all the kills in the log file, up to the current playback position.</p>
<p>Lastly, there is a box that holds all of the chat comments during game play. This indicates the team by color of the person doing the chat, as well as elapsed time within the log that the chat was done. If the player has (team) after their name, it indicates that it was team-only communication. If it is not there, the chat message went to all players.</p>
  
<h4>What is the Log Stats Table?</h4>
<p>The log stats table lists the players that played in the log in the first column. The other columns show the player's stats for the log file. You can see what the acronym for the stat represents by hovering your mouse over the acronym.</p>
<p>The two stats DA (Damage) and DAPM (Damage per Minute) represent how much hurt the player inflicted on the other team. This stat allows a team to see who outputs the most damage, but not necessarily the most kills, thus indicating more accurate performance in the game. Damage stats are only available when using the $suppstatslink on the server that the log was recorded.</p>
  
<h4>What is the Heal Spread Table?</h4>
<p>The heal spread lists the players that played in the log in the first column. The other columns represent what healer did the healing, and how much healing they gave the player listed in the first column. This can help the players see how heals are spread around, and be able to make adjustments as needed.</p>
<p>You can see who the healer was by hovering your mouse over the player's avatar. You can also click on the avatar to sort the table.</p>
<p>This is only available when using the $suppstatslink on the server that the log was recorded.</p>

<h4>What is the Medic Comparison Table?</h4>
<p>The Medic Comparison table lists the medics for the log in the first column. The other columns show the medic's stats for the log file. You can see what the acronym for the stat represents by hovering your mouse of the acronym. This table is auto-sorted by all the stats in descending order, with the exception of DU (Dropped Ubers) which is sorted in ascending order (since less is better). It is an easy way for the medic to gauge their performance compared to the other team's medic.</p>

<h4>What is the Weapon Stats Table?</h4>
<p>The Weapon Stats table lists the players that played in the log in the first column. The weapons are listed by their in-game kill icon as you go across the table lengthwise. You can see the weapon name by hovering your mouse over the icon. The row just beneath the icons lists the kills (K) and deaths (D) for the player listed in the first column.</p>

<h4>What is the Player Stats Table?</h4>
<p>The Player Stats table lists all the players down the first column, and also lists them by their avatar going across the table. You can see who the player's avatar represents by hovering your mouse over the avatar which will show the name of the player. You can also sort the table by clicking on an avatar.</p>
<p>As you read the table lengthwise, you can see who that player killed, and how many times the other player was killed. As you read the table going down, you can see who killed that player, and how many times.</p>
  
<h4>What is the Items Picked Up Table?</h4>
<p>The Items Picked Up table lists the players that played in the log in the first column. The other columns represent what item was picked up, and how many times the player listed in the first column picked the item up.</p>
<p>You can see what the item was by hovering your mouse over the icon of the item. You can also click on the icons to sort the table.</p>
<p>This is only available when using the $suppstatslink on the server that the log was recorded.</p>
  
EOD;
echo '<div id="faqContainer">';
echo outputInfoBox("faq", "Frequently Asked Questions", $s);
echo '</div><br class="hardSeparator"/>';
