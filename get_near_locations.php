<?php
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/php_error_log.txt');
error_reporting(E_ALL);

// START BOOTSTRAP DRUPAL
define('DRUPAL_ROOT', '/var/www/dev.buscarsalud/html');
$_SERVER['REMOTE_ADDR'] = "localhost"; // Necessary if running from command line
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
// END BOOTSTRAP DRUPAL

//Recieves exact coordinates of the mobile device, along with the Delimiter and Speciality. 
$lat = $_GET["lat"];
$lon = $_GET["lon"];
$del = $_GET["del"];
$spec = $_GET["spec"];


$spec_array = buscarsalud_get_especialidad_by_url_safe($spec);
$spec_tid = $spec_array['tid'];


//Create the query that fetches near locations. 
//Will return nid and the distance between the given point and the location for each result, according the Delimiter "del".
$sql = "SELECT n.nid, (6371 * ACOS( 
                                SIN(RADIANS(f.field_mapa_lat)) * SIN(RADIANS({$lat})) 
                                + COS(RADIANS( f.field_mapa_lon - {$lon})) * COS(RADIANS(f.field_mapa_lat)) 
                                * COS(RADIANS({$lat}))
                                )
                   ) AS distance
FROM node n
INNER JOIN field_data_field_mapa f ON f.entity_type = 'node' AND n.nid = f.entity_id AND n.vid = f.revision_id
INNER JOIN taxonomy_index especialidad ON n.nid = especialidad.nid AND especialidad.tid = {$spec_tid}
WHERE n.type = 'perfil' AND n.status = 1
HAVING distance < {$del} 
ORDER BY distance ASC
LIMIT 0 , 10";


//INNER JOIN taxonomy_index estado ON n.nid = estado.nid AND estado.tid = 17


//Send query to DB, returns results.
$results = db_query($sql);
$rows = array();
while(($row=$results->fetchAssoc())) {
  $rows[] = node_load($row['nid']);
}

// Declare variables
$doctors = array();
$i = 1;

//Go through each doctor and create the array with individual info
foreach($rows as $node){		
	//Get Latitude and Longitude values
	$lonlat = $node->field_mapa['und']['0']['wkt'];
	$string = substr($lonlat, 7, -1);
	list($longitude,$latitude) = explode(" ", $string); 
	
	// Get Degree
	$field_cedula_profesional = $node->field_cedula_profesional['und']['0']['value'];
	$cedula_entity = entity_load('field_collection_item', array($field_cedula_profesional));
	$degree = $cedula_entity[$field_cedula_profesional]->field_cedula['und']['0']['value'];	
	
	//Get Phone
	if($node->field_telefono){
		$phone_number = $node->field_telefono['und']['0']['value'];
	}else{
		$phone_number = null;
	}
	
	//Get Complete address
	$address_street = $node->field_domicilio['und']['0']['thoroughfare'];
	$address_colonia = $node->field_domicilio['und']['0']['premise'];
	$address_locality = $node->field_domicilio['und']['0']['locality']; 
	
	//Get the State.
	$field_estado = $node->field_estado['und']['0']['tid'];
	$estado_entity = entity_load('taxonomy_term', array($field_estado));
	$state = $estado_entity[$field_estado]->name;
	
	//Get the photo name
	if($node->field_image){
		$photo_uri = $node->field_image['und']['0']['uri'];
		$photo_name = str_replace("public://", "", $photo_uri);
	}else{
		$photo_name= null;
	}
	
	//Create the package to send
	$alias = "doctor" . $i;
	$doctors[$alias]['id'] = $node->nid;
	$doctors[$alias]['name'] = $node->title;
	$doctors[$alias]['latitude'] = $latitude;
	$doctors[$alias]['longitude'] = $longitude;
	$doctors[$alias]['degree'] = $degree;
	$doctors[$alias]['phone'] = $phone_number;
	$doctors[$alias]['locality'] = $address_locality . ", " . $state;
	$doctors[$alias]['photo'] = $photo_name;
	$doctors[$alias]['street'] = $address_street;
	$doctors[$alias]['colonia'] = $address_colonia;
	$i++;
}

// Send the package with all doctors and their individual info
print json_encode($doctors);
exit;
?>



