<?php
ob_start();
ini_set('display_errors', '0');
ini_set('log_errors', '1');
date_default_timezone_set('America/La_Paz');

require_once 'conexionmysqli.php';

function responderJson($estadoHttp, $contenido)
{
    // Se elimina cualquier salida generada antes de la respuesta JSON.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    http_response_code($estadoHttp);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');

    $opcionesJson = JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_PRETTY_PRINT;

    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $opcionesJson |= JSON_INVALID_UTF8_SUBSTITUTE;
    }

    $json = json_encode($contenido, $opcionesJson);

    if ($json === false) {
        http_response_code(500);
        $json = json_encode(array(
            'ok' => false,
            'mensaje' => 'No fue posible convertir la respuesta a JSON.',
            'datos' => array()
        ), $opcionesJson);
    }

    echo $json;
    exit;
}

function fechaValida($fecha)
{
    $objeto = DateTime::createFromFormat('Y-m-d', $fecha);
    return $objeto !== false && $objeto->format('Y-m-d') === $fecha;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderJson(405, array(
        'ok' => false,
        'mensaje' => 'Método no permitido. Utilice GET.',
        'datos' => array()
    ));
}

$primerDiaMes = date('Y-m-01');
$ultimoDiaMes = date('Y-m-t');

$fechaInicio = isset($_GET['fecha_inicio']) ? trim($_GET['fecha_inicio']) : $primerDiaMes;
$fechaFin = isset($_GET['fecha_fin']) ? trim($_GET['fecha_fin']) : $ultimoDiaMes;
$nroCorrelativo = isset($_GET['nro_correlativo']) ? trim($_GET['nro_correlativo']) : '';
$nit = isset($_GET['nit']) ? trim($_GET['nit']) : '';
$razonSocial = isset($_GET['razon_social']) ? trim($_GET['razon_social']) : '';
$codAlmacen = isset($_GET['cod_almacen']) ? trim($_GET['cod_almacen']) : '';

if (!fechaValida($fechaInicio) || !fechaValida($fechaFin)) {
    responderJson(422, array(
        'ok' => false,
        'mensaje' => 'Las fechas deben tener el formato YYYY-MM-DD.',
        'datos' => array()
    ));
}

if ($fechaInicio > $fechaFin) {
    responderJson(422, array(
        'ok' => false,
        'mensaje' => 'La fecha inicial no puede ser mayor que la fecha final.',
        'datos' => array()
    ));
}

$inicio = new DateTime($fechaInicio);
$fin = new DateTime($fechaFin);
if ($inicio->diff($fin)->days > 366) {
    responderJson(422, array(
        'ok' => false,
        'mensaje' => 'El rango máximo permitido es de 366 días.',
        'datos' => array()
    ));
}

$sql = "SELECT
            s.cod_salida_almacenes AS cod_salida_almacen,
            s.fecha,
            s.hora_salida,
            s.nro_correlativo,
            s.razon_social,
            s.nit,
            s.monto_final,
            s.cod_tipo_doc,
            s.salida_anulada,
            s.siat_estado_facturacion,
            s.cod_almacen,
            COALESCE(c.nombre_cliente, '') AS nombre_cliente,
            COALESCE(tp.nombre_tipopago, '') AS tipo_pago,
            TRIM(COALESCE(CONCAT_WS(' ', f.paterno, f.materno, f.nombres), '')) AS vendedor,
            COALESCE(std.descripcion, '') AS tipo_documento_identidad
        FROM salida_almacenes s
        INNER JOIN tipos_salida ts
            ON ts.cod_tiposalida = s.cod_tiposalida
        LEFT JOIN clientes c
            ON c.cod_cliente = s.cod_cliente
        LEFT JOIN tipos_pago tp
            ON tp.cod_tipopago = s.cod_tipopago
        LEFT JOIN funcionarios f
            ON f.codigo_funcionario = s.cod_chofer
        LEFT JOIN siat_sincronizarparametricatipodocumentoidentidad std
            ON std.codigoClasificador = s.siat_codigotipodocumentoidentidad
        WHERE s.cod_tiposalida = 1001
          AND s.cod_tipo_doc IN (1, 4)
          ";

$tipos = '';
$parametros = array();

if ($nroCorrelativo !== '') {
    $sql .= ' AND s.nro_correlativo = ?';
    $tipos .= 's';
    $parametros[] = $nroCorrelativo;
}

if ($nit !== '') {
    $sql .= ' AND s.nit LIKE ?';
    $tipos .= 's';
    $parametros[] = '%' . $nit . '%';
}

if ($razonSocial !== '') {
    $sql .= ' AND s.razon_social LIKE ?';
    $tipos .= 's';
    $parametros[] = '%' . $razonSocial . '%';
}

if ($codAlmacen !== '') {
    $sql .= ' AND s.cod_almacen = ?';
    $tipos .= 'i';
    $parametros[] = (int) $codAlmacen;
}

$sql .= ' ORDER BY s.fecha DESC, s.hora_salida DESC, s.nro_correlativo DESC LIMIT 100';

$stmt = mysqli_prepare($enlaceCon, $sql);
if (!$stmt) {
    responderJson(500, array(
        'ok' => false,
        'mensaje' => 'No fue posible preparar la consulta de ventas.',
        'datos' => array()
    ));
}

$argumentosBind = array($stmt, $tipos);
foreach ($parametros as $indice => $valor) {
    $parametros[$indice] = $valor;
    $argumentosBind[] = &$parametros[$indice];
}
call_user_func_array('mysqli_stmt_bind_param', $argumentosBind);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    responderJson(500, array(
        'ok' => false,
        'mensaje' => 'No fue posible ejecutar la consulta de ventas.',
        'datos' => array()
    ));
}

$resultado = mysqli_stmt_get_result($stmt);
if ($resultado === false) {
    mysqli_stmt_close($stmt);
    responderJson(500, array(
        'ok' => false,
        'mensaje' => 'El servidor MySQL debe tener habilitada la extensión mysqlnd.',
        'datos' => array()
    ));
}

$ventas = array();
while ($fila = mysqli_fetch_assoc($resultado)) {
    $ventas[] = array(
        'cod_salida_almacen' => (int) $fila['cod_salida_almacen'],
        'fecha' => $fila['fecha'],
        'hora_salida' => $fila['hora_salida'],
        'nro_correlativo' => $fila['nro_correlativo'],
        'razon_social' => $fila['razon_social'],
        'nit' => $fila['nit'],
        'monto_final' => (float) $fila['monto_final'],
        'cod_tipo_doc' => (int) $fila['cod_tipo_doc'],
        'salida_anulada' => (int) $fila['salida_anulada'],
        'siat_estado_facturacion' => $fila['siat_estado_facturacion'],
        'cod_almacen' => (int) $fila['cod_almacen'],
        'nombre_cliente' => $fila['nombre_cliente'],
        'tipo_pago' => $fila['tipo_pago'],
        'vendedor' => $fila['vendedor'],
        'tipo_documento_identidad' => $fila['tipo_documento_identidad']
    );
}

mysqli_free_result($resultado);
mysqli_stmt_close($stmt);

responderJson(200, array(
    'ok' => true,
    'mensaje' => 'Consulta realizada correctamente.',
    'total' => count($ventas),
    'filtros' => array(
        'fecha_inicio' => $fechaInicio,
        'fecha_fin' => $fechaFin,
        'nro_correlativo' => $nroCorrelativo,
        'nit' => $nit,
        'razon_social' => $razonSocial,
        'cod_almacen' => $codAlmacen
    ),
    'datos' => $ventas
));
