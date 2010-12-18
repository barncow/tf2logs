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

<div id="totalTime">Total Time: <span class="time"><?php echo outputSecondsToHumanFormat($log->getElapsedTime()) ?></span></div>
     
<table id="statPanel" class="statTable" border="0" cellspacing="0" cellpadding="3">
  <thead>
    <tr>
      <th>Name</th>
      <th>Steam ID</th>
      <th title="Classes">C</th>
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
      <td><?php echo link_to($stat->getName(), 'player/showNumericSteamId?id='.$stat->getPlayer()->getNumericSteamid()) ?></td>
      <td><?php echo $stat->getPlayer()->getSteamid() ?></td>
      <td>
        <ul>
          <?php foreach($stat->getRoleStats() as $rs): ?>
            <li><?php echo $rs->getRole()->getName() ?> - <?php echo outputSecondsToHumanFormat($rs->getTimePlayed()) ?></li>
          <?php endforeach ?>
        </ul>
      </td>
      <td class="<?php echo dataCellOutputClass($stat->getKills()) ?>"><?php echo $stat->getKills() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getAssists()) ?>"><?php echo $stat->getAssists() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getDeaths()) ?>"><?php echo $stat->getDeaths() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getKillsPerDeath()) ?>"><?php echo $stat->getKillsPerDeath() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getLongestKillStreak()) ?>"><?php echo $stat->getLongestKillStreak() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getCapturePointsBlocked()) ?>"><?php echo $stat->getCapturePointsBlocked() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getCapturePointsCaptured()) ?>"><?php echo $stat->getCapturePointsCaptured() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getDominations()) ?>"><?php echo $stat->getDominations() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getTimesDominated()) ?>"><?php echo $stat->getTimesDominated() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getRevenges()) ?>"><?php echo $stat->getRevenges() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getBuiltObjects()) ?>"><?php echo $stat->getBuiltObjects() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getDestroyedObjects()) ?>"><?php echo $stat->getDestroyedObjects() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getExtinguishes()) ?>"><?php echo $stat->getExtinguishes() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getUbers()) ?>"><?php echo $stat->getUbers() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getUbersPerDeath()) ?>"><?php echo $stat->getUbersPerDeath() ?></td>
      <td class="<?php echo dataCellOutputClass($stat->getDroppedUbers()) ?>"><?php echo $stat->getDroppedUbers() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Weapon Stats</caption>
  <thead>
    <tr>
      <th><!--playername--></th>
      <?php foreach($weapons as $w): ?>
        <th colspan="2">
          <?php if($w->getName()): ?>
            <?php echo $w->getName() ?>
          <?php else: ?>
            <?php echo $w->getKeyName() ?>
          <?php endif ?>
        </th>
      <?php endforeach ?>
    </tr>
    <tr>
      <th><!--playername--></th>
      <?php foreach($weapons as $w): ?>
        <th title="Kills">K</th>
        <th title="Deaths">D</th>
      <?php endforeach ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($log->getStats() as $stat): ?>
    <tr>
      <td><?php echo link_to($stat->getName(), 'player/showNumericSteamId?id='.$stat->getPlayer()->getNumericSteamid()) ?></td>
      <?php foreach($weapons as $w): ?>
        <?php $foundWS = false ?>
        <?php foreach($weaponStats as $ws): ?>
          <?php if($ws->getStatId() == $stat->getId() && $ws->getWeaponId() == $w->getId()): ?>
            <td class="<?php echo dataCellOutputClass($ws->num_kills) ?>"><?php echo $ws->num_kills ?></td>
            <td class="<?php echo dataCellOutputClass($ws->num_deaths) ?>"><?php echo $ws->num_deaths ?></td>
            <?php $foundWS = true ?>
            <?php break ?>
          <?php endif ?>
        <?php endforeach ?>
        <?php if(!$foundWS): ?>
          <td class="zeroValue">0</td>
          <td class="zeroValue">0</td>
        <?php endif ?>
      <?php endforeach ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Player Stats</caption>
  <thead>
    <tr>
      <th><!--playername--></th>
      <?php foreach($log->getStats() as $s): ?>
        <th colspan="2">
          <?php echo link_to($s->getName(), 'player/showNumericSteamId?id='.$s->getPlayer()->getNumericSteamid()) ?>
        </th>
      <?php endforeach ?>
    </tr>
    <tr>
      <th><!--playername--></th>
      <?php foreach($log->getStats() as $s): ?>
        <th title="Kills">K</th>
        <th title="Deaths">D</th>
      <?php endforeach ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($log->getStats() as $stat): ?>
    <tr>
      <td><?php echo link_to($stat->getName(), 'player/showNumericSteamId?id='.$stat->getPlayer()->getNumericSteamid()) ?></td>
      <?php foreach($log->getStats() as $colstat): ?>
        <?php $foundPS = false ?>
        <?php foreach($playerStats as $ps): ?>
          <?php if($ps->getStatId() == $stat->getId() && $ps->getPlayerId() == $colstat->getPlayer()->getId()): ?>
            <td class="<?php echo dataCellOutputClass($ps->num_kills) ?>"><?php echo $ps->num_kills ?></td>
            <td class="<?php echo dataCellOutputClass($ps->num_deaths) ?>"><?php echo $ps->num_deaths ?></td>
            <?php $foundPS = true ?>
            <?php break ?>
          <?php endif ?>
        <?php endforeach ?>
        <?php if(!$foundPS): ?>
          <td class="zeroValue">0</td>
          <td class="zeroValue">0</td>
        <?php endif ?>
      <?php endforeach ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

Created  <?php echo $log->getCreatedAt() ?><br/>
<?php if($log->getCreatedAt() != $log->getUpdatedAt()): ?>
  Last Generated <?php echo $log->getUpdatedAt() ?> 
<?php endif ?>
