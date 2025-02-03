<?php

require_once 'config.php';

set_time_limit(0);
error_reporting(E_ALL); // Habilitar la visualización de errores para facilitar la depuración

$enlaceCon = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

// Manejo de errores en la conexión
if (!$enlaceCon) {
    die("Error en la conexión: " . mysqli_connect_error());
}

mysqli_set_charset($enlaceCon, "utf8");

// Función para obtener resultados de consultas
if (!function_exists('mysqli_result')) {
    function mysqli_result($result, $number, $field = 0) {
        if ($result && mysqli_num_rows($result) > $number) {
            mysqli_data_seek($result, $number);
            $row = mysqli_fetch_assoc($result);
            if (isset($row[$field])) {
                return $row[$field];
            }
        }
        return null;
    }
}

// Opcional: cerrar la conexión después de su uso
// mysqli_close($enlaceCon);

?>
