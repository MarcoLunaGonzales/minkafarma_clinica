<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";

$queryMaestro = "SELECT p.cod_proveedor, p.nombre_proveedor, p.estado from proveedores p";
$resultMaestro = mysqli_query($enlaceCon, $queryMaestro);

// Comprobar si la consulta fue exitosa
if (!$resultMaestro) {
    die("Error en la consulta maestro: " . mysqli_error($enlaceCon));
}

// Crear el array para almacenar los datos completos
$data = [];

while ($respMaestro = mysqli_fetch_assoc($resultMaestro)) {
    $codProveedor = $respMaestro['cod_proveedor'];
    $nombreProveedor = $respMaestro['nombre_proveedor'];
    $estadoProveedor = $respMaestro['estado'];

    // Agregar el ingreso (maestro + detalles) al array de datos
    $data[] = $respMaestro;
}

// Liberar los resultados de la consulta maestro
mysqli_free_result($resultMaestro);

// Convertir los datos a JSON
$jsonData = $data;

$estado="OK";
$resultado=array(
                "estado"=>$estado,
                "mensaje"=>"Lista Obtenida Correctamente", 
                "lista"=>$jsonData     
                );

header('Content-type: application/json');
echo json_encode($resultado);



?>
