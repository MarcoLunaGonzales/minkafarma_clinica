<?php

require("conexionmysqli.php");
require("estilos.inc");

$nombre=$_POST["nombre"];
$sql="select max(cod_accionterapeutica)+1 from acciones_terapeuticas";
$resp=mysqli_query($enlaceCon,$sql);
$dat=mysqli_fetch_array($resp);
$codigo=$dat[0];
//$codigo=mysql_result($resp,0,0);

$sql_inserta=mysqli_query($enlaceCon,"insert into acciones_terapeuticas values($codigo,'$nombre',1)");

echo "<script language='Javascript'>
			alert('El proceso se completo correctamente.');
			location.href='navegador_accionester.php';
			</script>";
?>