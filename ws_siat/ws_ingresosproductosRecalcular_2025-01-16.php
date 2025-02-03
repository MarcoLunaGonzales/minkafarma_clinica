<?php

require "funciones.php";
require_once "../conexionmysqlipdf.inc";

// EJEMPLO URL
// http://localhost/minkafarma/ws_siat/ws_ingresosproductosRecalcular.php?cod_comprobante=30

// Parametros
$cod_comprobante = $_GET['cod_comprobante'] ?? '';

$queryMaestro = "SELECT i.nro_correlativo, 
							i.cod_ingreso_almacen, 
							i.nro_factura_proveedor, 
							i.fecha, 
							p.nombre_proveedor, 
							i.descuento_adicional, 
							i.descuento_adicional2, 
							i.fecha_factura_proveedor, 
							((SELECT ROUND(SUM((cantidad_unitaria * precio_bruto) - 
                                ROUND((cantidad_unitaria * precio_bruto) * (descuento_unitario/100), 2)), 2)
							FROM ingreso_detalle_almacenes
							WHERE cod_ingreso_almacen = i.cod_ingreso_almacen)-i.descuento_adicional-i.descuento_adicional2) as monto_total, 
							((SELECT ROUND(SUM((cantidad_unitaria * precio_bruto)), 2)
							FROM ingreso_detalle_almacenes
							WHERE cod_ingreso_almacen = i.cod_ingreso_almacen)) as monto_sin_descuento,
							i.dias_credito, 
							i.cod_proveedor,
							p.razon_social, 
							p.nit
		FROM ingreso_almacenes i
		LEFT JOIN proveedores p ON p.cod_proveedor = i.cod_proveedor
		WHERE i.ingreso_anulado = 0 
        AND i.cod_tipoingreso = 1000
        AND i.cod_comprobante = '$cod_comprobante'";

// Ejecutar la consulta del maestro
$resultMaestro = mysqli_query($enlaceCon, $queryMaestro);

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
        ORDER BY id.orden ASC";
    // Ejecutar la consulta del detalle
    $resultDetalle = mysqli_query($enlaceCon, $queryDetalle);

    $detalles = [];
    while ($detalle = mysqli_fetch_assoc($resultDetalle)) {
        $detalles[] = $detalle;
    }
    $ingreso['detalles'] = $detalles;
    $data[] = $ingreso;

    mysqli_free_result($resultDetalle);
}

mysqli_free_result($resultMaestro);
$jsonData = $data;

$resultado=array(
                "estado"  => true,
                "mensaje" => "Lista Obtenida Correctamente", 
                "lista"   => $jsonData     
                );

header('Content-type: application/json');
echo json_encode($resultado);



?>
