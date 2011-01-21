<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<?php if(!$sf_user->isAuthenticated()): ?>
<div id="pageLogin">
  In order to upload a log file, you must login through STEAM.<br/>It is quick and safe.<br/>You can browse the site without logging in.<br/>
  <a href="<?php echo url_for('@autoLogin') ?>"><?php echo image_tag('steam_openid.png') ?></a>
</div>
<?php endif ?>


<?php if($sf_user->isAuthenticated()): ?>
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
<?php endif ?>

<div id="recentlyAdded">
Recently Added
<ul>
  <?php foreach($logs as $l): ?>
    <li><?php echo link_to($l->getName(), 'log/show?id='.$l->getId()) ?></li>
  <?php endforeach ?>
</ul>
</div>
