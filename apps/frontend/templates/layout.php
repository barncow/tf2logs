<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
  <div id="header"><a href="<?php echo url_for('@homepage', true) ?>">tf2logs.com</a></div>
  <div id="headerLogin">
    <?php if($sf_user->isAuthenticated()): ?>
      <?php echo link_to('Logout', '@logout') ?>
    <?php else: ?>
      To upload a log, login through STEAM<br/><a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid.png') ?></a>
    <?php endif ?>
  </div>
  <div style="clear: both;"></div>
    
    <?php echo $sf_content ?>
    
    <div class="footer"><a href="http://steampowered.com">Powered by Steam</a></div>
  </body>
   <?php include_stylesheets() ?>
   <?php include_javascripts() ?>
</html>
