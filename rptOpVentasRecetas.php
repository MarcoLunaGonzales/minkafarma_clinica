<?php

require("conexionmysqli2.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault=date("Y-m-d");



echo "<h1>Recetas Registradas</h1><br>";
echo"<form method='post' action='rptVentasRecetaRepo.php' target='_BLANK'>";

	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";


	echo "<tr><th align='left' class='text-muted'>Fecha inicio:</th>";
			echo"<td><input type='date' class='form-control' value='$fecha_rptdefault' id='fecha_ini' size='10' name='fecha_ini'>";
    		echo"</td>";
	echo "</tr>";
	echo "<tr><th align='left' class='text-muted'>Fecha final:</th>";
			echo"<td><input type='date' class='form-control' value='$fecha_rptdefault' id='fecha_fin' size='10' name='fecha_fin'>";
    		echo"</td>";
	echo "</tr>";

	echo "<tr><th align='left'>Ver:</th>
	<td>
	<select name='rpt_ver' id='rpt_ver' class='selectpicker' data-style='btn btn-success'>";
		echo "<option value='0'>Ver Detallado Por Fecha</option>";
		echo "<option value='1'>Ver Detallado Por Medico y Producto</option>";
		echo "<option value='2'>Ver Resumido por Medico</option>";
	echo "</select></td></tr>";

	
	echo"</table><br>";



	echo "<center><input type='submit' name='reporte' value='VER REPORTE' class='boton-verde'>


	</center><br>";//	<input type='button' name='reporte' value='Menos Rentables' onClick='envia_formulario(this.form,0)' class='btn btn-rose'>
	echo"</form>";
	echo "</div>";
?>