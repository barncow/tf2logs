<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php if ($sf_user->hasFlash('notice')): ?>
  <div class="flash_notice"><?php echo $sf_user->getFlash('notice') ?></div>
<?php endif ?>

<?php if ($sf_user->hasFlash('error')): ?>
  <div class="flash_error"><?php echo $sf_user->getFlash('error') ?></div>
<?php endif ?>

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

<div id="recentlyAdded">
Recently Added
<ul>
  <?php foreach($logs as $l): ?>
    <li><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></li>
  <?php endforeach ?>
</ul>
</div>
