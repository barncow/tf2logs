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
  <?php use_javascript('loguploader.js'); ?>
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
