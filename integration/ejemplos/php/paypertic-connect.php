<?php
/****************************************************************************************************************
2014 Copyright Pay per TIC S.A.
Software Libre - Licencia GPL 
Autor: @martinolivera

Este ejemplo muestra como utilizar la comunicación de la API v2 Pay per TIC para enviar y recibir información a 
través de un cliente PHP

*****************************************************************************************************************/

if (!file_exists("paypertic.hash")) {
	die("ERROR: No existe el archivo paypertic.hash para acceder a la API v2. Ver http://github.com/paypertic/integration-testing");
}
$HASH = trim(file_get_contents("paypertic.hash"));

//URLS de la API v2
$URL = "http://pagos.paypertic.com/api2/";
$URL_API["importar_cobranzas"] = $URL . "cobranzas/importar/csv";

function curl_post($url, $param_array){
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $param_array); 
  $response = curl_exec($ch);
	return $response;
}


$arch = file("conceptos.csv",FILE_IGNORE_NEW_LINES);
// carga el archivo de conceptos posibles, con el siguiente formato de línea
// Nro ; descripcion ; monto
//15;Cuota social;1235.40
foreach ($arch as $line) {
	$pieces = explode(";",$line);
	$conceptos[$pieces[0]]["descripcion"] = trim($pieces[1]); 
	$conceptos[$pieces[0]]["monto"] = trim($pieces[2]); 
}

// traer datos del formulario / request
$nombre = $_REQUEST["nombre"];
$celular = $_REQUEST["celular"];
$telefono = $_REQUEST["telefono"];
$email = $_REQUEST["email"];
$conceptoId = $_REQUEST["conceptoId"];
$importe = $_REQUEST["importe"];
$medioId = $_REQUEST["medioId"];
$medioNro = $_REQUEST["medioNro"];

$conceptoDesc = $conceptos[$conceptoId]["descripcion"]; //toma la descripcion del archivo de conceptos
$monto = $conceptos[$conceptoId]["monto"]; //toma el monto del archivo de conceptos
if ($monto == "[importe]")
	$monto = $importe; //para casos con monto libre, registra el importe ingresado

$campos[] = ""; //Nro. socio (vacío para socios nuevos)
$campos[] = $nombre; //Nombre y apellido
$campos[] = $celular; //Celular
$campos[] = $telefono; //Telefono
$campos[] = $email; //e-mail
$campos[] = $conceptoId; //Nro. concepto
$campos[] = $conceptoDesc; //Descripcion del concepto
$campos[] = $monto; //Monto a cobrar
$campos[] = $medioId; //OPCIONAL1: ID del medio de pago
$campos[] = $medioNro; //OPCIONAL2: Número del medio de pago

$txt = implode(";", $campos);

$filename = "import_" . date("YmdHis") . ".csv";

file_put_contents($filename, $txt);


$post = array(
	"hash"=>"$HASH",
	"file"=>"@$filename",
);

//aquí se conecta con el sitio de Pay per TIC para importar la cobranza
$result = curl_post($URL_API["importar_cobranzas"], $post);

$redirect = $_REQUEST["redirect"];
if (substr($result, 0, 6) === "ERROR:") {
	echo "La suscripción NO se registró correctamente: $result";
	echo "Para reintentarlo, vuelva al sitio <a href=\"$redirect\">haciendo clic aquí</a>.";
}
elseif (substr($result, 0, 3) === "OK:") {
	header( "refresh:1;url=$redirect" ); 
	echo "La suscripción se registró correctamente: $result";
	echo "En un segundo lo redirigiremos al sitio, si no funciona <a href=\"$redirect\">haga clic aquí</a>.";
}


?>
