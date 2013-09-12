<?php
include_once('bootstrap.inc');
header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$output = array();

if(isset($_GET['especialidad'])){
  $specialty = $_GET['especialidad'];
	if(isset($specialty))
	{
	  $i = 1;
	  $states_within_specialty = array();
	  $specialty_array = buscarsalud_get_especialidad_by_tid($specialty);
	  $states_within_specialty = buscarsalud_get_estados($specialty_array, true);
	  $output['estado0']['nombre'] = "Todos";
	  $output['estado0']['tid'] = "";  
	  foreach($states_within_specialty as $state_in_array){
	    $index = 'estado' . $i;
	    $output[$index]['nombre'] = $state_in_array['name'];
	    $output[$index]['tid'] = $state_in_array['tid'];
	    $i++;
    }
  }
}

if(isset($_GET['estado'])){
  $state = $_GET['estado'];
  
	if(isset($state))
	{
	  $a = 1;
	  $specialties_within_state = array();
	  $state_array = buscarsalud_get_estado_by_tid($state);
	  $specialties_within_state = buscarsalud_get_especialidades($state_array, true);
	  foreach($specialties_within_state as $specialty_in_array){
	    $index = 'especialidad' . $a;
	    $output[$index]['nombre'] = $specialty_in_array['name'];
	    $output[$index]['tid'] = $specialty_in_array['tid'];
	    $a++;
	  }
	}
}

if(isset($_GET['todos'])){
  $all = $_GET['todos'];
  
	if($all == 'especialidad'){
	  $m = 1;
	  $all_specialties_with_data = array();
	  $all_specialties_with_data = buscarsalud_get_especialidades(null, true);
		foreach($all_specialties_with_data as $specialties_in_array){
		  $index = 'especialidad' . $m; 
		  $output[$index]['nombre'] = $specialties_in_array['name'];
		  $output[$index]['tid'] = $specialties_in_array['tid'];
		  $m++;
		}
	}
	
	if($all == 'estado'){
	  $r = 1;
	  $all_states_with_data = array();
	  $all_states_with_data = buscarsalud_get_estados(null, true);
	  $output['estado0']['nombre'] = "Todos";
	  $output['estado0']['tid'] = "";
		foreach($all_states_with_data as $states_in_array){
		  $index = 'estado' . $r; 
		  $output[$index]['nombre'] = $states_in_array['name'];
		  $output[$index]['tid'] = $states_in_array['tid'];
		  $r++;
		}
	}
}

if(isset($_GET['disponible'])){
  $available = $_GET['disponible'];
  
	if($available == 'especialidad'){
	  $n = 1;
	  $all_specialties_available = array();
	  $all_specialties_available = buscarsalud_get_especialidades();
	  $output['especialidad0']['nombre'] = '-Seleccionar-';
	  $output['especialidad0']['tid'] = 0;
	  foreach($all_specialties_available as $specialties_in_array){
	    $index = 'especialidad' . $n;
	    if($specialties_in_array['name'] != 'Sistemas'){
	      $output[$index]['nombre'] = $specialties_in_array['name'];
	      $output[$index]['tid'] = $specialties_in_array['tid'];
	      $n++;
	    }
	  }
	}
	
	if($available == 'estado'){
	  $o = 1;
	  $all_states_available = array();
	  $all_states_available = buscarsalud_get_estados();
	  $output['estado0']['nombre'] = '-Seleccionar-';
	  $output['estado0']['tid'] = 0;
	  foreach($all_states_available as $states_in_array){
	    $index = 'estado' . $o;
	    if($states_in_array['name'] != 'Staff'){
	      $output[$index]['nombre'] = $states_in_array['name'];
	      $output[$index]['tid'] = $states_in_array['tid'];
	      $o++;
	    }
	  }
	}
}

echo json_encode($output);
?>