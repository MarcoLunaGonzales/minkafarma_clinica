<?php


require("conexionmysqli2.inc");
require("funcion_nombres.php");

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

date_default_timezone_set('America/La_Paz');

/****************************************
 * Valores de IP y NAVEGADOR QUE UTILIZA
 ****************************************/
$ipAddress = $_POST['ipAddress'];
$userAgent = $_POST['userAgent'];
/****************************************/

$claveMarcado=$_POST["clave_marcado"];

$fechaActual=date("Y-m-d H:i:s");

$sql="select codigo_funcionario from usuarios_sistema where contrasena='$claveMarcado'";

//echo $sql;

$resp=mysqli_query($enlaceCon, $sql);

//echo $resp;
$numFilas=mysqli_num_rows($resp);

if($numFilas>0){
	while($dat=mysqli_fetch_array($resp)){
		$codUsuario=$dat[0];
		$nombreUsuario=nombreVisitador($enlaceCon, $codUsuario);
		$sqlInsert="insert into marcados_personal (cod_funcionario, fecha_marcado, estado, ip, user_agent) values 
		($codUsuario, '$fechaActual', 1, '$ipAddress', '$userAgent')";
		$respInsert=mysqli_query($enlaceCon, $sqlInsert);
		
		echo "<script language='Javascript'>
			alert('MARCADO EXITOSO!!!!!!!. $nombreUsuario');
			location.href='registrar_marcado.php';
			</script>";
	}
}else{
	echo "<script language='Javascript'>
			alert('ERROR, No se registro el Marcado!!!!.');
			location.href='registrar_marcado.php';
			</script>";	
}
?>