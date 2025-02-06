<?php
header('Content-Type: application/json');
ini_set('memory_limit','1G');

require('../function_formatofecha.php');
require('../conexionmysqli.inc');
require('../funcion_nombres.php');

$fechaInicio = isset($_GET['fechaInicio']) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_GET['fechaInicio']))) : date('Y-m-01 00:00:00');
$fechaFin    = isset($_GET['fechaFin']) ? date('Y-m-d H:i:s', strtotime(str_replace('T', ' ', $_GET['fechaFin']))) : date('Y-m-t 23:59:59');

$rpt_territorio = $_GET['codTipoTerritorio'] ?? '';
$tipoPago 		= $_GET['tipoPago'] ?? '';
$cod_personal 	= $_GET['cod_personal'] ?? '';
$fecha_reporte 	= date("d/m/Y");

$sqlConf 	= "SELECT id, valor FROM configuracion_facturas WHERE id=1";
$respConf 	= mysqli_query($enlaceCon, $sqlConf);
$nombreTxt  = mysqli_result($respConf, 0, 1);

$sqlConf  = "SELECT id, valor FROM configuracion_facturas WHERE id=9";
$respConf = mysqli_query($enlaceCon, $sqlConf);
$nitTxt   = mysqli_result($respConf, 0, 1);

// 1: Farmacia, 2: Clinica
$sql = "SELECT 
            s.cod_salida_almacenes,
            s.cod_tipopago as cod_tipo_pago,
            s.nro_correlativo,
            s.fecha,
            s.hora_salida,
            s.observaciones,
            s.cod_cliente,
            s.monto_total,
            s.descuento,
            s.monto_final,
            s.razon_social,
            s.nit,
            s.nro_correlativo as nro_factura_siat,
            s.cod_salida_almacenes as idTransaccion_siat,
            s.created_at,
            s.salida_anulada,
            SUM(ds.cantidad_unitaria*ds.precio_unitario) as total_detalle,
            SUM(ds.descuento_unitario) as descuento_detalle,
            SUM((ds.cantidad_unitaria*ds.precio_unitario)-ds.descuento_unitario) as total_final_detalle,
            td.abreviatura as tipodocumento,
            'FARMACIA' as emision,
            1 as tipo_venta
        FROM salida_almacenes s 
        INNER JOIN funcionarios f ON f.codigo_funcionario = s.cod_funcionario_caja
        LEFT JOIN salida_detalle_almacenes ds ON ds.cod_salida_almacen = s.cod_salida_almacenes
        LEFT JOIN tipos_docs td ON td.codigo = s.cod_tipo_doc
        WHERE f.cod_personal = '$cod_personal'
        AND CONCAT(s.fecha, ' ', s.hora_salida) BETWEEN '$fechaInicio' AND '$fechaFin'  
        AND s.cod_tiposalida = 1001 
        AND s.estado_salida = 1
        AND s.salida_anulada = 0 ";

if(!empty($tipoPago)){
    $sql .= " AND s.cod_tipopago = '$tipoPago' ";
}

if(!empty($rpt_territorio)){
    $sql .= " AND s.cod_almacen IN (SELECT a.cod_almacen FROM almacenes a WHERE a.cod_ciudad IN ($rpt_territorio)) ";
}

$sql .= " GROUP BY s.cod_salida_almacenes
        ORDER BY s.nro_correlativo";

$resp = mysqli_query($enlaceCon, $sql);
$datos = [];

while ($fila = mysqli_fetch_assoc($resp)) {
    $datos[] = $fila;
}

$response = [
    'fechaInicio' 		=> $sql,
    'fechaFin' 	    	=> $fechaFin,
    'nombreRazonSocial' => $nombreTxt,
    'nit' 				=> $nitTxt,
    'detalleVentas' 	=> $datos
];
ob_clean();
echo json_encode($response, JSON_PRETTY_PRINT);
?>
