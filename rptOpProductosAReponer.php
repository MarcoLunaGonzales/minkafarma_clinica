<script language='JavaScript'>
function envia_formulario(f)
{	var rpt_territorio,fecha_ini, fecha_fin, rpt_ver;
	rpt_territorio=f.rpt_territorio.value;
	fecha_ini=f.exafinicial.value;
	fecha_fin=f.exaffinal.value;
	rpt_ver=f.rpt_ver.value;
	
	var forms = f;
    if(forms.checkValidity()){
		window.open('rptProductosAReponer.php?rpt_territorio='+rpt_territorio+'&fecha_ini='+fecha_ini+'&fecha_fin='+fecha_fin+'&rpt_ver='+rpt_ver,'','scrollbars=yes,status=no,toolbar=no,directories=no,menubar=no,resizable=yes,width=1000,height=800');			
		return(true);    
	} else{
        alert("Debe seleccionar todos los campos del reporte.")
    }
}
</script>
<?php

require("conexionmysqli2.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault=date("Y-m-d");
echo "<table align='center' class='textotit'><tr><th>Productos a Reponer</th></tr></table><br>";
echo"<form method='post' action='rptProductosAReponer.php' target='_blank'>";
	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";

	echo "<tr><th align='left'>Territorio</th><td><select name='rpt_territorio' class='texto' required>";
	$sql="select cod_ciudad, descripcion from ciudades order by descripcion";
	$resp=mysqli_query($enlaceCon, $sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_ciudad=$dat[0];
		$nombre_ciudad=$dat[1];
		echo "<option value='$codigo_ciudad'>$nombre_ciudad</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><th align='left'>Distribuidor</th><td>
		<select name='rpt_distribuidor[]' id='rpt_distribuidor' class='texto' multiple size='7'>";
	$sql="select p.cod_proveedor, p.nombre_proveedor from proveedores p order by 2";
	$resp=mysqli_query($enlaceCon, $sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo=$dat[0];
		$nombre=$dat[1];
		echo "<option value='$codigo' selected>$nombre</option>";
	}
	echo "</select></td></tr>";
	
	echo "<tr><th align='left'>Fecha inicio:</th>";
			echo" <TD>
			<INPUT  type='date' class='texto' value='$fecha_rptdefault' id='fecha_ini' size='10' name='fecha_ini' required>";
    		echo" </TD>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha final:</th>";
			echo" <TD>
			<INPUT  type='date' class='texto' value='$fecha_rptdefault' id='fecha_fin' size='10' name='fecha_fin' required>";
    		echo" </TD>";
	echo "</tr>";

	echo "<tr><th align='left'>Ver</th><td><select name='rpt_ver' class='texto' required>";
	echo "<option value='0'>Solo Productos a reponer</option>";
	echo "<option value='1'>Ver todos los productos vendidos</option>";
	echo "</select></td></tr>";
	
	echo"\n </table><br>";
	require('home_almacen.php');
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton'>
	</center><br>";
	echo"</form>";
	echo "</div>";
?>