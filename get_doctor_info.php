<?php
include_once('bootstrap.inc');
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");



$nid = $_GET["nid"];
$user = $_POST['user'];
$pass = $_POST['pass'];
$login = $_POST['login'];

$output = array();

function load_doc_info($nid_passed, $doctor){ 
  $node = node_load($nid_passed);

	$experiencia_hasta_ano = NULL;
	
	//Get Latitude and Longitude values
	if($node->field_mapa){
	  /*$lonlat = $node->field_mapa['und'][0]['wkt'];
	  $string = substr($lonlat, 7, -1);
	  list($longitude,$latitude) = explode(" ", $string); */
	  $longitude = $node->field_mapa['und'][0]['lon'];
	  $latitude = $node->field_mapa['und'][0]['lat'];
	}else{
		$longitude = null;
		$latitude = null;
	}
	
	//Get Phone
	if($node->field_telefono){
		$phone_number = $node->field_telefono['und'][0]['value'];
	}else{
		$phone_number = null;
	}
	
	//Get Complete address
	if($node->field_domicilio){
	  $address_street = $node->field_domicilio['und'][0]['thoroughfare'];
	  $address_street = mb_convert_case($address_street, MB_CASE_TITLE, 'UTF-8');
	  $address_colonia = $node->field_domicilio['und'][0]['premise'];
	  $address_colonia = mb_convert_case($address_colonia, MB_CASE_TITLE, 'UTF-8');
	  $address_locality = $node->field_domicilio['und'][0]['locality'];
	  $address_locality = mb_convert_case($address_locality, MB_CASE_TITLE, 'UTF-8');
	  $address_state = $node->field_domicilio['und'][0]['administrative_area']; 
	  $address_state = mb_convert_case($address_state, MB_CASE_TITLE, 'UTF-8');
	  $address_postal_code = $node->field_domicilio['und'][0]['postal_code'];
	  $address_name = $node->field_domicilio['und'][0]['name_line'];
	  $address_name = mb_convert_case($address_name, MB_CASE_TITLE, 'UTF-8');
	  
	}else{
	  $address_street = null;
	  $address_colonia = null;
	  $address_locality = NULL;
	  $address_state = null;
	  $address_name = null;
	}
	
	//Get the State.
	$field_estado = $node->field_estado['und']['0']['tid'];
	$estado_entity = entity_load('taxonomy_term', array($field_estado));
	$state = $estado_entity[$field_estado]->name;
	
	//Get the Specialty
	$field_especialidad = $node->field_especialidad['und']['0']['tid'];
	$especialidad_entity = entity_load('taxonomy_term', array($field_especialidad));
	$specialty = $especialidad_entity[$field_especialidad]->name;
	
	//Get the photo name
	if($node->field_image){
	  $photo_uri = $node->field_image['und'][0]['uri'];
		$photo_name = str_replace("public://", "", $photo_uri);
	}else{
		$photo_name= null;
	}
	
	//Get summary
	if($node->field_perfil_extracto){
		$summary = $node->field_perfil_extracto['und']['0']['value'];
		$summary = mb_convert_case($summary, MB_CASE_TITLE, 'UTF-8');
	}else{
		$summary = null;
	}
	
	// Get degree, School, Year
	$cedulas = array();
	$c = 1;
	if($node->field_cedula_profesional){
		$field_cedula_profesional_array = $node->field_cedula_profesional['und'];
		foreach($field_cedula_profesional_array as $cedula){	
			$field_cedula_profesional = $cedula['value'];
			$cedula_entity = entity_load('field_collection_item', array($field_cedula_profesional));
			$degree = $cedula_entity[$field_cedula_profesional]->field_cedula['und']['0']['value'];
			$degree_convert_case = mb_convert_case($degree, MB_CASE_TITLE, 'UTF-8');		
			$school = $cedula_entity[$field_cedula_profesional]->field_escuela['und']['0']['value'];
			$school_convert_case = mb_convert_case($school, MB_CASE_TITLE, 'UTF-8');
		  $year = $cedula_entity[$field_cedula_profesional]->field_year['und']['0']['value'];
		  $cedulas[$c]['degree'] = $degree_convert_case;
		  $cedulas[$c]['school'] = $school_convert_case;
		  $cedulas[$c]['year'] = $year;
		  $c++;
	  }
	}
	//Get Languages
	$idiomas = array();
	$m = 1;
	$n = 1;
	if($node->field_idiomas){
		$field_idiomas_array = $node->field_idiomas['und'];
		foreach($field_idiomas_array as $idioma){
		  $idioma_value = $idioma['value'];
		  $idioma_entity = entity_load('field_collection_item', array($idioma_value));
		  $idioma = $idioma_entity[$idioma_value]->field_idioma['und'][0]['value'];
		  $nivel = $idioma_entity[$idioma_value]->field_idioma_nivel['und'][0]['value'];
		  switch($nivel){
		  	case 'basico':
		  		$nivel_value = 'Nivel básico';
		  		break;
		  	case 'profesional':
		  		$nivel_value = 'Nivel profesional';
		  		break;
		  	case 'lenguaje_nativo': 
		  		$nivel_value = 'Lenguaje Nativo';
		  	  break; 
		  	case 'basico_profesional':
		  		$nivel_value = 'Básico profesional';
		  		break;
		  	case 'basico_limitada':
		  		$nivel_value = 'Básico limitado';
		  		break;
		  } 
		  $idiomas[$m]['name'] = mb_convert_case($idioma, MB_CASE_TITLE, 'UTF-8');;
		  $idiomas[$m]['level'] = $nivel_value;
		  $m++;
		}
	}else{
	  $idiomas = null;
	}
	
	//Get Experience
	$experience = array();
	$m = 1;
	if($node->field_experiencia_profesional){
		$field_experiencia_array = $node->field_experiencia_profesional['und'];
		foreach($field_experiencia_array as $experiencia){
		  $experiencia_value = $experiencia['value'];
		  $experiencia_entity = entity_load('field_collection_item', array($experiencia_value));
		  $experiencia_titulo = $experiencia_entity[$experiencia_value]->field_titulo['und'][0]['value'];
		  $experiencia_empresa = $experiencia_entity[$experiencia_value]->field_empresa['und'][0]['title'];
		  $experiencia_descripcion = $experiencia_entity[$experiencia_value]->field_descripcion['und'][0]['value'];
		  $experiencia_desde_ano = $experiencia_entity[$experiencia_value]->field_desde_ano['und'][0]['value'];
		  if(isset($experiencia_entity[$experiencia_value]->field_hasta_ano['und'][0]['value'])){
	      $experiencia_hasta_ano = $experiencia_entity[$experiencia_value]->field_hasta_ano['und'][0]['value'];
	    }
		  if($experiencia_hasta_ano == NULL){
		  	$experiencia_hasta_ano = 'Actualmente';
		  }
		  $periodo = $experiencia_desde_ano . '-' . $experiencia_hasta_ano;
		  $experience[$m]['title'] = mb_convert_case($experiencia_titulo, MB_CASE_TITLE, 'UTF-8');
		  $experience[$m]['company'] = mb_convert_case($experiencia_empresa, MB_CASE_TITLE, 'UTF-8');
		  $experience[$m]['description'] = mb_convert_case($experiencia_descripcion, MB_CASE_TITLE, 'UTF-8');
		  $experience[$m]['period'] = $periodo;
			$m++;
		}
	}else{
		$experience = null;
	}
	
	//Get Mail
	if($node->field_correo_contacto){
		$email = $node->field_correo_contacto['und'][0]['email'];
	}else{
		$email = null;
	}
	
	//Get Points
	if($node->field_puntos){
	  $points = $node->field_puntos['und'][0]['value'];
	}else{
	  $points = null;
	}
	
	//Get Subtitle
	if($node->field_subtitle){
		$subtite = $node->field_subtitle['und']['0']['value'];
		$subtite = mb_convert_case($subtite, MB_CASE_TITLE, 'UTF-8');
	}else{
		$subtite = null;
	}
	
	//Gather all doctor's info into an array
	$doctor["name"] = $node->title;
	$doctor["state"] = $state;
	$doctor["specialty"] = $specialty;
	$doctor["latitude"] = $latitude;
	$doctor["longitude"] = $longitude;
	$doctor["phone"] = $phone_number;
	if($address_state == 'Coahuila De Zaragoza'){
    $address_state = 'Coahuila';
  }
  if($address_state == 'Michoacán de Ocampo'){
    $address_state = 'Michoacán';
  }
  if($address_state == 'Veracruz De Ignacio De La Llave'){
    $address_state = 'Veracruz';
  }
  if($address_state == 'Baja California Sur'){
    $address_state = 'Baja California S.';
  }
  if( $address_locality == '' || is_null($address_locality)){
	  	$doctor["locality"] = $state;
	}else{
		if($address_locality == $address_state){
			$doctor["locality"] = $address_locality;
		}else{
	  	$doctor["locality"] = $address_locality . ", " . $address_state;
	  }
	}
	
	$doctor["photo"] = $photo_name;
	$doctor["street"] = $address_street;
	$doctor["colonia"] = $address_colonia;
	$doctor["address_name"] = $address_name;
	$doctor["postal_code"] = $address_postal_code;
	$doctor["subtitle"] = $subtite;
	$doctor["summary"] = $summary;
	$doctor["email"] = $email;
	$doctor["points"] = $points;
	$doctor['cedulas'] = $cedulas;
	$doctor["languages"] = $idiomas;
	$doctor["experience"] = $experience;

	return $doctor;
}

if($login == 1){
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
	  echo json_encode($output);
	}
}else{
	echo json_encode(load_doc_info($nid, $output));
}


exit;
?>