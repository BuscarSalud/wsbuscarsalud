<?php
include_once('bootstrap.inc');
require_once('get_doctor_info.php');

$user = $_POST['user'];
$pass = $_POST['pass'];

$output = array();
$nid = 2318985;

if($user == 'felix'){
  $output['responseCode'] = 1;
  $output['nid'] = 2318985;
  $output['user'] = $user;
  $output['pass'] = $pass;
  $nid_login = 2318985;
  //Send the package
  echo json_encode(load_doc_info($nid_login, $output));
  
}else{
	$output['response'] = 0;
	$output['nid'] = null;
	$output['user'] = $user;
	$output['pass'] = $pass;
  $nid_login = 2318985;
  echo json_encode(load_doc_info($nid_login, $output));
}




echo json_encode($output);


header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
exit;
?>