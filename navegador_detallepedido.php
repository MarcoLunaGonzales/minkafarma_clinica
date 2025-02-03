<?php

	require("conexionmysqli.php");
	require('estilos_almacenes_central_sincab.php');
	require("funciones.php");
	require("funcion_nombres.php");

	$cod_pedido = $_GET['cod_pedido'];

	$sqlEmpresa  = "SELECT nombre, nit, direccion from datos_empresa";
	$respEmpresa = mysqli_query($enlaceCon,$sqlEmpresa);
	$datEmpresa	 = mysqli_fetch_array($respEmpresa);
	$nombreEmpresa = $datEmpresa[0];//$nombreEmpresa=mysql_result($respEmpresa,0,0);
	$nitEmpresa	   = $datEmpresa[1];//$nitEmpresa=mysql_result($respEmpresa,0,1);
	$direccionEmpresa = $datEmpresa[2];//$direccionEmpresa=mysql_result($respEmpresa,0,2);
	
	
	$sql="SELECT p.numero, 
				p.observaciones,
				a.nombre_almacen,
				td.nombre as tipo_doc,
				c.nombre_cliente, 
				p.nit,
				p.monto_final,
				DATE_FORMAT(p.fecha,'%d-%m-%Y %H:%i:%s') as fecha, 
				CONCAT(f.nombres, ' ', f.paterno, ' ', f.materno) as vendedor,
				tp.nombre_tipopago as tipopago,
				ptd.descripcion as documento_identidad,
				p.siat_complemento
		FROM pedidos p
		LEFT JOIN almacenes a ON a.cod_almacen = p.cod_almacen
		LEFT JOIN tipos_docs td ON td.codigo = p.cod_tipo_doc
		LEFT JOIN clientes c ON c.cod_cliente = p.cod_cliente
		LEFT JOIN funcionarios f ON f.codigo_funcionario = p.cod_funcionario
		LEFT JOIN tipos_pago tp ON tp.cod_tipopago = p.cod_tipopago
		LEFT JOIN siat_sincronizarparametricatipodocumentoidentidad ptd ON ptd.codigo = p.siat_codigotipodocumentoidentidad
		WHERE p.codigo = '$cod_pedido'
		LIMIT 1";
	$resp=mysqli_query($enlaceCon,$sql);
	$dat=mysqli_fetch_array($resp);

	$numero	  = $dat['numero'];
	$tipo_doc = $dat['tipo_doc'];
	$fecha 	  = $dat['fecha'];
	$nombre_almacen = $dat['nombre_almacen'];
	$observaciones  = $dat['observaciones'];
	$monto_final    = $dat['monto_final'];
		
	echo "<table class='texto' align='center'>";
	echo "<tr>
			<td colspan='3' align='center'>
				<h2 class='titulo-pedido mb-0'>
					<b>Detalle de Pedido</b>
				</h2>
			</td>
		</tr>";

	echo "<tr><td align='center' width='30%'><b>$nombreEmpresa</b></td>
	<td align='center' width='30%'>$tipo_doc<br>Nro. <b>$numero</b></td>
	<td align='center' width='30%'>Fecha Pedido: <b>$fecha</b></td>
	</tr>";
	
	echo "<tr><td align='center' class='bordeNegroTdMod'>Almacen Origen: <b>$nombre_almacen</b></td>
	<td align='center'>Monto Final: <b>".(number_format($monto_final, 2))."</b></td><td align='center'>Observaciones: <b>$observaciones</b></td></tr>";
			
	echo "</table><br>";

	echo "<table border='0' class='texto' cellspacing='0' width='70%' align='center'>";
	
	echo "<tr>
			<th width='5%'>#</th>
			<th width='50%'>Producto</th>
			<th width='5%'>Cantidad</th>
			<th width='10%'>Precio</th>
			<th width='10%'>Descuento</th>
			<th width='10%'>SubTotal</th>
		</tr>";
	
	//echo "<tr><td colspan='4'>&nbsp;</td></tr>";
	echo "<form method='post' action=''>";
	
	$sql_detalle="SELECT ma.descripcion_material, pd.cantidad_unitaria, pd.precio, pd.descuento, pd.monto, pd.orden
				FROM pedidos_detalle pd
				LEFT JOIN material_apoyo ma ON ma.codigo_material = pd.cod_producto
				WHERE pd.cod_pedido = '$cod_pedido'
				ORDER BY pd.orden ASC";
	
	$resp_detalle=mysqli_query($enlaceCon,$sql_detalle);
	$indice		= 1;
	$montoTotal = 0;

	while($dat_detalle=mysqli_fetch_array($resp_detalle)){
		$nombre_material 	= $dat_detalle['descripcion_material'];
		$cantidad_unitaria 	= $dat_detalle['cantidad_unitaria'];
		$precio 			= redondear2($dat_detalle['precio']);
		$descuento 			= redondear2($dat_detalle['descuento']);
		$subtotal 			= redondear2($dat_detalle['monto']);
		$orden 				= $dat_detalle['orden'];
		
		echo "<tr>
			<td class='bordeNegroTdMod'>$indice</td>
			<td class='bordeNegroTdMod'>$nombre_material</td>
			<td align='center' class='bordeNegroTdMod'>$cantidad_unitaria</td>
			<td class='bordeNegroTdMod' align='right'>$precio</td>
			<td class='bordeNegroTdMod' align='right'>$descuento</td>
			<td class='bordeNegroTdMod' align='right'>$subtotal</td>
		</tr>";
		$indice++;

		$montoTotal = $montoTotal + $subtotal;
	
	}
	echo "<tr>
		<td class='bordeNegroTdMod' align='right' colspan='5'><b>TOTAL:</b></td>
		<td class='bordeNegroTdMod' align='right'>".(redondear2($montoTotal))."</td>
	</tr>";
	
	echo "</table>";
?>
<style>
	.titulo-pedido {
		font-size: 2em;
		color: #007bff;
		text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.1);
		margin-bottom: 20px;
		border-bottom: 2px solid #007bff;
		font-family: 'Arial', sans-serif;
	}
</style>