<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";

// EJEMPLO URL
// http://localhost/minkafarma/ws_siat/ws_ingresosproductos.php?gestion=2024&mes=9&filtro_operador=2&filtro_valor=500

// Parametros
$mes     = $_GET['mes'] ?? '';
$gestion = $_GET['gestion'] ?? '';
$filtro_operador = $_GET['filtro_operador'] ?? '';  // 1:'>=' o 2:'<='
$filtro_valor    = $_GET['filtro_valor'] ?? '';     // Valor numérico para el filtro
$contabilizado   = $_GET['contabilizado'] ?? '';   // 0:Sin Contabilizar, 1:Contabilizado
// Convertir el valor del operador a `>=` o `<=`
if ($filtro_operador == '1') {
    $operador = '>='; // Mayor o igual
} elseif ($filtro_operador == '2') {
    $operador = '<='; // Menor o igual
} elseif ($filtro_operador == '3') {
    $operador = '>';  // Mayor
} else {
    $operador = ''; // Sin operador si no se define un valor válido
}

// Consulta maestro (ingresos de almacenes)
// $queryMaestro = "
//     SELECT i.cod_ingreso_almacen, i.nro_factura_proveedor, i.fecha, p.nombre_proveedor, 
//            i.descuento_adicional, i.descuento_adicional2, i.fecha_factura_proveedor
//     FROM ingreso_almacenes i
//     LEFT JOIN proveedores p ON p.cod_proveedor = i.cod_proveedor
//     WHERE i.fecha BETWEEN '2024-09-01' AND '2024-09-30' 
//     AND i.ingreso_anulado = 0 
//     AND i.contabilizado = 0 
//     AND i.cod_tipoingreso = 1000
//     AND i.cod_ingreso_almacen in (2,3,15,19);
// ";

$queryMaestro = "SELECT i.nro_correlativo, 
							i.cod_ingreso_almacen, 
							i.nro_factura_proveedor, 
							i.fecha, 
							p.nombre_proveedor, 
							i.descuento_adicional, 
							i.descuento_adicional2, 
							i.fecha_factura_proveedor, 
                            CAST(
                                ROUND(
                                    (SELECT SUM((cantidad_unitaria * precio_bruto) - 
                                    ROUND((cantidad_unitaria * precio_bruto) * (descuento_unitario/100), 2))
                                    FROM ingreso_detalle_almacenes
                                    WHERE cod_ingreso_almacen = i.cod_ingreso_almacen) 
                                    - i.descuento_adicional 
                                    - i.descuento_adicional2, 
                                2) AS DECIMAL(10, 2)
                            ) AS monto_total, 
							((SELECT ROUND(SUM((cantidad_unitaria * precio_bruto)), 2)
							FROM ingreso_detalle_almacenes
							WHERE cod_ingreso_almacen = i.cod_ingreso_almacen)) as monto_sin_descuento,
							i.dias_credito, 
							i.cod_proveedor,
							p.razon_social, 
							p.nit,
                            i.contabilizado
		FROM ingreso_almacenes i
		LEFT JOIN proveedores p ON p.cod_proveedor = i.cod_proveedor
		WHERE i.ingreso_anulado = 0 
        AND i.cod_tipoingreso = 1000";

if (!empty($contabilizado)) {
    $queryMaestro .= " AND i.contabilizado = '$contabilizado'";
}

if (!empty($gestion)) {
    $queryMaestro .= " AND YEAR(i.fecha) = '$gestion'";
}
if (!empty($mes)) {
    $queryMaestro .= " AND MONTH(i.fecha) = '$mes'";
}

// Agregar filtro de monto si ambos parámetros están definidos y válidos
if (!empty($operador) && !empty($filtro_valor)) {
    $queryMaestro .= " AND CAST(
                            ROUND(
                                (SELECT SUM((cantidad_unitaria * precio_bruto) - 
                                (cantidad_unitaria * precio_bruto) * (descuento_unitario/100))
                                FROM ingreso_detalle_almacenes
                                WHERE cod_ingreso_almacen = i.cod_ingreso_almacen) 
                                - i.descuento_adicional 
                                - i.descuento_adicional2, 
                            2) AS DECIMAL(10, 2)
                        ) $operador $filtro_valor";
}

// echo $queryMaestro;
// exit;

// Ejecutar la consulta del maestro
$resultMaestro = mysqli_query($enlaceCon, $queryMaestro);

// Comprobar si la consulta fue exitosa
if (!$resultMaestro) {
    die("Error en la consulta maestro: " . mysqli_error($enlaceCon));
}

// Crear el array para almacenar los datos completos
$data = [];

while ($ingreso = mysqli_fetch_assoc($resultMaestro)) {
    $codIngresoAlmacen = $ingreso['cod_ingreso_almacen'];

    // Consulta detalle (materiales por ingreso)
    $queryDetalle = "SELECT id.cod_material, m.codigo_anterior, m.descripcion_material, id.cantidad_unitaria, 
               id.descuento_unitario, id.precio_bruto
        FROM ingreso_detalle_almacenes id
        LEFT JOIN material_apoyo m ON m.codigo_material = id.cod_material
        WHERE id.cod_ingreso_almacen = '$codIngresoAlmacen'
        ORDER BY id.orden ASC;
    ";

    // Ejecutar la consulta del detalle
    $resultDetalle = mysqli_query($enlaceCon, $queryDetalle);

    // Comprobar si la consulta fue exitosa
    if (!$resultDetalle) {
        die("Error en la consulta detalle: " . mysqli_error($enlaceCon));
    }

    // Crear un array para almacenar los detalles
    $detalles = [];

    while ($detalle = mysqli_fetch_assoc($resultDetalle)) {
        $detalles[] = $detalle; // Añadir cada detalle al array
    }

    // Agregar los detalles al maestro
    $ingreso['detalles'] = $detalles;

    // Agregar el ingreso (maestro + detalles) al array de datos
    $data[] = $ingreso;

    // Liberar los resultados de la consulta detalle
    mysqli_free_result($resultDetalle);
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
