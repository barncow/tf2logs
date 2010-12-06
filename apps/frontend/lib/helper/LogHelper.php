<?php
function getWinSeparator($redScore, $blueScore) {
  if($redScore > $blueScore) return "&gt;";
  else if($redScore < $blueScore) return "&lt;";
  else if($redScore == $blueScore) return "==";
  else return "";
}
?>
