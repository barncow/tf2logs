<h1>Control Panel</h1>
<?php if($sf_user->hasCredential('owner')): ?>
  <h2>Owner Actions</h2>
  <ul>
    <li><a href="<?php echo url_for("weapons/index") ?>">Add/Edit Weapons</a></li>
    <li><a href="<?php echo url_for("log/unfinished") ?>">Check Logs with Errors</a></li>
    <li>
      <form action="<?php echo url_for('log/regenerate') ?>" method="post">
        Regenerate Log ID: <input type="text" size="2" name="id"/> <input type="submit" value="Go!"/>
      </form>
    </li>
  </ul>
<?php endif ?>

<h2>User Actions</h2>
<ul>
  <li><a href="<?php echo url_for("authModule/logout") ?>">Logout</a></li>
</ul>
