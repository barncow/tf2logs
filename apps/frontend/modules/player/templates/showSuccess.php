<?php use_helper('Implode') ?>
<?php use_stylesheet('jquery.tooltip.css'); ?>
<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_javascript('jquery.dimensions.js'); ?>
<?php use_javascript('jquery.tooltip.min.js'); ?>
<?php use_javascript('playershow.js'); ?>
<div id="playerName"><span class="description">Player Name: </span><?php echo $name ?></div>
Number matches: <?php echo $player->num_matches ?><br/>
<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Overall Stats</caption>
  <thead>
    <tr>
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
    <tr>
      <td><?php echo $player->kills ?></td>
      <td><?php echo $player->assists ?></td>
      <td><?php echo $player->deaths ?></td>
      <td><?php echo $player->kills_per_death ?></td>
      <td><?php echo $player->longest_kill_streak ?></td>
      <td><?php echo $player->capture_points_blocked ?></td>
      <td><?php echo $player->capture_points_captured ?></td>
      <td><?php echo $player->dominations ?></td>
      <td><?php echo $player->times_dominated ?></td>
      <td><?php echo $player->revenges ?></td>
      <td><?php echo $player->built_objects ?></td>
      <td><?php echo $player->destroyed_objects ?></td>
      <td><?php echo $player->extinguishes ?></td>
      <td><?php echo $player->ubers ?></td>
      <td><?php echo $player->ubers_per_death ?></td>
      <td><?php echo $player->dropped_ubers ?></td>
    </tr>
  </tbody>
</table>

<table class="statTable" border="0" cellspacing="0" cellpadding="3">
  <caption>Classes</caption>
  <thead>
    <tr>
      <th>Class Name</th>
      <th>Number of Times Used</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($roles as $r): ?>
      <tr>
        <td><?php echo $r->name ?></td>
        <td><?php echo $r->num_times ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
