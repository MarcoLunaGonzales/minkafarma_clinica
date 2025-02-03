<?php
require("conexionmysqli.inc");
require("estilos_almacenes.inc");

$fecha_rptdefault = date("Y-m-d");

echo "<h1>Ventas x Cliente Detallado</h1>"; 

echo"<form method='POST' action='rptVentaPorCliente.php'  target='_blank'>";
	
	echo"\n<table class='texto' align='center' cellSpacing='0' width='50%'>\n";	
	
	echo "<tr>
		<th align='left'>Almacen</th>
		<td><select name='rpt_almacen[]' id='rpt_almacen' class='selectpicker' data-style='btn btn-success' size='4' multiple>";
	$sql="SELECT cod_almacen, nombre_almacen from almacenes order by 2";
	$resp=mysqli_query($enlaceCon,$sql);
	while($dat=mysqli_fetch_array($resp))
	{	$codigo_almacen=$dat[0];
		$nombre_almacen=$dat[1];
		echo "<option value='$codigo_almacen' selected>$nombre_almacen</option>";
	}
	echo "</select></td></tr>";

	echo "<tr>
			<th align='left'>Cliente</th>
			<td>
				<select name='rpt_cliente[]' class='texto' data-style='btn btn-success' size='15' data-live-search='true' multiple>";
				$sql  = "SELECT c.cod_cliente, CONCAT(c.nombre_cliente) as cliente FROM clientes c order by 2";
				$resp = mysqli_query($enlaceCon,$sql);
				while($data = mysqli_fetch_array($resp))
				{	$codigo = $data[0];
					$nombre = $data[1];
					echo "<option value='$codigo'>$nombre</option>";
				}
				echo "</select>
			</td>
		</tr>";

	// echo "<span>
	// 				<input type='checkbox' id='verTodo' name='verTodo' value='1' checked/>
	// 				<label for='verTodo'>Ver todo</label>
	// 			</span>";

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
	
	
	echo"\n </table><br>";
	echo "<center><input type='submit' name='reporte' value='Ver Reporte' class='boton2'>
	</center><br>";
	echo"</form>";
	echo "</div>";

?>
<script>
    $(document).ready(function() {
        $('#verTodo').change(function() {
            var select = $('select[name="rpt_cliente[]"]');
            if ($(this).is(':checked')) {
                select.find('option').prop('selected', false);
                select.css('pointer-events', 'none');
                select.attr('disabled', true);
                select.selectpicker('refresh');
            } else {
                select.css('pointer-events', 'auto');
                select.attr('disabled', false);
                select.selectpicker('refresh');
            }
        });
    });
</script>