<?php
$json = array();
if(isset($url)) $json['url'] = url_for($url);
if(isset($msg)) $json['msg'] = $msg;
if(isset($logid)) {
  if(!isset($json['msg'])) $json['msg'] = "";
  $json['msg'] .= ' When fixed, the log will be <a href="log/show?id='.$logid.'">here</a>.';
}
echo json_encode($json);
?>
