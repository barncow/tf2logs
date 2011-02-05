<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheet('jquery-ui-1.8.9.custom.css'); ?>

<?php use_helper('Log') ?>

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
  <?php use_javascript('jquery-1.4.4.min.js'); ?>
  <?php use_stylesheet('jquery.ui.plupload.css'); ?>
  <?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
  <?php use_javascript('plupload.min.js'); ?>
  <?php use_javascript('plupload.html5.min.js'); ?>
  <?php use_javascript('plupload.html4.min.js'); ?>
  <?php use_javascript('jquery.ui.plupload.min.js'); ?>
  <?php use_javascript('loguploader.js'); ?>
  <div id="uploader">
    <form action="<?php echo url_for('log/add') ?>" method="post" enctype="multipart/form-data">
      <table>
        <?php echo $form->renderGlobalErrors() ?>
        <?php echo $form ?>
        <tr>
          <td colspan="2">
            <input type="submit" />
          </td>
        </tr>
      </table>
    </form>
  </div>
<?php endif ?>
<br/>
<div id="infoBoxContainer">
  <div id="recentlyAdded" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Recently Added</div>
    <div class="content">
      <table width="100%">
      <?php foreach($logs as $l): ?>
        <tr><td><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></td><td><?php echo $l->getCreatedAt() ?></td></tr>
      <?php endforeach ?>
      </table>
    </div>
    <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
  </div>
  
  <div id="actions" class="infoBox">
    <div class="ui-widget ui-widget-header ui-corner-top header">Actions</div>
    <div class="content">
      <ul>
        <li><?php echo link_to("Search Logs", '@log_search') ?></li>
        <li><?php echo link_to("Search Players", '@player_search') ?></li>
      </ul>
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
var ACSource = {
  source: <?php echo outputAsJSArray($mapNames); ?>
};
</script>
