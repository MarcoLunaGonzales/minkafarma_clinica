<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";

$cod_salida_almacen = $_POST['cod_salida_almacen'] ?? '';
$cod_comprobante    = $_POST['cod_comprobante'] ?? '';

$queryUpdate = "UPDATE salida_almacenes SET cod_comprobante = '$cod_comprobante' WHERE cod_salida_almacenes = '$cod_salida_almacen'";
$resultUpdate = mysqli_query($enlaceCon, $queryUpdate);

// Verificar si la actualización fue exitosa
if ($resultUpdate) {
    echo json_encode(['status' => 'success', 'message' => 'Estado de contabilizado actualizado correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el estado de contabilizado']);
}

?>
