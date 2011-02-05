<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
  <div id="header" class="ui-corner-all">
    <div id="homeLink">
      <a href="<?php echo url_for('@homepage', true) ?>">tf2logs.com</a>
    </div>
    <div id="userCP">
      <?php if($sf_user->isAuthenticated()): ?>
        <?php echo link_to('Logout', '@logout') ?>
      <?php else: ?>
        To upload a log, login through STEAM<a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid.png') ?></a>
      <?php endif ?>
    </div>
    <br class="hardSeparator"/>
  </div>
  
  <?php if ($sf_user->hasFlash('notice')): ?>
    <div class="alertBox ui-state-highlight ui-corner-all"><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><?php echo $sf_user->getFlash('notice') ?></div>
  <?php endif ?>

  <?php if ($sf_user->hasFlash('error')): ?>
    <div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><?php echo $sf_user->getFlash('error') ?></div>
  <?php endif ?>
    
    <?php echo $sf_content ?>
    
    <div class="footer"><a href="http://steampowered.com">Powered by Steam</a></div>
  </body>
  
</html>
