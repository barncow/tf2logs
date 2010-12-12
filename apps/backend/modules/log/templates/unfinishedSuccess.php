Here is a list of log files that are uploaded with errors.
<table border="0" cellspacing="5" cellpadding="0">
  <tbody>
  <?php if(count($logs) == 0): ?>
    <tr><td>No Logs with errors found.</td></tr>
  <?php endif ?>
  <?php foreach($logs as $l): ?>
      <tr><td><?php echo $l->getId()." - ". $l->getErrorLogName() ?> <a href="<?php echo url_for('log/regenerateUnfinished?id='.$l->getId()) ?>">Regenerate</a> <?php echo link_to('Delete', 'log/delete?id='.$l->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?></td></tr>
      <tr><td>Exception: <?php echo $l->getErrorException() ?></td></tr>
  <?php endforeach ?>
  </tbody>
</table>
