<script>
	function enviar_reporte1(f){
		f.action='rptUtilidadesDocItem.php';
		f.submit();
	}
	function enviar_reporte2(f){
		f.action='rptUtilidadesDocItem2.php';
		f.submit();
	}
	function enviar_reporte3(f){
		f.action='rptUtilidadesDocItem3.php';
		f.submit();
	}
</script>
<?php
require("conexionmysqli2.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault=date("Y-m-d");
echo "<h1>Reporte Costos x Documento e Item</h1>";
echo"<form method='post' action='rptUtilidadesDocItem.php' target='_blank'>";

	echo"<center><table class='texto'>\n";
	echo "<tr>
	<th align='left'>Territorio</th>
	<td>
	<select name='rpt_territorio' class='texto' required>";
	$sql="select cod_ciudad, descripcion from ciudades order by descripcion";
	$resp=mysqli_query($enlaceCon, $sql);
	echo "<option value=''></option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_ciudad=$dat[0];
		$nombre_ciudad=$dat[1];
		echo "<option value='$codigo_ciudad'>$nombre_ciudad</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><th align='left'>Fecha Inicio:</th>";
			echo" <td>
			<input  type='date' class='texto' value='$fecha_rptdefault' id='exafinicial' name='exafinicial' required>";
    		echo"  </td>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha Final:</th>";
			echo" <td>
			<input type='date' class='texto' value='$fecha_rptdefault' id='exaffinal' name='exaffinal' required>";
    		echo" </td>";
	echo "</tr>";
	
	echo"\n </table><br>";
	require('home_almacen.php');
	echo "<center><input type='button' onclick='enviar_reporte1(this.form)' name='reporte' value='Costo Ventas' class='boton'>
		<input type='button' onclick='enviar_reporte2(this.form)' name='reporte2' value='Costo Ventas 2' class='boton2'>
		<input type='button' onclick='enviar_reporte3(this.form)' name='reporte2' value='Costo Salidas' class='boton-azul'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>