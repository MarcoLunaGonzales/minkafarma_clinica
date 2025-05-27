<?php
require("conexionmysqli.php");
require("funciones.php");
header('Content-Type: application/json');
ob_clean();
$codigo = $_GET['codigo'] ?? '';
if (empty($codigo)) {
    echo json_encode(['status' => false, 'message' => 'Falta el parámetro codigo']);
    exit;
}
$url_financiero = obtenerValorConfiguracion($enlaceCon, '-5');
$json_url = $url_financiero . '/comprobantes/backend_comprobante_farmacia_anular.php?codigo=' . urlencode($codigo);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $json_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);
$fallo = false;
$errorMsg = '';
if ($curlError) {
    $fallo = true;
    $errorMsg = 'Error CURL: ' . $curlError;
} elseif ($httpCode !== 200) {
    $fallo = true;
    $errorMsg = 'Error HTTP: ' . $httpCode . ' - Respuesta: ' . $response;
} else {
    $responseData = json_decode($response, true);

    if (!$responseData) {
        $fallo = true;
        $errorMsg = 'Respuesta no es JSON válido: ' . $response;
    } elseif (!isset($responseData['status'])) {
        $fallo = true;
        $errorMsg = 'Respuesta inesperada del backend financiero: ' . $response;
    } elseif ($responseData['status'] !== true) {
        $fallo = true;
        $errorMsg = 'Backend financiero respondió fallo: ' . ($responseData['message'] ?? 'Sin mensaje detallado');
    }
}
if ($fallo) {
    echo json_encode([
        'status'  => false,
        'message' => 'Fallo al anular en backend financiero.'.$errorMsg
    ]);
    exit;
}

$sqlUpdateIngreso = "UPDATE ingreso_almacenes SET ingreso_anulado = 1 WHERE cod_ingreso_almacen = '$codigo'";
$resp = mysqli_query($enlaceCon, $sqlUpdateIngreso);

ob_clean();
if ($resp) {
    echo json_encode([
        'status' => true,
        'message' => 'Comprobante anulado correctamente y registro actualizado.'
    ]);
} else {
    echo json_encode([
        'status'   => false,
        'message'  => 'Comprobante anulado en backend financiero, pero error al actualizar ingreso_almacenes.',
        'db_error' => mysqli_error($enlaceCon)
    ]);
}
exit;
?>
