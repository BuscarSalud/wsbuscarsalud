<?php
include_once('bootstrap.inc');


//$cedula = $_POST['cedula'];
$data = $_POST['allData'];

$requestsDir = 'requests/';

$decoded_info = json_decode($data, true);
$uuid = $decoded_info['uuid'];

$uuid_after_clean = preg_replace("/[^a-z0-9\-]/i", "", $uuid);
$uuid_length = strlen(utf8_decode($uuid_after_clean));

if($uuid_length == 44)
{
  $uuid_validated = $uuid_after_clean;
}else{
  echo "Cédula no valida -> " . $uuid_after_clean;
  exit;
}

$newRequest = $requestsDir . $uuid_validated;
$newRequestAndImagesDirs = $newRequest . '/images/';

$fileName = $newRequest . '/' . $uuid_validated . '.txt';

mkdir($newRequestAndImagesDirs,0777,true);

$f = fopen ($fileName, 'w');
fwrite($f, $data, strlen($data));
fclose($f);

if(isset($_FILES['imagenCedulaFrontal']['name']))
{
  $cedulaFrontal = $newRequestAndImagesDirs . basename($_FILES['imagenCedulaFrontal']['name']);
  move_uploaded_file($_FILES['imagenCedulaFrontal']['tmp_name'], $cedulaFrontal);
}

if(isset($_FILES['imagenCedulaTrasera']['name']))
{
  $cedulaTrasera = $newRequestAndImagesDirs . basename($_FILES['imagenCedulaTrasera']['name']);
  move_uploaded_file($_FILES['imagenCedulaTrasera']['tmp_name'], $cedulaTrasera);
}

if(isset($_FILES['imagenCredencialFrontal']['name']))
{
  $credencialFrontal = $newRequestAndImagesDirs . basename($_FILES['imagenCredencialFrontal']['name']);
  move_uploaded_file($_FILES['imagenCredencialFrontal']['tmp_name'], $credencialFrontal);
}

if(isset($_FILES['imagenCredencialTrasera']['name']))
{
  $credencialTrasera = $newRequestAndImagesDirs . basename($_FILES['imagenCredencialTrasera']['name']);
  move_uploaded_file($_FILES['imagenCredencialTrasera']['tmp_name'], $credencialTrasera);
}

if(isset($_FILES['imagenPerfil']['name']))
{
  $imagenPerfil = $newRequestAndImagesDirs . basename($_FILES['imagenPerfil']['name']);
  move_uploaded_file($_FILES['imagenPerfil']['tmp_name'], $imagenPerfil);
}

if(isset($_FILES['imagenGaleria1']['name']))
{
  $imagenGaleria1 = $newRequestAndImagesDirs . basename($_FILES['imagenGaleria1']['name']);
  move_uploaded_file($_FILES['imagenGaleria1']['tmp_name'], $imagenGaleria1);
}

if(isset($_FILES['imagenGaleria2']['name']))
{
  $imagenGaleria2 = $newRequestAndImagesDirs . basename($_FILES['imagenGaleria2']['name']);
  move_uploaded_file($_FILES['imagenGaleria2']['tmp_name'], $imagenGaleria2);
}

if(isset($_FILES['imagenGaleria3']['name']))
{
  $imagenGaleria3 = $newRequestAndImagesDirs . basename($_FILES['imagenGaleria3']['name']);
  move_uploaded_file($_FILES['imagenGaleria3']['tmp_name'], $imagenGaleria3);
}

if(isset($_FILES['imagenGaleria4']['name']))
{
  $imagenGaleria4 = $newRequestAndImagesDirs . basename($_FILES['imagenGaleria4']['name']);
  move_uploaded_file($_FILES['imagenGaleria4']['tmp_name'], $imagenGaleria4);
}

if(isset($_FILES['imagenGaleria5']['name']))
{
  $imagenGaleria5 = $newRequestAndImagesDirs . basename($_FILES['imagenGaleria5']['name']);
  move_uploaded_file($_FILES['imagenGaleria5']['tmp_name'], $imagenGaleria5);
}



echo "Data = " . $uuid_after_clean;

header("Content-Type: application/json");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
exit;
?>