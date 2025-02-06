<?php
require("conexionmysqli.php");
header('Content-Type: application/json');
ob_clean();
$cod_funcionario_caja = $_COOKIE['global_usuario'];
try {
    if (!isset($_POST['fecha_inicio']) || !isset($_POST['fecha_fin'])) {
        echo json_encode(["status" => false, "message" => "Datos incompletos"]);
        exit;
    }

    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $sql = "UPDATE salida_almacenes 
            SET cod_funcionario_caja = '$cod_funcionario_caja' 
            WHERE CONCAT(fecha,' ',hora_salida) BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    $resp = mysqli_query($enlaceCon, $sql);

    if ($resp) {
        echo json_encode(["status" => true]);
    } else {
        echo json_encode(["status" => false, "message" => "Error al actualizar"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => false, "message" => "Error en el servidor"]);
}
?>
