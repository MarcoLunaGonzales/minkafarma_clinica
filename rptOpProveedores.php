<?php

require("conexionmysqli2.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault=date("Y-m-d");



echo "<h1>Proveedores</h1><br>";

echo"<form method='post' action='rptProveedores.php' target='_BLANK'>";

	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";

	echo "<tr><th align='left'>Proveedor / Distribuidor:</th>";
	echo "<th>
	<select name='rpt_proveedores[]' class='texto' multiple size='14'>";
	$sql="SELECT cod_proveedor, nombre_proveedor from proveedores where estado=1";
	$resp=mysqli_query($enlaceCon, $sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo=$dat[0];
		$nombre=$dat[1];
		echo "<option value='$codigo' selected>$nombre</option>";
	}
	echo "</select></td></tr>";

	
	echo"</table><br>";



	echo "<center><input type='submit' name='reporte' value='VER REPORTE' class='boton-verde'>


	</center><br>";//	<input type='button' name='reporte' value='Menos Rentables' onClick='envia_formulario(this.form,0)' class='btn btn-rose'>
	echo"</form>";
	echo "</div>";
?>