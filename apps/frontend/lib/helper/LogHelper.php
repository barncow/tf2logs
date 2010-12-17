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
?>
