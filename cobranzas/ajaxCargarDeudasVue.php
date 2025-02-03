<?php
require("../conexionmysqli.inc");
require("../funciones.php");

$codCliente	    = $_GET['cod_cliente'];
// $codigos_salida = !empty($_GET['codigos_salida']) ? (' AND s.cod_salida_almacenes NOT IN (' . $_GET['codigos_salida'] . ') ') : '';
$codigos_salida = '';
$globalAlmacen  = $_COOKIE['global_almacen'];

$configTipoPago = obtenerValorConfiguracion($enlaceCon, 59);


$sql="SELECT s.cod_salida_almacenes, 
			s.nro_correlativo, 
			(select td.nombre from tipos_docs td where td.codigo = s.cod_tipo_doc) as tipo_doc,
			s.fecha, 
			ROUND(s.monto_final, 2) AS monto_final, 
			ROUND(s.monto_cancelado, 2) AS monto_cancelado, 
			s.cod_cliente, 
			CONCAT(c.nombre_cliente) as nombre_cliente,
			0 as monto_pago
	FROM salida_almacenes s
	LEFT JOIN clientes c ON c.cod_cliente = s.cod_cliente 
	WHERE s.cod_cliente = '$codCliente' 
	AND s.salida_anulada = 0 
	AND s.monto_final > s.monto_cancelado 
	AND s.cod_almacen = '$globalAlmacen' 
	AND s.cod_tiposalida = 1001 
	$codigos_salida ".
	($configTipoPago == 1 ? " AND s.cod_tipopago = 4 " : "") 
	." ORDER BY s.fecha";
// echo $sql;
// exit;
$resp = mysqli_query($enlaceCon, $sql);

$lista_salida_almacenes = array();
$totalSaldoCabVentas = 0;
while ($row = mysqli_fetch_assoc($resp)) {
	$monto 			= $row['monto_final'];
	$montoCancelado = $row['monto_cancelado'];
	$saldo 			= $monto - $montoCancelado;
	$saldo 			= round($saldo,2);

	$salida_almacen = array(
        'cod_salida_almacenes' => $row['cod_salida_almacenes'],
        'nro_correlativo' 	=> $row['nro_correlativo'],
        'tipo_doc' 			=> $row['tipo_doc'],
        'fecha' 			=> $row['fecha'],
        'monto_final' 		=> $row['monto_final'],
        'monto_cancelado' 	=> $row['monto_cancelado'],
        'cod_cliente' 		=> $row['cod_cliente'],
        'nombre_cliente' 	=> $row['nombre_cliente'],
        'monto_pago' 		=> $row['monto_pago'],
		'saldo_cliente'		=> $saldo
    );
    array_push($lista_salida_almacenes, $salida_almacen);
	
	/*Acumulamos en esta variable $totalSaldoCabVentas para validacion del saldo por cliente*/
	$totalSaldoCabVentas += $saldo;
}
// var_dump($lista_salida_almacenes);
// exit;
// Validación
$montoSaldoVerificacion = obtenerMontoCuentaCobrarCliente($codCliente, $globalAlmacen);

$response = array();
$response['status'] = abs($montoSaldoVerificacion - ($totalSaldoCabVentas)) < 1;
if(count($lista_salida_almacenes) == 0){
	$response['status'] 	 = true;
	$response['message'] 	 = "No se encontraron registros.";
    $response['lista_pagos'] = [];
}else if (!$response['status']) {
    // $response['message'] 	 = "REVISION****** ".$montoSaldoVerificacion." ".$totalSaldoCabVentas;
	$response['message'] 	 = "Los montos de cobros no son correctos. Por favor, contacte al administrador.";
    $response['lista_pagos'] = [];
} else {
	$response['status'] 	 = true;
    $response['message'] 	 = "Se obtuvo la lista de pagos";
    $response['lista_pagos'] = $lista_salida_almacenes;
}
ob_clean();
echo json_encode($response);


?>