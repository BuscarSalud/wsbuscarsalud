<?php
include_once('bootstrap.inc');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Cache-Control: no-cache, must-revalidate"); 
header("Content-type: application/json");
header('Content-Type: text/html; charset=UTF-8');

$get_nid = $_GET["nid"];

// Load node with id 1
/*
$sql = "SELECT nid FROM node LIMIT 10";
$results = db_query($sql);
$rows = array();
while(($row=$results->fetchAssoc())) {
  $rows[] = node_load($row['nid']);
}
*/


$node = node_load($get_nid);
print "<pre>" . print_r($node, true) . "</pre>";

//print $node->field_mapa[und][0][wkt];
//echo "<br/>";
//print $node->field_mapa[und][0][lon];

$phone = $node->field_telefono;

/*$lonlat = $node->field_mapa['und'][0]['wkt'];
$string = substr($lonlat, 7, -1);
list($longitude,$latitude) = explode(" ", $string);


print ("Longitude: " . $longitude);
echo "<br/>";
print ("Latitude: " . $latitude); 
echo "<br/><br/>";
*/

$phone = array_filter($phone);
if($node->field_telefono){
	echo "Array not empty";
}else{
	echo "Array empty";
}

$field_ced = $node->field_cedula_profesional['und']['0']['value'];
$field_cedula_profesional = entity_load('field_collection_item', array($field_ced));
$degree = $field_cedula_profesional[$field_ced]->field_cedula['und']['0']['value'];

$idiomas = array();

$m = 1;
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
	  		$nivel_value = 'Nivel básico profesional';
	  		break;
	  	case 'basico_limitada':
	  		$nivel_value = 'Nivel basico limitado';
	  		break;
	  } 
	  $idiomas[$m][] = $idioma;
	  $idiomas[$m][] = $nivel_value;
	  $m++;
	 // print "<pre>" . print_r($idioma_entity, true) . "</pre>";
	}
}

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
	  $experiencia_hasta_ano = $experiencia_entity[$experiencia_value]->field_hasta_ano['und'][0]['value'];
	  if($experiencia_hasta_ano == NULL){
	  	$experiencia_hasta_ano = 'Actualmente';
	  }
	  $periodo = $experiencia_desde_ano . '-' . $experiencia_hasta_ano;
	  $experience[$m]['titulo'] = $experiencia_titulo;
	  $experience[$m]['empresa'] = $experiencia_empresa;
	  $experience[$m]['descripcion'] = $experiencia_descripcion;
	  $experience[$m]['periodo'] = $periodo;
	  print "<pre>" . print_r($experiencia_entity, true) . "</pre>";
		$m++;
	}
}

$address = array();
if($node->field_domicilio){
  $direccion_nombre = $node->field_domicilio['und'][0]['name_line'];
	$direccion_calle = $node->field_domicilio['und'][0]['thoroughfare'];
	$direccion_colonia = $node->field_domicilio['und'][0]['premise'];
	$direccion_ciudad = $node->field_domicilio['und'][0]['locality'];
	$direccion_estado = $node->field_domicilio['und'][0]['administrative_area'];
	$direccion_codigo_postal = $node->field_domiclio['und'][0]['postal_code'];
	$direccion_estado_ciudad = $direccion_ciudad . ", " . $direccion_estado;
}

$pregunta = $node->field_acordeon['und']['0']['value'];
$preguntas_frecuentes = entity_load('field_collection_item', array($pregunta));

//$cedula = $field_cedula_profesional['6']->field_cedula['und']['0']['value'];
//echo $cedula;

//$degree = $field_cedula_profesional[$field_cedula_profesional][field_cedula][und][0][value];

//print "<pre>" . print_r($field_cedula_profesional, true) . "</pre>";

$taxonomy = entity_load('taxonomy_term', array(16));
//print "<pre>" . print_r($taxonomy, true) . "</pre>";

//$term = buscarsalud_get_estado_by_url_safe('baja-california-sur');
//$term =  buscarsalud_get_especialidad_by_url_safe('cirugia');
//$term = buscarsalud_get_especialidad_by_tid('105');
//$term = buscarsalud_get_estados();
//--> $term = buscarsalud_get_especialidades();
//$term = buscarsalud_get_estados(null, true);
//$term = buscarsalud_get_especialidades(3, true);

//$term = buscarsalud_get_especialidades();

print "<pre>" . print_r($field_cedula_profesional, true) . "</pre>";

//print print_r($field_cedula_profesional, true);



echo "Carrera: " . $degree . "\n";

print "Pregunta frecuente: <pre>" . print_r($preguntas_frecuentes, true) . "</pre>";
//print "<pre>" . print_r($taxonomy, true) . "</pre>";
//print "<pre>" . print_r($term, true) . "</pre>";

exit;
?>
