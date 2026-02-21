<?php
require "funciones.php";
require_once "../conexionmysqlipdf.inc";
require "../funciones.php";

date_default_timezone_set('America/La_Paz');
error_reporting(E_ALL);
ini_set('display_errors','1');

/****************************************************/
/* VALIDAR PARAMETROS */
/****************************************************/
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fechaFin    = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

if($fechaInicio=='' || $fechaFin==''){
    die("Debe enviar fecha_inicio y fecha_fin");
}

$fechaInicio = date('Y-m-d', strtotime($fechaInicio));
$fechaFin    = date('Y-m-d', strtotime($fechaFin));

/****************************************************/
/* CONSULTA MAESTRO POR RANGO */
/****************************************************/
$queryMaestro = "
SELECT 
    sa.cod_salida_almacenes AS cod_salida_almacen,
    sa.cod_cliente AS cod_paciente,
    sa.nro_correlativo AS nro_factura_siat,
    '34' AS cod_area,
    MONTH(sa.fecha) AS mes,
    YEAR(sa.fecha) AS gestion,
    1 AS cod_entidad,
    1 AS cod_unidad,
    sa.cod_tipopago AS cod_tipo_pago,
    sa.razon_social,
    sa.fecha
FROM salida_almacenes sa
WHERE DATE(sa.fecha) BETWEEN '$fechaInicio' AND '$fechaFin'
AND sa.cod_salida_almacenes IS NOT NULL
ORDER BY sa.fecha ASC, sa.cod_salida_almacenes ASC
";

$resultMaestro = mysqli_query($enlaceCon, $queryMaestro);

if(!$resultMaestro){
    die("Error en consulta maestro: ".mysqli_error($enlaceCon));
}

$dataProcesada = [];
$totalProcesados = 0;
$totalErrores = 0;

/****************************************************/
/* URL FINANCIERO */
/****************************************************/
$url_financiero = obtenerValorConfiguracion($enlaceCon, '-5');
$json_url = $url_financiero . '/factura/backend_comprobante_new.php';

/****************************************************/
/* RECORRER SALIDAS */
/****************************************************/
while ($salida = mysqli_fetch_assoc($resultMaestro)) {

    $codSalidaAlmacen = $salida['cod_salida_almacen'];

    /************* DETALLE *************/
    $queryDetalle = "
        SELECT 
            '34' AS cod_area,
            '656' AS cod_servicio,
            1 AS cantidad,
            SUM(sd.cantidad_unitaria * sd.precio_unitario) AS precio,
            IFNULL(SUM(sd.descuento_unitario),0) AS descuento
        FROM salida_detalle_almacenes sd
        LEFT JOIN material_apoyo m 
            ON m.codigo_material = sd.cod_material
        WHERE sd.cod_salida_almacen = '$codSalidaAlmacen'
    ";

    $resultDetalle = mysqli_query($enlaceCon, $queryDetalle);

    $detalles = [];
    while ($detalle = mysqli_fetch_assoc($resultDetalle)) {
        $detalles[] = $detalle;
    }
    mysqli_free_result($resultDetalle);

    // Si no tiene detalle, saltar
    if(empty($detalles)){
        $totalErrores++;
        continue;
    }

    $salida['detalles'] = $detalles;

    /****************************************************/
    /* ENVIAR AL SERVICIO */
    /****************************************************/
    $ch = curl_init($json_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($salida));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    /****************************************************/
    /* CONTROL BASICO */
    /****************************************************/
    if($httpCode==200){
        $totalProcesados++;
        $dataProcesada[] = [
            'cod_salida' => $codSalidaAlmacen,
            'status' => 'OK',
            'response' => $response
        ];
    }else{
        $totalErrores++;
        $dataProcesada[] = [
            'cod_salida' => $codSalidaAlmacen,
            'status' => 'ERROR',
            'http_code' => $httpCode,
            'response' => $response
        ];
    }
}

mysqli_free_result($resultMaestro);

/****************************************************/
/* REPORTE FINAL */
/****************************************************/
echo "<pre>";
echo "=====================================\n";
echo "REPORTE GENERACION COMPROBANTES\n";
echo "Rango: $fechaInicio → $fechaFin\n";
echo "Procesados OK: $totalProcesados\n";
echo "Errores: $totalErrores\n";
echo "=====================================\n\n";
print_r($dataProcesada);
echo "</pre>";
?>