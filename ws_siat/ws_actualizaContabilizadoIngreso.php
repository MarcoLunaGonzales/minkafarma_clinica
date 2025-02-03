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
mysqli_begin_transaction($enlaceCon);

try {
    $orden = 1;
    foreach ($codigosArray as $codigo) {
        $queryUpdate = "UPDATE ingreso_almacenes 
                        SET contabilizado = 1, 
                            cod_comprobante = '$cod_comprobante', 
                            orden_contabilizado = '$orden '
                        WHERE cod_ingreso_almacen = $codigo";
        $resultUpdate = mysqli_query($enlaceCon, $queryUpdate);

        if (!$resultUpdate) {
            throw new Exception("Error al actualizar el código: $codigo");
        }

        $orden++;
    }
    mysqli_commit($enlaceCon);

    echo json_encode(['status' => 'success', 'message' => 'Estado de contabilizado y orden actualizado correctamente']);
} catch (Exception $e) {
    mysqli_rollback($enlaceCon);

    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>
