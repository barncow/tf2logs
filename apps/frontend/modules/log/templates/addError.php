<h1>Error Processing Log File</h1>

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="flash_notice"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="flash_error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>

<?php if (isset($logid) && $logid > 0): ?>
  When the issue is fixed, the log file will appear at the following address:
  <h3><a href="<?php echo url_for('log/show?id='.$logid, true) ?>"><?php echo url_for('log/show?id='.$logid, true) ?></a></h3>
<?php endif ?>
