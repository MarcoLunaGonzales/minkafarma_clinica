<?php
ob_start();

require('funciones.php');
require('conexionmysqli.php');

date_default_timezone_set('America/La_Paz');
header('Content-Type: application/json; charset=utf-8');

/* URL configurada para Financiero Loyola. */
$urlFinanciero = obtenerValorConfiguracion($enlaceCon, '-5');
$jsonUrl = rtrim($urlFinanciero, '/')
    . '/factura/backend_actualizar_tipo_pago_venta.php';

define('URL_BACKEND_TIPO_PAGO_FINANCIERO', $jsonUrl);

define('TIPO_VENTA_FARMACIA', 1);
define('COD_ENTIDAD_FARMACIA', 1);

function responder($ok, $mensaje, $extra = array(), $codigoHttp = 200)
{
    http_response_code($codigoHttp);

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(
        array_merge(
            array('ok' => (bool) $ok, 'mensaje' => $mensaje),
            $extra
        ),
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function llamarBackendFinanciero(
    $codigoSalida,
    $codigoTipoPago,
    $tipoPagoAnterior,
    $codigoEntidad,
    $codigoPaciente
)
{
    $payload = json_encode(array(
        'cod_salida_almacen'   => (int) $codigoSalida,
        'cod_tipo_pago'        => (int) $codigoTipoPago,
        'cod_tipo_pago_anterior'=> (int) $tipoPagoAnterior,
        'cod_entidad'          => (int) $codigoEntidad,
        'cod_paciente'         => (int) $codigoPaciente,
        'tipo_venta'           => TIPO_VENTA_FARMACIA
    ));

    $curl = curl_init(URL_BACKEND_TIPO_PAGO_FINANCIERO);

    if ($curl === false) {
        throw new RuntimeException('No se pudo iniciar la conexión con el sistema financiero.');
    }

    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => array(
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json'
        ),
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 40
    ));

    $respuesta = curl_exec($curl);
    $errorCurl = curl_error($curl);
    $codigoHttp = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($respuesta === false || $errorCurl !== '') {
        throw new RuntimeException(
            'No se pudo conectar con Financiero Loyola: ' . $errorCurl
        );
    }

    $datos = json_decode($respuesta, true);

    if (!is_array($datos)) {
        throw new RuntimeException('Financiero Loyola devolvió una respuesta no válida.');
    }

    $estado = isset($datos['status']) ? (bool) $datos['status'] : false;

    if ($codigoHttp < 200 || $codigoHttp >= 300 || !$estado) {
        $mensaje = isset($datos['message'])
            ? $datos['message']
            : 'No se pudo actualizar el comprobante contable.';

        throw new RuntimeException($mensaje);
    }

    return $datos;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método de solicitud no permitido.', array(), 405);
}

$codigoSalida = isset($_POST['codigo_salida'])
    ? filter_var($_POST['codigo_salida'], FILTER_VALIDATE_INT)
    : false;
$codigoTipoPago = isset($_POST['codigo_tipo_pago'])
    ? filter_var($_POST['codigo_tipo_pago'], FILTER_VALIDATE_INT)
    : false;
$globalAlmacen = isset($_COOKIE['global_almacen'])
    ? (int) $_COOKIE['global_almacen']
    : 0;

if (!$codigoSalida || $codigoSalida <= 0) {
    responder(false, 'El código de la venta no es válido.', array(), 422);
}

if (!in_array((int) $codigoTipoPago, array(1, 2, 3, 4), true)) {
    responder(false, 'El tipo de pago seleccionado no es válido.', array(), 422);
}

if ($globalAlmacen <= 0) {
    responder(false, 'No se encontró el almacén activo de la sesión.', array(), 401);
}

mysqli_begin_transaction($enlaceCon);

try {
    $sqlTipoPago = "SELECT cod_tipopago
                    FROM tipos_pago
                    WHERE cod_tipopago = ?
                    LIMIT 1";
    $stmtTipoPago = mysqli_prepare($enlaceCon, $sqlTipoPago);

    if (!$stmtTipoPago) {
        throw new RuntimeException('No se pudo validar el tipo de pago.');
    }

    mysqli_stmt_bind_param($stmtTipoPago, 'i', $codigoTipoPago);
    mysqli_stmt_execute($stmtTipoPago);
    mysqli_stmt_store_result($stmtTipoPago);

    if (mysqli_stmt_num_rows($stmtTipoPago) === 0) {
        mysqli_stmt_close($stmtTipoPago);
        throw new RuntimeException('El tipo de pago seleccionado no existe.');
    }
    mysqli_stmt_close($stmtTipoPago);

    $sqlVenta = "SELECT cod_tipopago, cod_cliente, monto_final, salida_anulada
                 FROM salida_almacenes
                 WHERE cod_salida_almacenes = ?
                   AND cod_almacen = ?
                   AND fecha = CURDATE()
                 LIMIT 1
                 FOR UPDATE";
    $stmtVenta = mysqli_prepare($enlaceCon, $sqlVenta);

    if (!$stmtVenta) {
        throw new RuntimeException('No se pudo validar la venta.');
    }

    mysqli_stmt_bind_param($stmtVenta, 'ii', $codigoSalida, $globalAlmacen);
    mysqli_stmt_execute($stmtVenta);
    $resultadoVenta = mysqli_stmt_get_result($stmtVenta);
    $venta = mysqli_fetch_assoc($resultadoVenta);
    mysqli_stmt_close($stmtVenta);

    if (!$venta) {
        throw new RuntimeException('No se encontró la venta de hoy seleccionada.');
    }

    if ((int) $venta['salida_anulada'] === 1) {
        throw new RuntimeException('No se puede cambiar el tipo de pago de una venta anulada.');
    }

    $tipoPagoAnterior = (int) $venta['cod_tipopago'];

    if ($tipoPagoAnterior === (int) $codigoTipoPago) {
        mysqli_commit($enlaceCon);
        responder(true, 'La venta ya tiene seleccionado ese tipo de pago.', array(
            'codigo_salida' => (int) $codigoSalida,
            'tipo_pago'     => (int) $codigoTipoPago
        ));
    }

    $sqlActualizar = "UPDATE salida_almacenes
                      SET cod_tipopago = ?
                      WHERE cod_salida_almacenes = ?
                        AND cod_almacen = ?";
    $stmtActualizar = mysqli_prepare($enlaceCon, $sqlActualizar);

    if (!$stmtActualizar) {
        throw new RuntimeException('No se pudo preparar la actualización de la venta.');
    }

    mysqli_stmt_bind_param(
        $stmtActualizar,
        'iii',
        $codigoTipoPago,
        $codigoSalida,
        $globalAlmacen
    );

    if (!mysqli_stmt_execute($stmtActualizar)) {
        mysqli_stmt_close($stmtActualizar);
        throw new RuntimeException('No se pudo actualizar el tipo de pago de la venta.');
    }
    mysqli_stmt_close($stmtActualizar);

    /* Mantiene sincronizada la tabla de pagos cuando la instalación la utiliza. */
    $sqlEliminarPago = "DELETE FROM salida_almacenes_pagos
                        WHERE cod_salida_almacenes = ?";
    $stmtEliminarPago = mysqli_prepare($enlaceCon, $sqlEliminarPago);

    if ($stmtEliminarPago) {
        mysqli_stmt_bind_param($stmtEliminarPago, 'i', $codigoSalida);
        mysqli_stmt_execute($stmtEliminarPago);
        mysqli_stmt_close($stmtEliminarPago);

        $sqlInsertarPago = "INSERT INTO salida_almacenes_pagos
                                (cod_salida_almacenes, cod_tipo_pago, monto)
                            VALUES (?, ?, ?)";
        $stmtInsertarPago = mysqli_prepare($enlaceCon, $sqlInsertarPago);

        if (!$stmtInsertarPago) {
            throw new RuntimeException('No se pudo preparar el detalle del nuevo pago.');
        }

        $montoFinal = (float) $venta['monto_final'];
        mysqli_stmt_bind_param(
            $stmtInsertarPago,
            'iid',
            $codigoSalida,
            $codigoTipoPago,
            $montoFinal
        );

        if (!mysqli_stmt_execute($stmtInsertarPago)) {
            mysqli_stmt_close($stmtInsertarPago);
            throw new RuntimeException('No se pudo registrar el detalle del nuevo pago.');
        }
        mysqli_stmt_close($stmtInsertarPago);
    }

    /*
     * tipo_venta = 1 identifica Farmacia en Financiero Loyola.
     * Si el comprobante falla, se revierte también el cambio local.
     */
    $respuestaFinanciero = llamarBackendFinanciero(
        $codigoSalida,
        $codigoTipoPago,
        $tipoPagoAnterior,
        COD_ENTIDAD_FARMACIA,
        (int) $venta['cod_cliente']
    );

    mysqli_commit($enlaceCon);

    responder(true, 'El tipo de pago y su comprobante fueron actualizados correctamente.', array(
        'codigo_salida'       => (int) $codigoSalida,
        'tipo_pago_anterior'  => $tipoPagoAnterior,
        'tipo_pago_nuevo'     => (int) $codigoTipoPago,
        'tipo_venta'          => TIPO_VENTA_FARMACIA,
        'codigo_comprobante'  => isset($respuestaFinanciero['cod_comprobante'])
            ? (int) $respuestaFinanciero['cod_comprobante']
            : 0
    ));
} catch (Throwable $e) {
    mysqli_rollback($enlaceCon);
    responder(false, $e->getMessage(), array(), 500);
}
