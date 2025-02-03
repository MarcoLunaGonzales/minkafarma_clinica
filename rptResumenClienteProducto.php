<?php
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli.php');
require("funciones.php");
ini_set('max_input_vars', 3000);
 

$rptAlmacen 	= $_POST["rpt_almacen"];
$rptCliente 	= $_POST["rpt_cliente"] ?? '';
$rptVerTodo 	= $_POST["verTodo"] ?? '';
$rptFechaInicio	= $_POST["rpt_ini"];
$rptFechaFinal  = $_POST["rpt_fin"];
$rptTipo  		= $_POST["rpt_tipo"]; // Producto | Cliente

$rptAlmacenS = "";
if($rptAlmacen != ""){
	$rptAlmacenS = implode(",",$rptAlmacen);
}
$rptClienteS = "";
if($rptCliente != ""){
	$rptClienteS = implode(",",$rptCliente);
}
// Almacenes
$sql_nombre_almacen	 = "SELECT nombre_almacen from almacenes where cod_almacen in ($rptAlmacenS)";
$resp_nombre_almacen = mysqli_query($enlaceCon,$sql_nombre_almacen);
$nombre_almacen		 = "";
while($datos_nombre_almacen = mysqli_fetch_array($resp_nombre_almacen)){
	$nombre_almacen .= $datos_nombre_almacen[0]." - ";
}
// Clientes
$clientes = 'TODOS';
if($rptVerTodo != 1){
	$sqlCliente	 = "SELECT GROUP_CONCAT(c.nombre_cliente SEPARATOR ', ') as nombres
					FROM clientes c
					WHERE c.cod_cliente IN ($rptClienteS)";
	$respCliente = mysqli_query($enlaceCon,$sqlCliente);
	if ($respCliente) {
		$data = mysqli_fetch_assoc($respCliente);
		if ($data) {
			$clientes = $data['nombres'];
		}
	}
}

if($rptTipo == 1){
?>
<!-- DETALLA POR PRODUCTO -->
<center>
	<h5>Reporte Ventas x Cliente (Productos)
		<br>Almacen: <strong><?=$nombre_almacen?></strong>
		<br>Periodo: <strong><?=$rptFechaInicio?>  a  <?=$rptFechaFinal?></strong>
		<br>Clientes: <strong><?=$clientes?></strong>
	</h5>
</center>
<table border=0 align='center' class='texto' width='70%'>
	<thead style="background: gray;">
		<tr>
			<th width="5%">#</th>
			<th style="text-align:left !important;" width="80%">Producto</th>
			<th style="text-align:left !important;" width="15%">Monto</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$consulta = "SELECT ma.descripcion_material,
							SUM(sda.cantidad_unitaria) as cantidad, 
							SUM((sda.cantidad_unitaria * sda.precio_unitario)-sda.descuento_unitario) as total
					FROM salida_almacenes s
					INNER JOIN salida_detalle_almacenes sda ON sda.cod_salida_almacen = s.cod_salida_almacenes
					LEFT JOIN material_apoyo ma ON ma.codigo_material = sda.cod_material
					WHERE s.cod_almacen in ($rptAlmacenS)
					AND s.cod_tiposalida = 1001 AND s.salida_anulada=0 ";
			if (!empty($rptClienteS) && empty($rptVerTodo)) {
				$consulta .= " AND s.cod_cliente IN ($rptClienteS)";
			}
			if($rptFechaInicio != "" && $rptFechaFinal != ""){
				$consulta .= " AND s.fecha BETWEEN '$rptFechaInicio' AND '$rptFechaFinal' ";
			}
			$consulta .= " GROUP BY ma.descripcion_material
							ORDER BY total DESC";

			// echo $consulta;
			
			$resp = mysqli_query($enlaceCon, $consulta);
			$index1 	 = 0;
			$montoTotal1 = 0;
			while($data = mysqli_fetch_array($resp)){
				$index1++;
				$montoTotal1 += $data['total'];
		?>
			<tr>
				<td><?=$index1?></td>
				<td><?=$data['descripcion_material']?></td>
				<td><?=number_format($data['total'], 2)?></td>
			</tr>
		<?php
			}
		?>
		<tr>
			<td style="text-align:right !important;" colspan="2">TOTAL</td>
			<td><?=number_format($montoTotal1, 2)?></td>
		</tr>
	</tbody>
</table>
<?php
}else if($rptTipo == 2){	
?>
<!-- DETALLA POR CLIENTES -->
<center>
	<h5>Reporte Ventas x Cliente (Clientes)
		<br>Almacen: <strong><?=$nombre_almacen?></strong>
		<br>Periodo: <strong><?=$rptFechaInicio?>  a  <?=$rptFechaFinal?></strong>
		<br>Clientes: <strong><?=$clientes?></strong>
	</h5>
</center>
<table border=0 align='center' class='texto' width='70%'>
	<thead style="background: gray;">
		<tr>
			<th width="5%">#</th>
			<th style="text-align:left !important;" width="80%">Cliente</th>
			<th style="text-align:left !important;" width="15%">Total Final</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$consulta = "SELECT c.cod_cliente, c.nombre_cliente,
							SUM(sda.cantidad_unitaria) as cantidad, 
							SUM((sda.cantidad_unitaria * sda.precio_unitario)-sda.descuento_unitario) as total
					FROM salida_almacenes s
					INNER JOIN salida_detalle_almacenes sda ON sda.cod_salida_almacen = s.cod_salida_almacenes
					LEFT JOIN clientes c ON c.cod_cliente = s.cod_cliente
					WHERE s.cod_almacen in ($rptAlmacenS)
					AND s.cod_tiposalida = 1001 
					AND s.salida_anulada=0 ";
			if (!empty($rptClienteS) && empty($rptVerTodo)) {
				$consulta .= " AND s.cod_cliente IN ($rptClienteS)";
			}
			if($rptFechaInicio != "" && $rptFechaFinal != ""){
				$consulta .= " AND s.fecha BETWEEN '$rptFechaInicio' AND '$rptFechaFinal' ";
			}
			$consulta .= " GROUP BY c.cod_cliente, c.nombre_cliente
							ORDER BY c.nombre_cliente";

			// echo $consulta;
			
			$resp = mysqli_query($enlaceCon, $consulta);
			
			$index2 	 = 0;
			$montoTotal2 = 0;
			while($data = mysqli_fetch_array($resp)){
				$index2++;
				$montoTotal2 += $data['total'];
		?>
			<tr>
				<td><?=$index2?></td>
				<td><?=$data['nombre_cliente']?></td>
				<td><?=number_format($data['total'], 2)?></td>
			</tr>
		<?php
			}
		?>
		<tr>
			<td style="text-align:right !important;" colspan="2">TOTAL</td>
			<td><?=number_format($montoTotal2, 2)?></td>
		</tr>
	</tbody>
</table>
<?php
}
?>