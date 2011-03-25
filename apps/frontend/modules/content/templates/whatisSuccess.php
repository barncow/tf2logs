<?php
$sf_response->setTitle('What is TF2Logs.com? - TF2Logs.com'); 
use_helper('PageElements');
$s = <<<EOD
<p>TF2Logs.com is a Team Fortress 2 server log parser. When you play games in TF2, such as 6v6 or Highlander, the server will output a log file into the tf/logs folder of the server, and will have a name similar to l03454.log. The filesize will also be a few hundred kilobytes, but probably will not be too much more than 600kB (although it could be bigger).</p>
<p>You can take that log from the server, and upload it to this website and get stats such as kills and deaths, plus if you specify what map the log is from, and TF2Logs.com supports it, you can see your kills and deaths on an overhead image of that map.</p>
EOD;
echo '<div id="whatIsTF2LogsContainer">';
echo outputInfoBox("whatislogs", "What is TF2Logs.com?", $s);
echo '</div><br class="hardSeparator"/>';