<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <?php use_helper('PageElements') ?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
  <div id="header" class="ui-corner-all">
    <div id="homeLink">
      <a href="<?php echo url_for('@homepage', true) ?>"><?php echo image_tag(getRandomLogoFilename()) ?></a>
    </div>
    <div id="userCP">
      <ul>
        <li><?php echo link_to('Log Search', '@log_search') ?></li>
        <li><?php echo link_to('Player Search', '@player_search') ?></li>
      <?php if($sf_user->isAuthenticated()): ?>
        <li><strong><?php echo link_to('Upload a Log', '@homepage') ?></strong></li>
        <li><?php echo link_to('Control Panel', '@controlpanel') ?></li>
        <li><?php echo link_to('Logout', '@logout') ?></li>
      <?php else: ?>
        <li><span class="subInfo"><strong>To upload a log, login through STEAM</strong></span><a class="fRight" href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid_bar.png') ?></a></li>
      <?php endif ?>
      </ul>
    </div>
    <br class="hardSeparator"/>
  </div>
  
  <?php if ($sf_user->hasFlash('notice')): ?>
    <div class="alertBox ui-state-highlight ui-corner-all"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><?php echo $sf_user->getFlash('notice') ?></div>
    <?php $sf_user->setFlash('notice', null) /*erasing flash */?>
  <?php endif ?>

  <?php if ($sf_user->hasFlash('error')): ?>
    <div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?php echo $sf_user->getFlash('error') ?></div>
    <?php $sf_user->setFlash('error', null) /*erasing flash */?>
  <?php endif ?>
    
    <?php echo $sf_content ?>
    
    <div id="footer">
      <div id="copyright"><a href="http://steampowered.com">Powered by Steam</a><br/>&copy; 2011 TF2Logs.com. Valve, the Valve logo, Steam, the Steam logo, Team Fortress, and the Team Fortress logo are trademarks and/or registered trademarks of Valve Corporation.</div>
    </div>
  </body>
  
</html>
