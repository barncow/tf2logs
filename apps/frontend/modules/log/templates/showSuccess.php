<?php use_helper('Log') ?>
<div id="logName"><?php echo $log->getName() ?></div>

<div id="score">
  <span class="red teamName">Red</span> <span class="red"><?php echo $log->getRedscore() ?></span>
   <span class="winSeparator"><?php echo getWinSeparator($log->getRedscore(), $log->getBluescore()) ?></span> 
   <span class="blue teamName">Blue</span> <span class="blue"><?php echo $log->getBluescore() ?></span>
</div>

Created  <?php echo $log->getCreatedAt() ?><br/>
Last Generated <?php echo $log->getUpdatedAt() ?> 
     
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Steam ID</th>
      <th>Team</th>
      <th>Kills</th>
      <th>Assists</th>
      <th>Deaths</th>
      <th>Kills/Death</th>
      <th>Longest Kill Streak</th>
      <th>Capture Points Blocked</th>
      <th>Capture Points Captured</th>
      <th>Dominations</th>
      <th>Times Dominated</th>
      <th>Revenges</th>
      <th>Built Objects</th>
      <th>Destroyed Objects</th>
      <th>Extinguishes</th>
      <th>Ubers</th>
      <th>Ubers/Death</th>
      <th>Dropped Ubers</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($log->getStats() as $stat): ?>
    <tr>
      <td><?php echo $stat->getName() ?></td>
      <td><?php echo $stat->getPlayer()->getSteamid() ?></td>
      <td><?php echo $stat->getTeam() ?></td>
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
