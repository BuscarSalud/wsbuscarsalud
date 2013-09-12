<?php
include_once('bootstrap.inc');

$uuid = $_POST['request'];
$output = array();

$uuid_after_clean = preg_replace("/[^a-z0-9\-]/i", "", $uuid);
$uuid_length = strlen(utf8_decode($uuid_after_clean));

if($uuid_length == 44)
{
  $uuid_validated = $uuid_after_clean;
  $output['status'] = 1;
  $output['request'] = $uuid_validated;
  $output['error'] = 0;
  $output['error_message'] = '';
  $output['nid'] = '2318985';
  echo json_encode($output);
}else{
  $output['status'] = 0;
  $output['request'] = $uuid_after_clean;
  $output['error'] = 1;
  $output['error_message'] = 'La cedula no es valida';
  $output['nid'] = '';
  echo json_encode($output);
  exit;
}


header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
exit;
?>