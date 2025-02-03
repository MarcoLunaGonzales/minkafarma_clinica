<?php

require("conexionmysqli.inc");
require("estilos_almacenes.inc");


$fechaDefaultIni=date("Y-m-01");
$fechaDefaultFin=date("Y-m-d");

echo "<h1>Reporte Ventas x Vendedor</h1><br>";
echo"<form method='get' action='rptVentasxVendedor.php' target='_BLANK'>";

	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";
	echo "<tr><th align='left'>Territorio</th>
		<td><select name='rpt_territorio'  class='selectpicker' data-style='btn btn-success' required>";
	$sql="select cod_ciudad, descripcion from ciudades order by descripcion";
	$resp=mysqli_query($enlaceCon, $sql);
	echo "<option value=''></option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_ciudad=$dat[0];
		$nombre_ciudad=$dat[1];
		echo "<option value='$codigo_ciudad'>$nombre_ciudad</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><th align='left'>Fecha inicio:</th>";
			echo" <TD bgcolor='#ffffff'>
			<INPUT  type='date' class='texto' value='$fechaDefaultIni' id='fecha_ini' size='10' name='fecha_ini' required>";
    		echo" </TD>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha final:</th>";
			echo" <TD bgcolor='#ffffff'>
			<INPUT  type='date' class='texto' value='$fechaDefaultFin' id='fecha_fin' size='10' name='fecha_fin' required>";
    		echo" </TD>";
	echo "</tr>";
	
	echo"\n </table><br>";
	require('home_almacen.php');
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>