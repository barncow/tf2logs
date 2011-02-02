<?php
$json = array();
if(isset($url)) $json['url'] = url_for($url);
if(isset($msg)) $json['msg'] = $msg;
echo json_encode($json);
?>
