Here is a list of log files that are uploaded with errors.
<table border="0" cellspacing="5" cellpadding="0">
  <tbody>
  <?php foreach($logs as $l): ?>
      <tr><td><?php echo $l->getId()." - ". $l->getErrorLogName() ?> <a href="<?php echo url_for('log/regenerateUnfinished?id='.$l->getId()) ?>">Regenerate</a></td></tr>
  <?php endforeach ?>
  </tbody>
</table>
