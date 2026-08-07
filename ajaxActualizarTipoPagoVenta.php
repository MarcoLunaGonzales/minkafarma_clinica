<?php

ob_start();

require("conexionmysqli.php");

header('Content-Type: application/json; charset=utf-8');

function responder($ok, $mensaje)
{
    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(array(
        'ok' => $ok,
        'mensaje' => $mensaje
    ));

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método de solicitud no permitido.');
}

$codigoSalida = isset($_POST['codigo_salida'])
    ? (int) $_POST['codigo_salida']
    : 0;

$codigoTipoPago = isset($_POST['codigo_tipo_pago'])
    ? (int) $_POST['codigo_tipo_pago']
    : 0;

if ($codigoSalida <= 0) {
    responder(false, 'El código de la venta no es válido.');
}

if ($codigoTipoPago <= 0) {
    responder(false, 'El tipo de pago seleccionado no es válido.');
}

/*
 * Verificar que el tipo de pago exista.
 */
$sqlTipoPago = "SELECT cod_tipopago
                FROM tipos_pago
                WHERE cod_tipopago = ?
                LIMIT 1";

$stmtTipoPago = mysqli_prepare($enlaceCon, $sqlTipoPago);

if (!$stmtTipoPago) {
    responder(false, 'No se pudo validar el tipo de pago.');
}

mysqli_stmt_bind_param(
    $stmtTipoPago,
    "i",
    $codigoTipoPago
);

mysqli_stmt_execute($stmtTipoPago);
mysqli_stmt_store_result($stmtTipoPago);

if (mysqli_stmt_num_rows($stmtTipoPago) == 0) {
    mysqli_stmt_close($stmtTipoPago);

    responder(
        false,
        'El tipo de pago seleccionado no existe.'
    );
}

mysqli_stmt_close($stmtTipoPago);

/*
 * Verificar que la venta exista.
 */
$sqlVenta = "SELECT cod_salida_almacenes
             FROM salida_almacenes
             WHERE cod_salida_almacenes = ?
             LIMIT 1";

$stmtVenta = mysqli_prepare($enlaceCon, $sqlVenta);

if (!$stmtVenta) {
    responder(false, 'No se pudo validar la venta.');
}

mysqli_stmt_bind_param(
    $stmtVenta,
    "i",
    $codigoSalida
);

mysqli_stmt_execute($stmtVenta);
mysqli_stmt_store_result($stmtVenta);

if (mysqli_stmt_num_rows($stmtVenta) == 0) {
    mysqli_stmt_close($stmtVenta);

    responder(
        false,
        'No se encontró la venta seleccionada.'
    );
}

mysqli_stmt_close($stmtVenta);

/*
 * Actualizar el tipo de pago.
 */
$sqlActualizar = "UPDATE salida_almacenes
                  SET cod_tipopago = ?
                  WHERE cod_salida_almacenes = ?";

$stmtActualizar = mysqli_prepare(
    $enlaceCon,
    $sqlActualizar
);

if (!$stmtActualizar) {
    responder(
        false,
        'No se pudo preparar la actualización.'
    );
}

mysqli_stmt_bind_param(
    $stmtActualizar,
    "ii",
    $codigoTipoPago,
    $codigoSalida
);

if (!mysqli_stmt_execute($stmtActualizar)) {
    mysqli_stmt_close($stmtActualizar);

    responder(
        false,
        'No se pudo actualizar el tipo de pago.'
    );
}

mysqli_stmt_close($stmtActualizar);

responder(
    true,
    'El tipo de pago fue actualizado correctamente.'
);