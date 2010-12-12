<?php
function implodeCollection($col, $display, $backupDisplay = null) {
  $ret = "";
  foreach($col as $key => $c) {
    $sep = ", ";
    if($key == 0) $sep = "";
    $value = $c[$display];
    if($value == null) $value = $c[$backupDisplay];
    if($value == null) continue; //no valid value, just move on.
    $ret .= $sep.$value;
  }
  return $ret;
}
?>
