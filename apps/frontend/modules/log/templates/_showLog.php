<?php

$s = '<div id="mapViewerContainer">';
if($log['map_name']) {
  if(mapExists($log['map_name'])) {
    $s .= '<div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>This log file has data that can be viewed on the map. However, you will need a modern browser, such as Google Chrome or Mozilla Firefox to view it.</div>';
  } else {
    $s .= '<div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>The map '.$log['map_name'].' is not currently supported.</div>';
  }
} else {
  $s .= '<div class="alertBox ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'.$log['Submitter']['name'].' did not specify a map for this log file.</div>';
}
 $s .= '</div>';
echo outputInfoBox("logViewerInfoBox", "Log Viewer", $s);
echo '<br class="hardSeparator"/>';



$miniStats = array();
echo outputStatPanel($log['Stats'], $miniStats);

if(!$playerHealStats || count($playerHealStats) == 0) {
  echo '<div class="statTableContainer">';
  echo outputInfoBox("_playerHealStats", "Heal Spread", '<div class="infoBoxAlert ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>This is only available with the<br/>Supplemental Stats SourceMod plugin.</div>', true);
  echo '</div>';
} else {
  echo outputPlayerHealStats($miniStats, $playerHealStats);
}

echo outputMedicStats($log['Stats']);

echo outputWeaponStats($weapons, $miniStats, $weaponStats);

echo '<span class="statDescription">Rows indicate kills; columns indicate deaths</span><br class="hardSeparator"/>';
echo outputPlayerStats($miniStats, $playerStats);

if(!$itemPickupStats || count($itemPickupStats) == 0) {
  echo '<div class="statTableContainer">';
  echo outputInfoBox("_itemPickupStats", "Items Picked Up", '<div class="infoBoxAlert ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>This is only available with the<br/>Supplemental Stats SourceMod plugin.</div>', true);
  echo '</div>';
} else {
  echo outputItemPickupStats($miniStats, $itemPickupStats);
}
echo '<br class="hardSeparator"/>';

if(mapExists($log['map_name'])) { ?>
<script type="application/x-javascript">
  var gameMapObj;
  var mapViewerObj;
  <?php echo outputPlayerCollection($log['Stats']); ?>
  <?php echo outputWeaponCollection($weapons); ?>

/////////////////////////////////////////////////////////////////////////////////////
// Doc ready
/////////////////////////////////////////////////////////////////////////////////////
$(function (){  
	  mvc = $("#mapViewerControls");
	  mapViewerObj = new MapViewer(gameMapObj, playerCollection, logEventCollection, weaponCollection, $("#mapViewerContainer"));
});
</script>
<?php } ?>
