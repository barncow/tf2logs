<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <?php use_stylesheet('main.css?date='.date('Ymd')); /* making it so that browsers only cache once per day */?>
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <?php use_helper('PageElements') ?>
    <?php if(strpos($_SERVER["HTTP_HOST"], "local") === false && strpos($_SERVER["HTTP_HOST"], "qa") === false): ?>
      <!--google analytics -->
      <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-21949486-1']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
      </script>
    <?php endif ?>
    
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
    <div class="wrapper">
      <div id="header" class="ui-corner-all">
        <div id="homeLink">
          <a href="<?php echo url_for('@homepage', true) ?>" id="homeLinkImage"><?php echo image_tag(getRandomLogoFilename()) ?></a>
          <?php echo link_to("FAQ", '@faq', array('id' => 'faqlink')) ?>
        </div>
        <div id="userCP">
          
          <ul>
            <li id="whatsNewLink"><?php echo link_to("What's New", '@whats_new') ?></li>
            <li><?php echo link_to('Log Search', '@log_search') ?></li>
            <li><?php echo link_to('Player Search', '@player_search') ?></li>
            <li><?php echo link_to("Plugins", '@plugins') ?></li>
          <?php if($sf_user->isAuthenticated()): ?>
            <li><strong><?php echo link_to('Upload a Log', '@homepage') ?></strong></li>
            <li><?php echo link_to('My TF2Logs', '@controlpanel', array('id' => 'mycplink')) ?></li>
            <li><?php echo link_to('Logout', '@logout') ?></li>
          <?php else: ?>
            <li><a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid_bar.png') ?></a></li>
          <?php endif ?>
          </ul>
          <br class="hardSeparator"/>
          <?php if(!$sf_user->isAuthenticated()): ?>
            <span class="subInfo fRight"><strong>To upload a log, sign in through STEAM</strong></span>
          <?php endif ?>
          
          <?php
            /****************** WHAT'S NEW UPDATE DATE *********************/
          ?>
          <span class="fLeft">Updated March 25</span>
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
      
      <div id="pushFooter"></div>
    </div>
    <div id="footer">
      <div id="copyright"><a href="http://steampowered.com">Powered by Steam</a><br/>&copy; 2011 TF2Logs.com. Valve, the Valve logo, Steam, the Steam logo, Team Fortress, and the Team Fortress logo are trademarks and/or registered trademarks of Valve Corporation. Icons and Game Overhead Images provided by Valve Corporation. Site maintained by <?php echo link_to('Barncow', 'player/showNumericSteamId?id=76561197993228277') ?>.</div>
    </div>
  </body>
  
</html>
