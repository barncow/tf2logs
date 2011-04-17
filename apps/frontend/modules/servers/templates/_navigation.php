<div id="serverNavigationBar" class="ui-corner-all">
<ul>
  <li>
  <?php echo link_to($serverGroup->getName(), '@server_main?group_slug='.$serverGroup->getSlug()) ?>
  <?php if(isset($server) && $server && $serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER): ?>
    -> <?php echo link_to($server->getName(), '@server_main_group?group_slug='.$serverGroup->getSlug().'&server_slug='.$server->getSlug()) ?>
  <?php endif ?>
  :</li>
  <li>Player Search</li>
  <li>Log Search</li>
  
  <?php if($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_SINGLE_SERVER): ?>
    <li><?php echo link_to('Server Status', '@server_single_status?server_slug='.$serverGroup->getSlug()) ?></li>
    <?php
      $servers = $serverGroup->getServers();
      $server = $servers[0];
      if($server->getLiveLogId()) {
        echo link_to('<strong>LIVE</strong>', '@server_single_live?server_slug='.$server->getSlug());
    } ?>
    
    <?php if($sf_user->doesUserOwn($serverGroup->getOwnerPlayerId())): ?>
      <li><?php echo link_to('Edit', '@server_single_edit?group_slug='.$serverGroup->getSlug()) ?></li>
    <?php endif ?>
  <?php elseif($serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER): ?>
    <?php if(isset($server) && $server): ?>
      <li><?php echo link_to('Server Status', '@server_multi_status?group_slug='.$serverGroup->getSlug().'&server_slug='.$server->getSlug()) ?></li>
      <?php if($server->getLiveLogId()) {
          echo link_to('<strong>LIVE</strong>', '@server_multi_live?server_slug='.$server->getSlug().'&group_slug='.$serverGroup->getSlug());
      } ?>
      <?php if($sf_user->doesUserOwn($serverGroup->getOwnerPlayerId())): ?>
        <li><?php echo link_to('Edit', '@server_multi_edit?group_slug='.$serverGroup->getSlug().'&server_slug='.$server->getSlug()) ?></li>
      <?php endif ?>
    <?php else: ?>
      <li><?php echo link_to('Edit', '@server_single_edit?group_slug='.$serverGroup->getSlug()) ?></li>
    <?php endif ?>
  <?php endif ?>
  
  
</ul>
</div>
