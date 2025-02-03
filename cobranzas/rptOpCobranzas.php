<?php
require("../conexionmysqli.inc");
require("../estilos_almacenes.inc");


$fecha_rptdefault_ini=date("Y-m-01");
$fecha_rptdefault=date("Y-m-d");

echo "<table align='center' class='textotit'><tr><th>Reporte de Cobros</th></tr></table><br>";
echo"<form method='get' action='rptCobranzas.php' target='_blank'>";

	echo"\n<table class='texto' border='1' align='center' cellSpacing='0' width='50%'>\n";
	echo "<tr><th align='left'>Territorio</th><td>
	<select name='rpt_territorio' class='selectpicker' data-style='btn btn-info' data-show-subtext='true' data-live-search='true' required>";
	$sql="select cod_ciudad, descripcion from ciudades order by descripcion";
	$resp=mysqli_query($enlaceCon, $sql);
	echo "<option value=''></option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_ciudad=$dat[0];
		$nombre_ciudad=$dat[1];
		echo "<option value='$codigo_ciudad'>$nombre_ciudad</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><th align='left'>Funcionario</th><td>
	<select name='rpt_funcionario' class='selectpicker' data-style='btn btn-info' data-show-subtext='true' data-live-search='true' required>";
	$sql="SELECT f.codigo_funcionario, concat(f.paterno, ' ', f.materno, ' ', f.nombres) from funcionarios f 
		where f.estado=1 order by 2";
	$resp=mysqli_query($enlaceCon, $sql);
	echo "<option value='0'>Todos</option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo=$dat[0];
		$nombre=$dat[1];
		echo "<option value='$codigo'>$nombre</option>";
	}
	echo "</select></td></tr>";


	echo "<tr><th align='left'>Cliente</th><td>
	<select name='rpt_cliente' class='selectpicker' data-style='btn btn-info' data-show-subtext='true' data-live-search='true' required>";
	$sql="select cod_cliente, concat(nombre_cliente) from clientes order by 2";
	$resp=mysqli_query($enlaceCon, $sql);
	echo "<option value='0'>Todos</option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo=$dat[0];
		$nombre=$dat[1];
		echo "<option value='$codigo'>$nombre</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><th align='left'>Fecha inicio:</th>";
			echo" <TD bgcolor='#ffffff'><INPUT  type='date' class='texto' value='$fecha_rptdefault_ini' id='fecha_ini' size='10' name='fecha_ini' required>";
    		echo"  </TD>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha final:</th>";
			echo" <TD bgcolor='#ffffff'><INPUT  type='date' class='texto' value='$fecha_rptdefault' id='fecha_fin' size='10' name='fecha_fin' required>";
    		echo"  </TD>";
	echo "</tr>";
	
	echo"\n </table><br>";
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>