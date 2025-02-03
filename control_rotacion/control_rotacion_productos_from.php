<script type="text/javascript">
 function cambiarSubLinea(){
  var categoria=$("#cod_proveedor").val();
  var parametros={"categoria":categoria};
     $.ajax({
        type: "GET",
        dataType: 'html',
        url: "../ajaxCambiarComboLinea.php",
        data: parametros,   
        success:  function (resp) { 
        	//alert(resp);
          $("#rpt_subcategoria").html(resp);
          $(".selectpicker").selectpicker("refresh");
        }
    });
 }
</script>
<?php

require_once("../conexionmysqli.inc");
require_once("../estilos2.inc");

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


$fecha_rptinidefault=date("Y")."-".date("m")."-01";
//$hora_rptinidefault=date("H:i");
$hora_rptinidefault="06:00";
$hora_rptfindefault="23:59";
$fecha_rptdefault=date("Y-m-d");

echo "<h1>Reporte Control de Rotación de Productos</h1><br>";
echo"<form method='post' action='control_rotacion_productos_print.php' target='_blank'>";

	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";
	echo "<tr><th align='left'>Proveedor</th><td>
	<select name='cod_proveedor' id='cod_proveedor' class='selectpicker form-control' data-show-subtext='true' onchange='cambiarSubLinea()' data-live-search='true' required>";
	echo "<option disabled selected value=''>SELECCIONE PROVEEDOR</option>";
	
	$sql="SELECT cod_proveedor,nombre_proveedor from proveedores where estado=1 and cod_proveedor>0 order by nombre_proveedor";
//	echo $sql;
	$resp=mysqli_query($enlaceCon,$sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_proveedor=$dat[0];
		$nombre_proveedor=$dat[1];
			echo "<option value='$codigo_proveedor'>$nombre_proveedor</option>";
	}
	echo "</select></td></tr>";

	echo "<tr><th align='left'>SubLinea</th><td><select name='rpt_subcategoria[]' id='rpt_subcategoria' class='selectpicker form-control' data-actions-box='true' data-show-subtext='true' data-live-search='true' multiple required>";
	


	echo "<tr><th align='left'>Fecha Inicio:</th>";
			echo" <TD bgcolor='#ffffff'>
				<INPUT  type='date' class='texto' value='$fecha_rptdefault' id='exafinicial' size='10' name='exafinicial'><INPUT  type='time' class='texto' value='$hora_rptinidefault' id='exahorainicial' size='10' name='exahorainicial' required>";
    		echo"  </TD>";
	echo "</tr>";
	echo "<tr><th align='left'>Fecha Fin:</th>";
			echo" <TD bgcolor='#ffffff'>
				<INPUT  type='date' class='texto' value='$fecha_rptdefault' id='exaffinal' size='10' name='exaffinal'><INPUT  type='time' class='texto' value='$hora_rptfindefault' id='exahorafinal' size='10' name='exahorafinal' required>";
    		echo"  </TD>";
	echo "</tr>";

?>

<?php
		echo "<tr><th align='left'>Tipo Rotación : </th><td>
	<select name='tipo_rotacion[]' id='tipo_rotacion' class='selectpicker form-control'  data-style='btn btn-primary' data-actions-box='true'  data-show-subtext='true' data-live-search='true' required multiple='true'>";
	echo "<option value='1' selected>CON ROTACIÓN</option>";
	echo "<option value='2' selected>SIN ROTACIÓN</option>";
	echo "<option value='3' selected>SIN ROTACIÓN & CON INGRESOS</option>";
	echo "<option value='4' selected>SIN ROTACIÓN & CON TRASPASOS</option>";
	echo "<option value='5' selected>SIN STOCK INICIAL & SIN ROTACION DESDE INGRESO</option>";
	echo "<option value='6' selected>SIN STOCK INICIAL & CON ROTACION DESDE INGRESO</option>";
	echo "</select></td></tr>";

	echo "<tr><th align='left'>Incluir</th><td>
	<select name='tipo_stock' id='tipo_stock' class='selectpicker form-control'  data-style='btn btn-primary' data-actions-box='true'  required>";
	echo "<option value='1' selected>SALDO INICIAL & SALDO FINAL & VENTAS</option>";
	echo "<option value='2'>SOLO SALDO INICIAL</option>";
	echo "<option value='3'>SOLO SALDO FINAL</option>";
	echo "<option value='4'>SOLO CANTIDAD VENTAS</option>";
	// echo "<option value='4'>SOLO VENTAS</option>";

	echo"\n </table><br>";
	echo "<center><input type='submit' name='reporte' value='Ver Reporte'  class='btn btn-info'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>
