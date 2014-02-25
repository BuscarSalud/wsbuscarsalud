<?php
include_once('bootstrap.inc');
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if( isset($_GET['palabras']) ){
  $params['palabras'] = $_GET['palabras'];
}

if(isset($_GET['pagina'])){
  $page = intval($_GET['pagina']);
  $params['page'] = $page;
}

if(isset($_GET['limite'])){
	$limit = intval($_GET['limite']);
	$params['limit'] = $limit;
}

$results = buscarsalud_data_get_profiles($params);
buscarsalud_data_prepare_profiles($results, 'http://www.buscarsalud.com');

echo json_encode($results);
//print_r($params);
exit;

?>