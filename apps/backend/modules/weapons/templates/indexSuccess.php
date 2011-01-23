<table class="massEdit" border="0" cellspacing="5" cellpadding="0">
  <thead>
    <th></th>
    <th>Name</th>
    <th>Key</th>
    <th>Role</th>
    <th>Image</th>
    <th></th>
  </thead>
  <tbody>
    <?php echo $newWeapon->renderGlobalErrors() ?>
    <tr id="newWeaponForm">
      <form action="<?php echo url_for('weapons/create') ?>" method="post">
        <?php echo $newWeapon->renderHiddenFields(false) ?>
        <td></td>
        <td>
          <?php echo $newWeapon['name']->renderError() ?>
          <?php echo $newWeapon['name'] ?>
        </td>
        <td>
          <?php echo $newWeapon['key_name']->renderError() ?>
          <?php echo $newWeapon['key_name'] ?>
        </td>
        <td>      
          <?php echo $newWeapon['role_id']->renderError() ?>
          <?php echo $newWeapon['role_id'] ?>
        </td>
        <td>      
          <?php echo $newWeapon['image_name']->renderError() ?>
          <?php echo $newWeapon['image_name'] ?>
        </td>
        <td>
          <input type="submit" value="Save New Weapon"/>
        </td>
      </form>
    </tr>
    <?php foreach($weaponsForms as $weapon): ?>
      <tr clas="updateWeaponForm">
        <form action="<?php echo url_for('weapons/update') ?>" method="post">
          <?php echo $weapon->renderHiddenFields(false) ?>
          <input type="hidden" name="sf_method" value="put" />
          <input type="hidden" name="id" value="<?php echo $weapon->getObject()->getId() ?>" />
          <?php echo $weapon->renderGlobalErrors() ?>
          <td><?php echo link_to('Delete', 'weapons/delete?id='.$weapon->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?></td>
          <td>
            <?php echo $weapon['name']->renderError() ?>
            <?php echo $weapon['name'] ?>
          </td>
          <td>
            <?php echo $weapon['key_name']->renderError() ?>
            <?php echo $weapon['key_name'] ?>
          </td>
          <td>      
            <?php echo $weapon['role_id']->renderError() ?>
            <?php echo $weapon['role_id'] ?>
          </td>
          <td>      
            <?php echo $weapon['image_name']->renderError() ?>
            <?php echo $weapon['image_name'] ?>
          </td>
          <td>
            <input type="submit" value="Save"/>
          </td>
        </form>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
