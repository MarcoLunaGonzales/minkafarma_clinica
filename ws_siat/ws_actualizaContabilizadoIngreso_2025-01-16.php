<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";

$codigosIngresos = $_POST['codigos_ingresos'] ?? '';
$cod_comprobante = $_POST['cod_comprobante'] ?? '';

if (empty($codigosIngresos)) {
    echo json_encode(['status' => 'error', 'message' => 'No se enviaron códigos de ingresos']);
    exit;
}

$codigosArray = array_filter(array_map('intval', explode(',', $codigosIngresos)));

if (empty($codigosArray)) {
    echo json_encode(['status' => 'error', 'message' => 'Códigos de ingresos no válidos']);
    exit;
}

$codigosList = implode(',', $codigosArray);

$queryUpdate = "UPDATE ingreso_almacenes SET contabilizado = 1, cod_comprobante = '$cod_comprobante' WHERE cod_ingreso_almacen IN ($codigosList)";
$resultUpdate = mysqli_query($enlaceCon, $queryUpdate);

// Verificar si la actualización fue exitosa
if ($resultUpdate) {
    echo json_encode(['status' => 'success', 'message' => 'Estado de contabilizado actualizado correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el estado de contabilizado']);
}

?>
