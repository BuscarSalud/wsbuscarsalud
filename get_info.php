<?php
include_once('bootstrap.inc');
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/**
 * Returns an array of profiles.
 * @param ARRAY $params An array with the following optional index:
 *   $params = array(
 *     'filters' => array(
 *       'reclamados' => true, 
 *     ), 
 *     'sorting_items' => array( // Use any of the following and only valid sorting options
 *       'distancia', // To sort by distance ASC, this sorting option requires to provide location parameter.
 *       'puntos', // To sort by puntos DESC
 *       'nombre', // To sort by name ASC 
 *     ), 
 *     'location' => array( // Provide this field with your location to use distance sorting option.
 *       'latitude' => 123123.12, // Latitude of the location
 *       'longitude' => -12323.23, // Longitude of the location
 *     ), 
 *     'base_url' => 'http://www.buscarsalud.com', 
 *     'limit' => 20, // The ammount of items to return.
 *     'page' => 1, // Page for skiping ammounts of items defined by the limit parameter.
 *   );
 */

$seconds = time();

//Recieves exact coordinates of the mobile device, along with the Delimiter and Speciality. 
if( isset($_GET['lat']) ){
  $lat = $_GET['lat'];
  $params['location']['latitude']=$lat;
}
if( isset($_GET['lon']) ){
  $lon = $_GET['lon'];
  $params['location']['longitude']=$lon;
}


if( isset($_GET['especialidad']) ){
  $espe = intval($_GET['especialidad']);
//  $spec_array = buscarsalud_get_especialidad_by_url_safe($espe);
//  $spec_tid = $spec_array['tid'];
  $params['filters']['especialidad'] = $espe;   
}

if( isset($_GET['estado']) ){
  $estado = intval($_GET['estado']);
  $params['filters']['estado'] = $estado;   
}


if( isset($_GET['orden']) ){
  $sorting = $_GET['orden'];
  switch ($sorting){
    case 'nombre':
      $params['sorting_items'][] = 'nombre';
      $params['sorting_items'][] = 'puntos';
      $params['sorting_itmes'][] = 'distancia';
      break;
    case 'puntos':
      $params['sorting_itmes'][] = 'puntos';
      $params['sorting_itmes'][] = 'distancia';
      $params['sorting_itmes'][] = 'nombre';
      break;
    case 'distancia':
      $params['sorting_items'][] = 'distancia';
      $params['sorting_items'][] = 'puntos';
      $params['sorting_items'][] = 'nombre';
  }
}

if(isset($_GET['pagina'])){
	$page = intval($_GET['pagina']);
	$params['page'] = $page;
}

if(isset($_GET['limite'])){
	$limit = intval($_GET['limite']);
	$params['limit'] = $limit;
}
//$params['sorting_items'][] = 'nombre';
//print_r($params);

  if(isset($_GET['pagina'])){
    $i = ($page * 10) - 10;
	}else{
		$i = 0;
	}



$response = buscarsalud_data_get_profiles($params);
buscarsalud_data_prepare_profiles($response, 'http://www.buscarsalud.com');

foreach( $response as $doctor ){
    $alias = "doctor" . $i ;
    $doctors[$alias]['nid'] = $doctor['nid'];
    $doctors[$alias]['nombre'] = $doctor['nombre'];
    $doctors[$alias]['latitude'] = $doctor['latitude'];
    $doctors[$alias]['longitude'] = $doctor['longitude'];
    $doctors[$alias]['titulo'] = $doctor['titulo'];
    $doctors[$alias]['telefono'] = $doctor['telefono'];
    $doctors[$alias]['ciudad'] = $doctor['ciudad'] . ', ' . $doctor['estado'];
    $doctors[$alias]['img'] = $doctor['img'];
    $doctors[$alias]['calle'] = $doctor['calle'];
    $doctors[$alias]['colonia'] = $doctor['colonia'];
    $doctors[$alias]['escuela'] = $doctor['escuela'];
    $doctors[$alias]['puntos'] = $doctor['puntos'];
    //$doctors[$alias]['extracto'] = $doctor['extracto'];
    $i++;
  }  

print json_encode($doctors);
$seconds = time() - $seconds;

//print "Tiempo: " . $seconds;
exit;

?>
