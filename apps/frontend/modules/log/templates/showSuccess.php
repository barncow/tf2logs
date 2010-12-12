<?php use_helper('Log') ?>
<?php use_helper('Implode') ?>
<?php use_stylesheet('jquery.tooltip.css'); ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_javascript('jquery.dimensions.js'); ?>
<?php use_javascript('jquery.tooltip.min.js'); ?>
<?php use_javascript('logshow.js'); ?>
<div id="logName"><?php echo $log->getName() ?></div>

<div id="score">
  <span class="Red teamName">Red</span> <span class="red"><?php echo $log->getRedscore() ?></span>
   <span class="winSeparator"><?php echo getWinSeparator($log->getRedscore(), $log->getBluescore()) ?></span> 
   <span class="Blue teamName">Blue</span> <span class="blue"><?php echo $log->getBluescore() ?></span>
</div>
     
<table id="statPanel" border="0" cellspacing="0" cellpadding="3">
  <thead>
    <tr>
      <th>Name</th>
      <th>Steam ID</th>
      <th title="Classes">C</th>
      <th title="Weapons">W</th>
      <th title="Kills">K</th>
      <th title="Assists">A</th>
      <th title="Deaths">D</th>
      <th title="Kills/Death">KPD</th>
      <th title="Longest Kill Streak">LKS</th>
      <th title="Capture Points Blocked">CPB</th>
      <th title="Capture Points Captured">CPC</th>
      <th title="Dominations">DOM</th>
      <th title="Times Dominated">TDM</th>
      <th title="Revenges">R</th>
      <th title="Built Objects">BO</th>
      <th title="Destroyed Objects">DO</th>
      <th title="Extinguishes">E</th>
      <th title="Ubers">U</th>
      <th title="Ubers/Death">UPD</th>
      <th title="Dropped Ubers">DU</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($log->getStats() as $stat): ?>
    <tr class="<?php echo $stat->getTeam() ?>">
      <td><?php echo $stat->getName() ?></td>
      <td><?php echo $stat->getPlayer()->getSteamid() ?></td>
      <td><?php echo implodeCollection($stat->getRoles(), "name", "key_name") ?></td>
      <td><?php echo implodeCollection($stat->getWeapons(), "name", "key_name") ?></td>
      <td><?php echo $stat->getKills() ?></td>
      <td><?php echo $stat->getAssists() ?></td>
      <td><?php echo $stat->getDeaths() ?></td>
      <td><?php echo $stat->getKillsPerDeath() ?></td>
      <td><?php echo $stat->getLongestKillStreak() ?></td>
      <td><?php echo $stat->getCapturePointsBlocked() ?></td>
      <td><?php echo $stat->getCapturePointsCaptured() ?></td>
      <td><?php echo $stat->getDominations() ?></td>
      <td><?php echo $stat->getTimesDominated() ?></td>
      <td><?php echo $stat->getRevenges() ?></td>
      <td><?php echo $stat->getBuiltObjects() ?></td>
      <td><?php echo $stat->getDestroyedObjects() ?></td>
      <td><?php echo $stat->getExtinguishes() ?></td>
      <td><?php echo $stat->getUbers() ?></td>
      <td><?php echo $stat->getUbersPerDeath() ?></td>
      <td><?php echo $stat->getDroppedUbers() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

Created  <?php echo $log->getCreatedAt() ?><br/>
<?php if($log->getCreatedAt() != $log->getUpdatedAt()): ?>
  Last Generated <?php echo $log->getUpdatedAt() ?> 
<?php endif ?>
