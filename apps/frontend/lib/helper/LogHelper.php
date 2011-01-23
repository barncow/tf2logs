<?php
function getWinSeparator($redScore, $blueScore) {
  if($redScore > $blueScore) return "&gt;";
  else if($redScore < $blueScore) return "&lt;";
  else if($redScore == $blueScore) return "==";
  else return "";
}

/**
* Outputs a number for seconds into a human readable format,
* like 10 minutes, 12 seconds, or if less than a minute,
* just 12 seconds.
*/
function outputSecondsToHumanFormat($seconds) {
  $mins = (int)($seconds/60);
  $secs = $seconds%60;
  $outmins = "";
  if($mins > 0) {
    $pluralmin = "";
    if($mins != 1) $pluralmin = "s";
    $outmins = $mins." minute".$pluralmin.", ";
  }
  $pluralsec = "";
  if($secs != 1) $pluralsec = "s";
  return $outmins.$secs." second".$pluralsec;
}

/**
* If the value given is zero, the output is changed
* to a class to fade the zero number. This is to
* draw attention more to the non-zero values.
* If the number is not zero, normal styling is used.
*/
function dataCellOutputClass($value) {
  if($value == 0) return 'zeroValue';
  return 'nonZeroValue';
}

function doPerDeathDivision($numerator, $deaths) {
  if($deaths == 0) return $numerator;
  return round((float) $numerator/$deaths, 3);
}

function mapExists($map) {
  return $map != null && is_dir(sfConfig::get('sf_web_dir').'/maps/'.$map);
}

function getCoords($coord) {
  $a = explode(" ", $coord);
  return $a[0].",".$a[1];
}

function outputPlayerCollection($statsArray) {
  $s = "var playerCollection = new PlayerCollection([\n";
  $isFirst = true;
  foreach ($statsArray as $stat) {
    $comma = ",";
    if($isFirst) {
      $comma = ""; 
      $isFirst = false;
    }
    $s .= $comma."new PlayerDrawable(".$stat['Player']['id'].",\"".addslashes($stat['name'])."\",\"".strtolower($stat['team'])."\")\n";
  }
  $s .= "]);";
  return $s;
}

function outputEventsCollection($eventsArray) {
  $s = "var logEventCollection = new LogEventCollection([\n";
  $isFirst = true;
  foreach ($eventsArray as $event) {
    $comma = ",";
    if($isFirst) {
      $comma = "";
      $isFirst = false;
    }
    if($event['event_type'] == "kill") {
      $s .= $comma."new LogEvent(".$event['elapsed_seconds'].").k(".$event['weapon_id'].",".$event['attacker_player_id'].",new Coordinate(".getCoords($event['attacker_coord'])."),".$event['victim_player_id'].",new Coordinate(".getCoords($event['victim_coord'])."))\n";
    } elseif($event['event_type'] == "say") {
      $s .= $comma."new LogEvent(".$event['elapsed_seconds'].").s(".$event['chat_player_id'].",\"".addslashes($event['text'])."\")\n";
    } elseif($event['event_type'] == "say_team") {
      $s .= $comma."new LogEvent(".$event['elapsed_seconds'].").ts(".$event['chat_player_id'].",\"".addslashes($event['text'])."\")\n";
    } elseif($event['event_type'] == "pointCap") {
      $pids = array();
      foreach($event['EventPlayers'] as $ep) {
        $pids[] = $ep['player_id'];
      }
      $s .= $comma."new LogEvent(".$event['elapsed_seconds'].").pc(\"".$event['capture_point']."\",\"".$event['team']."\",[".implode(",", $pids)."])\n";
    } elseif($event['event_type'] == "rndStart") {
      $s .= $comma."new LogEvent(".$event['elapsed_seconds'].").rs(".$event['red_score'].",".$event['blue_score'].")\n";
    }
  }
  $s .= "]);";
  return $s;
}

function outputWeaponCollection($weaponsArray) {
  $s = "var weaponCollection = new WeaponCollection([\n";
  $isFirst = true;
  foreach ($weaponsArray as $w) {
    $comma = ",";
    if($isFirst) {
      $comma = ""; 
      $isFirst = false;
    }
    $s .= $comma."new Weapon(".$w['id'].",\"".$w['key_name']."\",\"".addslashes($w['name'])."\")\n";
  }
  $s .= "]);";
  return $s;
}
?>
