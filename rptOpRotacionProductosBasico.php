<?php
require("conexionmysqli.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault=date("Y-m-d");
$globalCiudad=$_COOKIE['global_agencia'];
$globalAlmacen=$_COOKIE['global_almacen'];
$globalTipoFuncionario=$_COOKIE['globalTipoFuncionario'];
$global_usuario=$_COOKIE['global_usuario'];

if($rpt_territorio==""){
	$rpt_territorio=$globalCiudad;
}
echo "<h1>Reporte de Rotación de Productos</h1>"; 

echo"<form method='POST' action='rptRotacionProductosBasico.php'  target='_blank'>";
	
	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";	
	
	echo "<tr>
		<th align='left'>Almacen</th>
		<td><select name='rpt_almacen[]' id='rpt_almacen' class='selectpicker' data-style='btn btn-success' size='4' multiple>";
	$sql="select cod_almacen, nombre_almacen from almacenes order by 2";
	$resp=mysqli_query($enlaceCon,$sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_almacen=$dat[0];
		$nombre_almacen=$dat[1];
		echo "<option value='$codigo_almacen' selected>$nombre_almacen</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><th align='left'>Distribuidor</th><td><select name='rpt_grupo[]' class='selectpicker' data-style='btn btn-success' size='8' data-live-search='true' multiple data-actions-box='true' multiple>";
	$sql="select p.cod_proveedor, p.nombre_proveedor from proveedores p;";
	$resp=mysqli_query($enlaceCon,$sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo=$dat[0];
		$nombre=$dat[1];
		echo "<option value='$codigo' selected>$nombre</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><th align='left'>Fecha Inicio:</th>";
			echo" <td>
			<INPUT  type='date' class='text' value='$fecha_rptdefault' id='rpt_ini' name='rpt_ini'>
			</td>";
	echo "</tr>";
	echo "<tr>
			<th align='left'>Fecha Final:</th>";
			echo" <td>
			<INPUT  type='date' class='text' value='$fecha_rptdefault' id='rpt_fin' name='rpt_fin'>
			</td>";
	echo "</tr>";


	echo "<tr>
			<th align='left'>Ver Productos con Rotacion menor al:</th>";
			echo"<td>
				<select name='rpt_porcentaje' class='selectpicker' data-style='btn btn-success' id='rpt_porcentaje'>
					<option value='5'>5 %</value>
					<option value='10'>10 %</value>
					<option value='20'>20 %</value>
					<option value='30'>30 %</value>
					<option value='50'>50 %</value>
					<option value='70'>70 %</value>
				</select>
			</td>";
	echo "</tr>";
	
	
	echo"\n </table><br>";
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton2'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>