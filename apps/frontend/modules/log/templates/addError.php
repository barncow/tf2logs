<?php $sf_response->setTitle('Log Upload Error - TF2Logs.com'); ?>
<h1>Error Processing Log File</h1>

<?php echo $form->renderGlobalErrors() ?>

<?php if (isset($logid) && $logid > 0): ?>
  When the issue is fixed, the log file will appear at the following address:
  <h3><a href="<?php echo url_for('log/show?id='.$logid, true) ?>"><?php echo url_for('log/show?id='.$logid, true) ?></a></h3>
<?php endif ?>
