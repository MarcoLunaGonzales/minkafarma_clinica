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

$rptAlmacenS = "";
if($rptAlmacen != ""){
	$rptAlmacenS = implode(",",$rptAlmacen);
}
$rptClienteS = "";
if($rptCliente != ""){
	$rptClienteS = implode(",",$rptCliente);
}

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
					WHERE c.cod_cliente in ($rptClienteS)";
	$respCliente = mysqli_query($enlaceCon,$sqlCliente);
	if ($respCliente) {
		$data = mysqli_fetch_assoc($respCliente);
		if ($data) {
			$clientes = $data['nombres'];
		}
	}
}

?>
<center>
	<h5>Reporte Ventas x Cliente Detallado
		<br>Almacen: <strong><?=$nombre_almacen?></strong>
		<br>Periodo: <strong><?=$rptFechaInicio?>  a  <?=$rptFechaFinal?></strong>
		<br>Clientes: <strong><?=$clientes?></strong>
	</h5>
</center>
<table border=0 align='center' class='texto' width='70%'>
	<thead style="background: gray;">
		<tr>
			<th style="text-align:left !important;" width="1%">#</th>
			<th style="text-align:left !important;" width="5%">Nro.Doc</th>
			<th style="text-align:left !important;" width="10%">Fecha</th>
			<th style="text-align:left !important;" width="14%">Cliente</th>
			<th style="text-align:left !important;" width="10%">Tipo Documento</th>
			<th style="text-align:left !important;" width="10%">Tipo Pago</th>
			<th style="text-align:left !important;" width="10%">Monto</th>
			<th style="text-align:left !important;" width="10%">Descuento</th>
			<th style="text-align:left !important;" width="10%">Monto Final</th>
			<th style="text-align:left !important;" width="40%">Detalle</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$consulta = "SELECT s.cod_salida_almacenes, 
								s.fecha, 
								s.hora_salida, 
								(select a.nombre_almacen from almacenes a where a.cod_almacen=s.almacen_destino) as almacen, 
								(select td.nombre from tipos_docs td where td.codigo = s.cod_tipo_doc) as tipo_documento,
								s.observaciones, 
								s.estado_salida, 
								s.nro_correlativo, 
								s.salida_anulada, 
								s.almacen_destino, 
								(select concat(c.nombre_cliente) from clientes c where c.cod_cliente = s.cod_cliente) as nombre_cliente, 
								s.cod_cliente,
								s.cod_tipo_doc, 
								razon_social, 
								nit,
								(select concat(f.paterno,' ',f.nombres) from funcionarios f where f.codigo_funcionario=s.cod_chofer) as vendedor,
								(select nombre_tipopago from tipos_pago where cod_tipopago = s.cod_tipopago) as tipoPago,
								s.cod_chofer, 
								s.cod_tipopago, 
								s.monto_total, s.descuento,
								s.monto_final, 
								DATE_FORMAT(fecha, '%d-%m-%Y') as fecha
					FROM salida_almacenes s
					WHERE s.cod_almacen in ($rptAlmacenS)
					AND s.cod_tiposalida = 1001 AND s.salida_anulada=0 ";
			if (!empty($rptClienteS) && empty($rptVerTodo)) {
				$consulta .= " AND s.cod_cliente IN ($rptClienteS)";
			}
			if($rptFechaInicio != "" && $rptFechaFinal != ""){
				$consulta .= " AND s.fecha BETWEEN '$rptFechaInicio' AND '$rptFechaFinal' ";
			}
			$consulta .= " ORDER BY s.fecha DESC, s.hora_salida DESC";

			//echo $consulta;
			
			$respCab = mysqli_query($enlaceCon, $consulta);
			
			$index = 0;
			$montoTotalReporte=0;
			while($dataCab = mysqli_fetch_array($respCab)){
				$cod_salida_almacenes = $dataCab['cod_salida_almacenes'];
				$nombreTipoDoc = nombreTipoDoc($enlaceCon, $dataCab['cod_tipo_doc']);
				$index++;
				$montoTotalReporte+=$dataCab['monto_final'];
		?>
			<tr>
				<td><?= $index ?></td>
				<td><?= $nombreTipoDoc.' - '.$dataCab['nro_correlativo'] ?></td>
				<td><?=$dataCab['fecha']?></td>
				<td><?=$dataCab['nombre_cliente']?></td>
				<td><?=$dataCab['tipo_documento']?></td>
				<td><?=$dataCab['tipoPago']?></td>
				<td><?=number_format($dataCab['monto_total'], 2)?></td>
				<td><?=number_format($dataCab['descuento'], 2)?></td>
				<td><?=number_format($dataCab['monto_final'], 2)?></td>
				<td style="padding:3px;">
					<table style="font-size: 10px;">
						<thead style="background: gray;">
							<tr>
								<th style="padding:3px;" width="70%">Producto</th>
								<th style="padding:3px;" width="70%">Lote</th>
								<th style="padding:3px;" width="70%">FV</th>
								<th style="padding:3px;" width="10%">Cantidad</th>
								<th style="padding:3px;" width="10%">Precio</th>
								<th style="padding:3px;" width="10%">Descuento</th>
								<th style="padding:3px;" width="10%">Monto</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$consultaDetalle = "SELECT ma.descripcion_material, sda.cantidad_unitaria, sda.precio_unitario, sda.descuento_unitario, sda.lote, sda.fecha_vencimiento
													FROM salida_detalle_almacenes sda
													LEFT JOIN material_apoyo ma ON ma.codigo_material = sda.cod_material
													WHERE sda.cod_salida_almacen = '$cod_salida_almacenes'";
								// echo $consultaDetalle;

								$respDetalle = mysqli_query($enlaceCon, $consultaDetalle);
								
								while($dataDetalle = mysqli_fetch_array($respDetalle)){
									$montoProducto=($dataDetalle['cantidad_unitaria']*$dataDetalle['precio_unitario'])-$dataDetalle['descuento_unitario'];
							?>
								<tr>
									<td><?=$dataDetalle['descripcion_material']?></td>
									<td><?=$dataDetalle['lote']?></td>
									<td><?=$dataDetalle['fecha_vencimiento']?></td>
									<td><?=number_format($dataDetalle['cantidad_unitaria'], 2)?></td>
									<td><?=number_format($dataDetalle['precio_unitario'], 2)?></td>
									<td><?=number_format($dataDetalle['descuento_unitario'], 2)?></td>
									<td><?=number_format($montoProducto, 2)?></td>
								</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php
			}
		?>
							<tr>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th>-</th>
								<th><b><?=number_format($montoTotalReporte,2)?></b></th>
								<th>-</th>
							</tr>
	</tbody>
</table>