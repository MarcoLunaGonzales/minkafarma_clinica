<?php
require("conexionmysqli.php");

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $cod_salida_almacenes = $_POST['cod_salida_almacenes'];
    //$nro_receta           = $_POST['nro_receta'];
    $nombre_paciente      = $_POST['nombre_paciente'];
    $nombre_medico        = $_POST['nombre_medico'];

    // Validar datos
    if (empty($nombre_paciente) || empty($nombre_medico)) {
        $response = ['success' => false, 'message' => 'Todos los campos son obligatorios.'];
    } else {
        try {
            // Construir consulta SQL
            $sql = "UPDATE salida_almacenes 
                    SET nombre_paciente = '$nombre_paciente', 
                        nombre_medico = '$nombre_medico' 
                    WHERE cod_salida_almacenes = '$cod_salida_almacenes'";
            
            // Ejecutar consulta
            $respUpdMonto = mysqli_query($enlaceCon, $sql);
            
            // Verificar si la consulta se ejecutó correctamente
            if ($respUpdMonto) {
                $response = ['success' => true, 'message' => 'Datos actualizados correctamente.'];
            } else {
                $response = ['success' => false, 'message' => 'Error al actualizar los datos: ' . mysqli_error($enlaceCon)];
            }
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
} else {
    $response = ['success' => false, 'message' => 'Método de solicitud no válido.'];
}

// Limpiar buffer y enviar respuesta JSON
ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
?>
