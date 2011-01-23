<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_stylesheet('./mint-choc/jquery-ui-1.8.9.custom.css'); ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<?php use_helper('Log') ?>

<?php if(!$sf_user->isAuthenticated()): ?>
<div id="pageLogin">
  In order to upload a log file, you must login through STEAM.<br/>It is quick and safe.<br/>You can browse the site without logging in.<br/>
  <a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid.png') ?></a>
</div>
<?php endif ?>


<?php if($sf_user->isAuthenticated()): ?>
<div id="uploadForm">
  <form action="<?php echo url_for('log/add') ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
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

<div id="recentlyAdded" class="statBox">
<span class="title">Recently Added</span>
<ul>
  <?php foreach($logs as $l): ?>
    <li><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></li>
  <?php endforeach ?>
</ul>
</div>

<div id="topUploaders" class="statBox">
<span class="title">Top Uploaders</span>
<ol>
  <?php foreach($topuploaders as $p): ?>
    <li><?php echo link_to($p->name, 'player/showNumericSteamId?id='.$p->numeric_steamid) ?> - <?php echo $p->num_logs ?></li>
  <?php endforeach ?>
</ol>
</div>

<script type="application/x-javascript">
$(function(){
  $("#log_map_name").autocomplete({
    source: <?php echo outputAsJSArray($mapNames); ?>
  });
});
</script>
