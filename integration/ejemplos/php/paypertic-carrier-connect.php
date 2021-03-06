<?php
/****************************************************************************************************************
2014 Copyright Pay per TIC S.A.
Software Libre - Licencia GPL 
Autor: @martinolivera

Este ejemplo muestra como utilizar la comunicación de la API v2 Pay per TIC para enviar y recibir información a 
través de un cliente PHP, para carriers

*****************************************************************************************************************/

if (!file_exists("paypertic-carrier.hash")) {
	die("ERROR: No existe el archivo paypertic-carrier.hash para acceder a la API v2. Ver http://github.com/paypertic/integration-testing");
}
$HASH = trim(file_get_contents("paypertic-carrier.hash"));

//URLS de la API v2 para carriers
$URL = "http://pagos.paypertic.com/api2/carrier/";

$URL_API["crear_institucion"] = $URL . "institucion/crear";

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


// traer datos del formulario / request
$cuit = $_REQUEST["cuit"];
$nombre = $_REQUEST["nombre"];
//$domicilio = $_REQUEST["domicilio"];

$resp_nombre = $_REQUEST["resp_nombre"];
$resp_cargo = $_REQUEST["resp_cargo"];
$resp_mail = $_REQUEST["resp_mail"];
$resp_telefono = $_REQUEST["resp_telefono"];

$cobros_nombre = $_REQUEST["cobros_nombre"];
$cobros_horario = $_REQUEST["cobros_horario"];
$cobros_mail = $_REQUEST["cobros_mail"];
$cobros_telefono = $_REQUEST["cobros_telefono"];

$smtp_user = $_REQUEST["smtp_user"];
$smtp_pass = $_REQUEST["smtp_pass"];
$smtp_host = $_REQUEST["smtp_host"];
$smtp_port = $_REQUEST["smtp_port"];

$ante_iva = $_REQUEST["ante_iva"];
$vendedor = $_REQUEST["vendedor"];
$nos_conocio = $_REQUEST["nos_conocio"];



$post2paypertic = array(
	"hash"=>"$HASH",

	"cuit"=>"$cuit",
	"nombre"=>"$nombre",
//	"domicilio"=>"$domicilio",

	"resp_nombre"=>"$resp_nombre",
	"resp_cargo"=>"$resp_cargo",
	"resp_mail"=>"$resp_mail",
	"resp_telefono"=>"$resp_telefono",

	"cobros_nombre"=>"$cobros_nombre",
	"cobros_horario"=>"$cobros_horario",
	"cobros_mail"=>"$cobros_mail",
	"cobros_telefono"=>"$cobros_telefono",

	"smtp_user"=>"$smtp_user",
	"smtp_pass"=>"$smtp_pass",
	"smtp_host"=>"$smtp_host",
	"smtp_port"=>"$smtp_port",

	"ante_iva"=>"$ante_iva",
	"vendedor"=>"$vendedor",
	"nos_conocio"=>"$nos_conocio"
);


//aquí se conecta con el sitio de Pay per TIC para crear la institucion
$result = curl_post($URL_API["crear_institucion"], $post2paypertic);

$redirect = $_REQUEST["redirect"];
if (empty($redirect)) $redirect = "http://www.paypertic.com.ar";
if (substr($result, 0, 6) === "ERROR:") {
	echo "La Entidad Cobradora NO se registr&oacute; correctamente: $result<br/>";
	echo "Para reintentarlo, vuelva al sitio <a href=\"$redirect\">haciendo clic aqu&iacute;</a>.";
}
else {
	header("refresh:3;url=$redirect"); 
	echo "La Entidad Cobradora se registr&oacute; correctamente: $result<br/>";
	echo "Recibir&aacute; un e-mail en la cuenta <b>$resp_mail</b> con las instrucciones para utilizar la plataforma Pay per TIC<br/><br/>";
	echo "En un segundo lo redirigiremos al sitio, si no redirige <a href=\"$redirect\">haga clic aqu&iacute;</a>.";
}


?>
