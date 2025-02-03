<?php
require("../conexionmysqli.inc");
require("../funciones.php");

$sql = "SELECT cod_tipopago, nombre_tipopago 
        FROM tipos_pago 
        WHERE cod_tipopago not in (4) 
        ORDER BY 1";

$resp = mysqli_query($enlaceCon, $sql);

$lista = array();
while ($row = mysqli_fetch_assoc($resp)) {
    $lista[] = $row; // Agrega directamente la fila al array $lista
}

$response = array();

if ($resp) {
    $response['success'] = true;
    $response['message'] = "Se obtuvo la lista de tipos de pago correctamente";
    $response['lista'] = $lista;
} else {
    $response['success'] = false;
    $response['message'] = "Error al obtener la lista de tipos de pago";
}

// Limpia el buffer de salida
ob_clean();

// Devuelve la respuesta en formato JSON
echo json_encode($response);

?>