<script language='JavaScript'>
function envia_formulario(f)
{	var rpt_territorio,fecha_ini, fecha_fin, rpt_ver;
	rpt_territorio=f.rpt_territorio.value;
	fecha_ini=f.exafinicial.value;
	fecha_fin=f.exaffinal.value;
	window.open('rptVentasDetallado.php?rpt_territorio='+rpt_territorio+'&fecha_ini='+fecha_ini+'&fecha_fin='+fecha_fin+'','','scrollbars=yes,status=no,toolbar=no,directories=no,menubar=no,resizable=yes,width=1000,height=800');			
	return(true);
}

function actualizarDatosPersonal(){
    var parametros={"codigo":0};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "ajaxCalcularDatosPersonal.php",
        data: parametros,   
        success:  function (resp) { 
          //alert(resp);
          document.getElementById("rpt_personal").innerHTML=resp;
          $("#rpt_personal").selectpicker('refresh');      
        }
    });
 }
</script>
<?php

require("conexionmysqli.php");
require("estilos_almacenes.inc");

error_reporting(E_ALL);
ini_set('display_errors', '1');


$fecha_rptdefault=date("Y-m-d");
echo "<h1>Reporte Detallado x Documento y Producto</h1>";

echo"<form method='post' action='rptVentasDetallado.php' target='blank'>";

	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";
	echo "<tr><th align='left'>Almacen</th>
	<td><select name='rpt_territorio' class='selectpicker' data-style='btn btn-success'>";
	
	$sql="select cod_ciudad, descripcion from ciudades order by descripcion";
	$resp=mysqli_query($enlaceCon,$sql);
	echo "<option value=''></option>";
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_ciudad=$dat[0];
		$nombre_ciudad=$dat[1];
		echo "<option value='$codigo_ciudad'>$nombre_ciudad</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><th align='left'>Personal</th><td>
		<select name='rpt_personal[]' id='rpt_personal' class='selectpicker form-control' data-live-search='true' data-size='6' data-actions-box='true' data-style='btn btn-danger' multiple>";
		$sql="SELECT codigo_funcionario,CONCAT(paterno,' ',materno,' ',nombres)personal FROM funcionarios where estado=1 order by paterno,materno,nombres ";

	$resp=mysqli_query($enlaceCon,$sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_funcionario=$dat[0];
		$nombre_funcionario=$dat[1];
		if($globalFuncionario==$codigo_funcionario){
		   echo "<option value='$codigo_funcionario' selected>$nombre_funcionario</option>";	
		}else{
		  echo "<option value='$codigo_funcionario' selected>$nombre_funcionario</option>";				
		}
	}
	echo "</select><a href='#' class='btn btn-deffault btn-fab btn-sm'><i class='material-icons' onclick='actualizarDatosPersonal();return false;' title='Listar Todo el Personal (Incluidos Retirados)'>refresh</i></a></td></tr>";

	
	echo "<tr><th align='left'>Fecha inicio:</th>";
			echo" <td><INPUT  type='date' class='texto' value='$fecha_rptdefault' id='fecha_ini' size='10' name='fecha_ini'>";
    		echo"  </td>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha final:</th>";
			echo" <td><INPUT  type='date' class='texto' value='$fecha_rptdefault' id='fecha_fin' size='10' name='fecha_fin'>";
    		echo"  </td>";
	echo "</tr>";
	
	echo"\n </table><br>";
	require('home_almacen.php');
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton'>
	</center><br>";
	echo"</form>";
	echo "</div>";
?>