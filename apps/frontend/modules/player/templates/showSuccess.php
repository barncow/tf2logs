<?php use_helper('Implode') ?>
<?php use_stylesheet('jquery.tooltip.css'); ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_javascript('jquery.dimensions.js'); ?>
<?php use_javascript('jquery.tooltip.min.js'); ?>
<?php use_javascript('playershow.js'); ?>
<?php use_helper('Log') ?>
<div id="playerName"><span class="description">Player Name: </span><?php echo $player->name ?></div>

<?php if($player->num_matches == 0): ?>
  <tr><td colspan="3">This player has not played in any logs.</td></tr>
<?php else: ?>
<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Overall Stats</caption>
  <thead>
    <tr>
      <th title="Kills">K</th>
      <th title="Assists">A</th>
      <th title="Deaths">D</th>
      <th title="Kills/Death">KPD</th>
      <th title="Damage">DA</th>
      <th title="Longest Kill Streak">LKS</th>
      <th title="Capture Points Blocked">CPB</th>
      <th title="Capture Points Captured">CPC</th>
      <th title="Dominations">DOM</th>
      <th title="Times Dominated">TDM</th>
      <th title="Revenges">R</th>
      <th title="Extinguishes">E</th>
      <th title="Ubers">U</th>
      <th title="Ubers/Death">UPD</th>
      <th title="Dropped Ubers">DU</th>
      <th title="Healing">H</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="<?php echo dataCellOutputClass($player->kills) ?>"><?php echo $player->kills ?></td>
      <td class="<?php echo dataCellOutputClass($player->assists) ?>"><?php echo $player->assists ?></td>
      <td class="<?php echo dataCellOutputClass($player->deaths) ?>"><?php echo $player->deaths ?></td>
      <td class="<?php echo dataCellOutputClass($player->kills_per_death) ?>"><?php echo $player->kills_per_death ?></td>
      <td class="<?php echo dataCellOutputClass($player->damage) ?>"><?php echo $player->damage ?></td>
      <td class="<?php echo dataCellOutputClass($player->longest_kill_streak) ?>"><?php echo $player->longest_kill_streak ?></td>
      <td class="<?php echo dataCellOutputClass($player->capture_points_blocked) ?>"><?php echo $player->capture_points_blocked ?></td>
      <td class="<?php echo dataCellOutputClass($player->capture_points_captured) ?>"><?php echo $player->capture_points_captured ?></td>
      <td class="<?php echo dataCellOutputClass($player->dominations) ?>"><?php echo $player->dominations ?></td>
      <td class="<?php echo dataCellOutputClass($player->times_dominated) ?>"><?php echo $player->times_dominated ?></td>
      <td class="<?php echo dataCellOutputClass($player->revenges) ?>"><?php echo $player->revenges ?></td>
      <td class="<?php echo dataCellOutputClass($player->extinguishes) ?>"><?php echo $player->extinguishes ?></td>
      <td class="<?php echo dataCellOutputClass($player->ubers) ?>"><?php echo $player->ubers ?></td>
      <td class="<?php echo dataCellOutputClass($player->ubers_per_death) ?>"><?php echo $player->ubers_per_death ?></td>
      <td class="<?php echo dataCellOutputClass($player->dropped_ubers) ?>"><?php echo $player->dropped_ubers ?></td>
      <td class="<?php echo dataCellOutputClass($player->healing) ?>"><?php echo $player->healing ?></td>
    </tr>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Classes</caption>
  <thead>
    <tr>
      <th>Class Name</th>
      <th>Number of Times Used</th>
      <th>Total Time Played</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($roles as $r): ?>
      <tr>
        <td><?php echo $r->name ?></td>
        <td><?php echo $r->num_times ?></td>
        <td><?php echo outputSecondsToHumanFormat($r->time_played) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Weapon Stats</caption>
  <thead>
    <tr>
      <th>Weapon</th>
      <th>Kills</th>
      <th>Damage</th>
      <th>Deaths</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($weapons as $w): ?>
      <tr>
        <td><?php echo outputWeapon($w['name'], $w['key_name'], $w['image_name']) ?></td>
        <?php $foundWS = false ?>
        <?php foreach($weaponStats as $ws): ?>
          <?php if($ws->getWeaponId() == $w['id']): ?>
            <td class="<?php echo dataCellOutputClass($ws->num_kills) ?>"><?php echo $ws->num_kills ?></td>
            <td class="<?php echo dataCellOutputClass($ws->damage) ?>"><?php echo $ws->damage ?></td>
            <td class="<?php echo dataCellOutputClass($ws->num_deaths) ?>"><?php echo $ws->num_deaths ?></td>
            <?php $foundWS = true ?>
            <?php break ?>
          <?php endif ?>
        <?php endforeach ?>
        <?php if(!$foundWS): ?>
          <td class="zeroValue">0</td>
          <td class="zeroValue">0</td>
          <td class="zeroValue">0</td>
        <?php endif ?>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Logs Played - <?php echo $player->num_matches ?></caption>
  <thead>
    <tr>
      <th>Log Name</th>
      <th>Map Name</th>
      <th>Date Submitted</th>
    </tr>
  </thead>
  <tbody>    
    <?php foreach($participatedLogs as $pl): ?>
      <tr>
        <td><?php echo link_to($pl['name'], 'log/show?id='.$pl['id']) ?></td>
        <td><?php echo $pl['map_name'] ?></td>
        <td><?php echo $pl['created_at'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Logs Submitted - <?php echo $numSubmittedLogs ?></caption>
  <thead>
    <tr>
      <th>Log Name</th>
      <th>Map Name</th>
      <th>Date Submitted</th>
    </tr>
  </thead>
  <tbody>
    <?php if($numSubmittedLogs == 0): ?>
      <tr><td colspan="3">This player has not submitted any logs.</td></tr>
    <?php else: ?>
      <?php foreach($submittedLogs as $sl): ?>
        <tr>
          <td><?php echo link_to($sl['name'], 'log/show?id='.$sl['id']) ?></td>
          <td><?php echo $sl['map_name'] ?></td>
          <td><?php echo $sl['created_at'] ?></td>
        </tr>
      <?php endforeach ?>
    <?php endif ?>
  </tbody>
</table>
