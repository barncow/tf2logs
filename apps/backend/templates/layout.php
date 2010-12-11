<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
  </head>
  <body>
    <?php if ($sf_user->hasFlash('notice')): ?>
      <div class="flash_notice"><?php echo $sf_user->getFlash('notice') ?></div>
    <?php endif ?>

    <?php if ($sf_user->hasFlash('error')): ?>
      <div class="flash_error"><?php echo $sf_user->getFlash('error') ?></div>
    <?php endif ?>
    
    <?php echo $sf_content ?>
  </body>
</html>
