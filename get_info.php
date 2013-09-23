<?php
include_once('bootstrap.inc');
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


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

//$params['sorting_items'][] = 'nombre';
//print_r($params);




$results = buscarsalud_data_get_profiles($params);
buscarsalud_data_prepare_profiles($results, 'http://www.buscarsalud.com');

//$results = buscarsalud_ws_get_profiles($params);

foreach( $results as $doc ){
  $rows[] = node_load($doc['nid']);
}

$i = 0;

//Go through each doctor and create the array with individual info
foreach($rows as $node){    
  //Get Latitude and Longitude values
  if($node->field_mapa){
    /*$lonlat = $node->field_mapa['und'][0]['wkt'];
    $string = substr($lonlat, 7, -1);
    list($longitude,$latitude) = explode(" ", $string); */
    $longitude = $node->field_mapa['und'][0]['lon'];
    $latitude = $node->field_mapa['und'][0]['lat'];
  }else{
    $latitude = null;
    $longitude = null;
  }
  
  
  // Get Degree and School
  $field_cedula_profesional = $node->field_cedula_profesional['und'][0]['value'];
  $cedula_entity = entity_load('field_collection_item', array($field_cedula_profesional));
  $degree = $cedula_entity[$field_cedula_profesional]->field_cedula['und'][0]['value'];  
  $degree_convert_case = mb_convert_case($degree, MB_CASE_TITLE, 'UTF-8');
  $school = $cedula_entity[$field_cedula_profesional]->field_escuela['und'][0]['value'];  
  $school_convert_case = mb_convert_case($school, MB_CASE_TITLE, 'UTF-8');
  
  //Get Phone
  if($node->field_telefono){
    $phone_number = $node->field_telefono['und'][0]['value'];
  }else{
    $phone_number = null;
  }
  
  //Get Complete address
  if($node->field_domicilio){
    $address_street = $node->field_domicilio['und'][0]['thoroughfare'];
    $address_colonia = $node->field_domicilio['und'][0]['premise'];
    $address_locality = $node->field_domicilio['und'][0]['locality']; 
    $address_state = $node->field_domicilio['und'][0]['administrative_area']; 
  }else{
    $address_street = null;
    $address_colonia = null;
    $address_locality = null;
    $address_state = null;
  }
  
  
  //Get the State.
  $field_estado = $node->field_estado['und'][0]['tid'];
  $estado_entity = entity_load('taxonomy_term', array($field_estado));
  $state = $estado_entity[$field_estado]->name;
  
  //Get the photo name
  if($node->field_image){
    $photo_uri = $node->field_image['und'][0]['uri'];
    $photo_name = str_replace("public://", "", $photo_uri);
  }else{
    $photo_name= null;
  }
  
  //Get Points
  if($node->field_puntos){
    $points = $node->field_puntos ['und'][0]['value'];
  }else{
    $points = null;
  }
  
  
  //Create the package to send

  $alias = "doctor" . $i ;
  $doctors[$alias]['nid'] = $node->nid;
  $doctors[$alias]['nombre'] = $node->title;
  $doctors[$alias]['latitude'] = $latitude;
  $doctors[$alias]['longitude'] = $longitude;
  $doctors[$alias]['titulo'] = $degree_convert_case;
  $doctors[$alias]['telefono'] = $phone_number;
  if( $address_locality == "" ){
    $doctors[$alias]["ciudad"] = mb_convert_case($state, MB_CASE_TITLE, 'UTF-8');
  }else{
    $ciudad_combinada = $address_locality . ", " . $address_state;
    $doctors[$alias]["ciudad"] = mb_convert_case($ciudad_combinada, MB_CASE_TITLE, 'UTF-8');
  }
  $doctors[$alias]['img'] = $photo_name;
  $doctors[$alias]['calle'] = mb_convert_case($address_street, MB_CASE_TITLE, 'UTF-8');
  $doctors[$alias]['colonia'] = mb_convert_case($address_colonia, MB_CASE_TITLE, 'UTF-8');
  $doctors[$alias]['escuela'] = $school_convert_case;
  $doctors[$alias]['puntos'] = $points;
  $i++;
}

/*
$i = 0;
foreach($results as $k => $value){
  unset ($results[$k]);
  $new_key = "doctor" . $i;
  $results[$new_key] = $value;
  $i++;
}*/
//print "<pre>" . print_r($results, true) . "</pre>";

// Send the package with all doctors and their individual info
print json_encode($doctors);
$seconds = time() - $seconds;

//print "Tiempo: " . $seconds;
exit;

?>