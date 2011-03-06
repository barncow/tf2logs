<?php 
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
$sf_response->setTitle('Welcome - TF2Logs.com'); 
use_stylesheet('jquery-ui-1.8.9.custom.css'); 
use_helper('Log');
use_helper('PageElements');
use_javascript('jquery.qtip.min.20110205.js'); 
use_stylesheet('jquery.qtip.min.20110205.css'); 
use_javascript('autocompletehelper.js');
?>

<?php if(!$sf_user->isAuthenticated()): ?>
<div id="pageLogin" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Login to Upload</div>
    <div class="content">
     In order to upload a log file, you must login through STEAM.<br/>It is quick and safe.<br/>You can browse the site without logging in.<br/>
     <a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid.png') ?></a>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<br class="hardSeparator"/>
<?php endif ?>

<?php if($sf_user->isAuthenticated()): ?>
  <?php use_stylesheet('jquery.ui.plupload.css'); ?>
  <?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
  <?php use_javascript('plupload.min.js'); ?>
  <?php use_javascript('plupload.html5.min.js'); ?>
  <?php use_javascript('plupload.html4.min.js'); ?>
  <?php use_javascript('jquery.ui.plupload.min.js'); ?>
  <?php use_javascript('loguploader.min.js'); ?>
  <?php use_javascript('jquery.infieldlabel.min.js'); ?>
  <div id="uploader">
    <?php 
    $url = url_for('log/add');
    $s = <<<EOD
    To upload a log file, choose a file below. You can also optionally specify a name, which will overwrite the file name that is uploaded. Also, you can optionally specify a Map Name. This identifies what map it is so that it can searched on, and the log parser can display the events happening on screen.
    <form action="$url" method="post" enctype="multipart/form-data">
      <table>
        {$form->renderGlobalErrors()}
        {$form->renderHiddenFields()}
        <tr>
          <th>{$form['name']->renderLabel()}</th>
          <td class="txtleft">
            {$form['name']->renderError()}
            {$form['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
          </td>
        </tr>
        <tr>
          <th>{$form['map_name']->renderLabel()}</th>
          <td class="txtleft">
            {$form['map_name']->renderError()}
            {$form['map_name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
          </td>
        </tr>
        <tr>
          <th>{$form['logfile']->renderLabel()}</th>
          <td class="txtleft">
            {$form['logfile']->renderError()}
            {$form['logfile']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
          </td>
        </tr>
        <tr>
          <td colspan="2" class="center">
            <input type="submit" value="Upload" class="ui-state-default"/>
          </td>
        </tr>
      </table>
    </form>
EOD;
echo '<div id="infoBoxContainer">';
echo outputInfoBox("uploadForm", "Upload Log", $s);
echo '</div><br class="hardSeparator"/>';
?>
  </div>
  <div id="helpMessage" title="Uploading a Log">
    <h3>What kind of files are supported?</h3>
    <p>This log parser only takes TF2 <strong>server</strong> logs, and will calculate stats from them. If you specify a map for the log file, and are using either Google Chrome, Mozilla Firefox, or another modern browser, it will also show kills on screen. While any TF2 log file can be added, this is intended for competitive formats, such as 6v6 and Highlander.</p>
    
    <h3>Do I need to do any editing of the log before I upload?</h3>
    <p>
      Theoretically, no. The log parser tries to take the log file as given, and will find where the game starts and ends. If there is garbage data in the file, which can happen if the server log is left running while everyone leaves, for instance, it can make the file size very large. This should be trimmed. However, the log parser will try to detect if something is wrong and will stop parsing. Typical log files will be about a few hundred kilobytes, probably not much more than 600kB.
    </p>
    
    <h3>Do I need to trim any pre-game events?</h3>
    <p>
      You can if you want, however the parser will try to find the beginning of the game by finding a line that looks similar to this:
      <code>L 02/21/2011 - 19:12:40: World triggered "Round_Start"</code>
      The idea is that the parser will automatically ignore any pre-game events, to make the stats more accurate. If the log file does not have this line, then the whole log file is treated as one round.
    </p>
    
    <h3>How is the end of a game determined?</h3>
    <p>
      The end of the the game is determined by an event that may look similar to this: 
      <code>L 02/21/2011 - 19:42:46: World triggered "Game_Over" reason "Reached Time Limit"</code>
      However, only the last "Game_Over" event will be used to determine when the game is over.
      If the parser finds the end of the file, or if the data seems corrupt, the parser will consider that the end of the game.
    </p>
    
    <h3>Does the parser filter out stats between halves?</h3>
    <p>The parser will not count any stats between a "Game_Over" event (shown above) and the next "Round_Start" event (also shown above). The parser will detect any team switches during this period, however.</p>
    
    <h3>Is there any private data in the logs?</h3>
    <p>The raw log file does contain IP addresses, rcon commands, and SourceMod (and similar mods) commands. The log parser will change any IP address to a dummy IP (in order to preserve formatting), and any rcon or SourceMod commands that come through will be stripped entirely from the log file.</p>
  </div>
<?php endif ?>
<br/>
<div id="infoBoxContainer">
  <div id="recentlyAdded" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Recently Added</div>
    <div class="content">
      <table width="100%">
      <?php foreach($logs as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td style="white-space: nowrap;"><?php echo getHumanReadableDate($l->getDateTimeObject('created_at')) ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>

  <div id="topUploaders" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Top Uploaders</div>
    <div class="content">
      <table width="100%">
        <?php foreach($topuploaders as $p): ?>
          <tr><td><?php echo link_to($p->name, 'player/showNumericSteamId?id='.$p->numeric_steamid) ?></td><td><?php echo $p->num_logs ?></td></tr>
        <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
  <br class="hardSeparator"/>
</div>

<script type="application/x-javascript">
var uploadurl = "<?php echo url_for('log/add') ?>";
var csrftoken = "<?php echo $form->getCSRFToken() ?>";
ACSource.source = <?php echo outputAsJSArray($mapNames); ?>;
</script>
