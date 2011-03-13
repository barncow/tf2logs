<?php 
$sf_response->setTitle($player->name.' - TF2Logs.com');
use_helper('Implode');
use_javascript('jquery-1.4.4.min.js'); 
use_stylesheet('demo_table_jui.css'); 
use_javascript('jquery.dataTables.min.js'); 
use_javascript('playershow.min.js'); 
use_javascript('jquery.qtip.min.20110205.js'); 
use_stylesheet('jquery.qtip.min.20110205.css'); 
use_helper('Log');
use_helper('Search');
use_helper('PageElements'); 

$s = <<<EOD
<div class="subInfo">
  Steam ID: {$player->steamid}<br/>
  <a href="http://steamcommunity.com/profiles/{$player->numeric_steamid}" target="_blank">Steam Community</a><br/>
  Views: {$player->views}
</div>
EOD;
echo '<div class="statTableContainer">';
echo outputInfoBox("playerName", $player->name, $s);
echo '</div><br class="hardSeparator"/>';

if($player->num_matches == 0) {
  echo '<div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>This player has not played in any logs.</div>';
} else {
$s = <<<EOD
<table class="statTable" id="playerStatPanel" border="0" cellspacing="0" cellpadding="3">
    <thead>
      <tr>
        <th class="ui-state-default" title="Logs Played">LP</th>
        <th class="ui-state-default" title="Kills">K</th>
        <th class="ui-state-default" title="Assists">A</th>
        <th class="ui-state-default" title="Deaths">D</th>
        <th class="ui-state-default" title="Kills+Assists/Death">KAPD</th>
        <th class="ui-state-default" title="Damage">DA</th>
        <th class="ui-state-default" title="Damage/Death">DAPD</th>
        <th class="ui-state-default" title="Longest Kill Streak">LKS</th>
        <th class="ui-state-default" title="Headshots">HS</th>
        <th class="ui-state-default" title="Backstabs">BS</th>
        <th class="ui-state-default" title="Capture Points Blocked">CPB</th>
        <th class="ui-state-default" title="Capture Points Captured">CPC</th>
        <th class="ui-state-default" title="Intel Defends">ID</th>
        <th class="ui-state-default" title="Intel Captures">IC</th>
        <th class="ui-state-default" title="Dominations">DOM</th>
        <th class="ui-state-default" title="Times Dominated">TDM</th>
        <th class="ui-state-default" title="Revenges">R</th>
        <th class="ui-state-default" title="Extinguishes">E</th>
        <th class="ui-state-default" title="Ubers">U</th>
        <th class="ui-state-default" title="Ubers/Death">UPD</th>
        <th class="ui-state-default" title="Dropped Ubers">DU</th>
        <th class="ui-state-default" title="Healing">H</th>
      </tr>
    </thead>
    <tbody>
      <tr>
EOD;
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->num_matches).'">'.$player->num_matches.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->kills).'">'.$player->kills.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->assists).'">'.$player->assists.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->deaths).'">'.$player->deaths.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass(doPerDeathDivision($player->kills+$player->assists, $player->deaths)).'">'.doPerDeathDivision($player->kills+$player->assists, $player->deaths).'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->damage).'">'.$player->damage.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass(doPerDeathDivision($player->damage, $player->deaths)).'">'.doPerDeathDivision($player->damage, $player->deaths).'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->longest_kill_streak).'">'.$player->longest_kill_streak.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->headshots).'">'.$player->headshots.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->backstabs).'">'.$player->backstabs.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->capture_points_blocked).'">'.$player->capture_points_blocked.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->capture_points_captured).'">'.$player->capture_points_captured.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->flag_defends).'">'.$player->flag_defends.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->flag_captures).'">'.$player->flag_captures.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->dominations).'">'.$player->dominations.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->times_dominated).'">'.$player->times_dominated.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->revenges).'">'.$player->revenges.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->extinguishes).'">'.$player->extinguishes.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->ubers).'">'.$player->ubers.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass(doPerDeathDivision($player->ubers, $player->deaths)).'">'.doPerDeathDivision($player->ubers, $player->deaths).'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->dropped_ubers).'">'.$player->dropped_ubers.'</span></td>';
        $s .= '<td class="ui-table-content"><span class="'.dataCellOutputClass($player->healing).'">'.$player->healing.'</span></td>';
  $s .= <<<EOD
      </tr>
    </tbody>
  </table>
EOD;

echo '<div class="statTableContainer">';
echo outputInfoBox("playerStatPanel", 'Overall Stats', $s, true);
echo '</div><br class="hardSeparator"/>';
?>

<div style="width: 600px;">
<div class="statTableContainer">
  <table class="statTable" id="playerWeaponStats" width="100%" border="0" cellspacing="0" cellpadding="3">
    <caption>Weapon Stats</caption>
    <thead>
      <tr>
        <th>Weapon</th>
        <th>Kills</th>
        <th>Deaths</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($weapons as $w): ?>
        <?php
        $title = "";
        $weaponHTML = outputWeapon($w['name'], $w['key_name'], $w['image_name']);
        //only want a tooltip if there is an img.
        if(strpos($weaponHTML, "<img") === 0) $title = ' title="'.outputWeaponName($w['name'], $w['key_name']).'"';
        ?>
        <tr>
          <td class="ui-table-content"<?php echo $title ?>><?php echo $weaponHTML ?></td>
          <?php $foundWS = false ?>
          <?php foreach($weaponStats as $ws): ?>
            <?php if($ws->getWeaponId() == $w['id']): ?>
              <td class="ui-table-content"><span class="<?php echo dataCellOutputClass($ws->num_kills) ?>"><?php echo $ws->num_kills ?></span></td>
              <td class="ui-table-content"><span class="<?php echo dataCellOutputClass($ws->num_deaths) ?>"><?php echo $ws->num_deaths ?></span></td>
              <?php $foundWS = true ?>
              <?php break ?>
            <?php endif ?>
          <?php endforeach ?>
          <?php if(!$foundWS): ?>
            <td class="ui-table-content"><span class="zeroValue">0</span></td>
            <td class="ui-table-content"><span class="zeroValue">0</span></td>
          <?php endif ?>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<div class="statTableContainer">
  <table class="statTable" id="playerClassStats" border="0" cellspacing="0" cellpadding="3">
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
          <td class="ui-table-content">
            <img src="<?php echo sfConfig::get('app_class_icon_base_url').'/'.$r->key_name.'.png'?>" class="classIcon playerClassImg" alt="<?php echo $r->name ?>"/>
            <?php echo $r->name ?>
          </td>
          <td class="ui-table-content"><?php echo $r->num_times ?></td>
          <td class="ui-table-content"><?php echo outputSecondsToHumanFormat($r->time_played) ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?php 
$pagination = "";
if ($plPager->haveToPaginate()) $pagination = '<div class="ui-table-content statTable">'.outputPaginationLinks($sf_request, $plPager, 'plPage', 'playerLogPlayed').'</div>';
$data = "";
foreach($plPager->getResults() as $pl) {
  $link = link_to($pl['name'], 'log/show?id='.$pl['id']);
  $date = getHumanReadableDate($pl['created_at']);
  $data .= <<<EOD
      <tr>
        <td class="ui-table-content">$link</td>
        <td class="ui-table-content">{$pl['map_name']}</td>
        <td class="ui-table-content">{$date}</td>
      </tr>
EOD;
}
    
$s = <<<EOD
$pagination
<table class="statTable" width="100%">
  <thead>
    <tr>
      <th class="ui-state-default">Log Name</th>
      <th class="ui-state-default">Map Name</th>
      <th class="ui-state-default">Date Submitted</th>
    </tr>
  </thead>
  <tbody>
    $data
  </tbody>
</table>
$pagination
EOD;
echo '<div class="statTableContainer">';
echo '<a name="playerLogPlayed"/>';
echo outputInfoBox("playerLogPlayed", 'Logs Played - '.$player->num_matches, $s, true);
echo '</div></div><br class="hardSeparator"/>';

} /* user has num_matches > 0 */

if($numSubmittedLogs > 0) {
  $pagination = "";
  if ($slPager->haveToPaginate()) $pagination = '<div class="ui-table-content statTable">'.outputPaginationLinks($sf_request, $slPager, 'slPage', 'playerLogSubmitted').'</div>';
  $data = "";
  foreach($slPager->getResults() as $sl) {
    $link = link_to($sl['name'], 'log/show?id='.$sl['id']);
    $date = getHumanReadableDate($sl['created_at']);
    $data .= <<<EOD
        <tr>
          <td class="ui-table-content">$link</td>
          <td class="ui-table-content">{$sl['map_name']}</td>
          <td class="ui-table-content">{$date}</td>
        </tr>
EOD;
  }
      
  $s = <<<EOD
  $pagination
  <table class="statTable" width="100%">
    <thead>
      <tr>
        <th class="ui-state-default">Log Name</th>
        <th class="ui-state-default">Map Name</th>
        <th class="ui-state-default">Date Submitted</th>
      </tr>
    </thead>
    <tbody>
      $data
      
    </tbody>
  </table>
  $pagination
EOD;
  echo '<div class="statTableContainer">';
  echo '<a name="playerLogSubmitted"/>';
  echo outputInfoBox("playerLogSubmitted", 'Logs Submitted - '.$numSubmittedLogs, $s, true);
  echo '</div><br class="hardSeparator"/>';
  } //end if player submitted logs
?>
