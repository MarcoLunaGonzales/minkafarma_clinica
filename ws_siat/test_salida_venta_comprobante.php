<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";
require("../funciones.php");

/***************************************************************************/
$cod_salida_almacen = 100;
$queryMaestro = "SELECT sa.cod_salida_almacenes as cod_salida_almacen,
                        sa.cod_cliente as cod_paciente,
                        sa.nro_correlativo as nro_factura_siat,
                        '34' as cod_area,
                        MONTH(sa.fecha) as mes,
                        YEAR(sa.fecha) as gestion,
                        1 as cod_entidad,
                        1 as cod_unidad,
                        sa.cod_tipopago as cod_tipo_pago,
                        sa.razon_social
                FROM salida_almacenes sa
                WHERE sa.cod_salida_almacenes = '$cod_salida_almacen'";
// Ejecutar la consulta del maestro
$resultMaestro = mysqli_query($enlaceCon, $queryMaestro);
// Crear el array para almacenar los datos completos
$data = [];

while ($salida = mysqli_fetch_assoc($resultMaestro)) {
    $codSalidaAlmacen = $salida['cod_salida_almacen'];

    $queryDetalle = "SELECT '34' as cod_area,
                            '656' as cod_servicio,
                            1 as cantidad,
                            SUM(sd.cantidad_unitaria * sd.precio_unitario) as precio,
                            (sd.descuento_unitario) as descuento
                    FROM salida_detalle_almacenes sd
                    LEFT JOIN material_apoyo m ON m.codigo_material = sd.cod_material
                    WHERE sd.cod_salida_almacen = '$codSalidaAlmacen'";

    // Ejecutar la consulta del detalle
    $resultDetalle = mysqli_query($enlaceCon, $queryDetalle);

    // Crear un array para almacenar los detalles
    $detalles = [];

    while ($detalle = mysqli_fetch_assoc($resultDetalle)) {
        $detalles[] = $detalle; // Añadir cada detalle al array
    }

    // Agregar los detalles al maestro
    $salida['detalles'] = $detalles;

    // Agregar el salida (maestro + detalles) al array de datos
    $data[] = $salida;

    // Liberar los resultados de la consulta detalle
    mysqli_free_result($resultDetalle);
}

// Liberar los resultados de la consulta maestro
mysqli_free_result($resultMaestro);
echo "<pre>";
print_r($data);
echo "</pre>";
// Genera el comprobante de salida
$url_financiero = obtenerValorConfiguracion($enlaceCon, '-5');
$json_url = $url_financiero . '/factura/backend_comprobante_new.php';

$ch = curl_init($json_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data[0]));
$response = curl_exec($ch);
curl_close($ch);
/***************************************************************************/
?>
