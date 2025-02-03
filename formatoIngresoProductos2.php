<?php
require("conexionmysqlipdf.inc");
require("fpdf186/fpdf.php");
require("funciones.php");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'NOTA DE INGRESO', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $fecha_hora_actual = date('d-m-Y H:i:s'); 
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '  /  ' . $fecha_hora_actual, 0, 0, 'C');
        //$this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}
// Crear instancia de FPDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

$codigoIngreso=$_GET["codigo_ingreso"];
$globalAlmacen=$_COOKIE["global_almacen"];
$codigoSucursal = $_COOKIE['global_agencia'];


// Obtener datos principales
$sql = "SELECT i.cod_ingreso_almacen, i.fecha, ti.nombre_tipoingreso, i.observaciones, i.nro_correlativo, i.descuento_adicional, i.descuento_adicional2, p.nombre_proveedor, i.nro_factura_proveedor, p.direccion, p.telefono1, tp.nombre_tipopago, i.dias_credito, tpd.nombre as nombretipodoc
FROM ingreso_almacenes i
JOIN tipos_ingreso ti ON i.cod_tipoingreso = ti.cod_tipoingreso 
LEFT JOIN proveedores p ON i.cod_proveedor = p.cod_proveedor 
LEFT JOIN tipos_pago tp ON tp.cod_tipopago = i.cod_tipopago
LEFT JOIN tipos_docs tpd ON tpd.codigo=i.cod_tipo_doc
WHERE i.cod_almacen='$globalAlmacen' AND i.cod_ingreso_almacen='$codigoIngreso'";

$resp = mysqli_query($enlaceCon, $sql);
$dat = mysqli_fetch_array($resp);

$codigo = $dat[0];
$fecha_ingreso = $dat[1];
$fecha_ingreso_mostrar = substr($fecha_ingreso, 8, 2) . '-' . substr($fecha_ingreso, 5, 2) . '-' . substr($fecha_ingreso, 0, 4);
$nombre_tipoingreso = $dat[2];
$obs_ingreso = $dat[3];
$nro_correlativo = $dat[4];
$descuentoCab1 = $dat[5];
$descuentoCab2 = $dat[6];
$nombre_proveedor = $dat[7];
$nro_factura_proveedor = $dat[8];
$direccion_proveedor=$dat[9];
$telefono_proveedor=$dat[10];
$tipoPago = $dat[11];
$diasCredito = $dat[12];
$nombreTipoDocumento = $dat[13];

// Datos principales
$imgLogo=obtenerValorConfiguracion($enlaceCon,13);
$pdf->Image('imagenes/'.$imgLogo, 10, 12, 40, 15);

$pdf->SetFont('Arial', '', 9);

$x=130; $y=15;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Nro.: ',0,"L", 0, 0);
$pdf->Cell(40, 5, $nro_correlativo, 0, 1);

$x=130; $y=20;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Tipo Doc.: ',0,"L", 0, 0);
$pdf->Cell(50, 5, $nombreTipoDocumento, 0, 1);

$x=130; $y=25;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Nro. Doc: ',0,"L", 0, 0);
$pdf->Cell(50, 5, $nro_factura_proveedor, 0, 1);

$x=15; $y=30;
$pdf->SetXY($x,$y);
$pdf->Cell(20, 5, 'Fecha:',0, 0, 0);
$pdf->Cell(20, 5, $fecha_ingreso_mostrar,0, 0, 1);

$x=15; $y=35;
$pdf->SetXY($x,$y);
$pdf->Cell(20, 5, 'Proveedor:',0,0,0);
$pdf->Cell(40, 5, $nombre_proveedor,0,0,1);

$x=60; $y=30;
$pdf->SetXY($x,$y);
$pdf->Cell(20, 5, 'Direccion:',0,0,0);
$x=80; $y=30; $pdf->SetXY($x,$y);
$pdf->Cell(80, 5, $direccion_proveedor,0,0,1);

$x=90; $y=35;
$pdf->SetXY($x,$y);
$pdf->Cell(20, 5, 'Telefono:',0, 0, 0);
$x=110; $y=35;
$pdf->SetXY($x,$y);
$pdf->Cell(50, 5, $telefono_proveedor,0,0,1);

$x=15; $y=40;
$pdf->SetXY($x,$y);
$pdf->Cell(20, 5, 'Tipo de Ingreso:',0,0,0);
$pdf->Cell(40, 5, $nombre_tipoingreso,0,0,1);


// $pdf->Cell(40, 10, 'Observaciones: ', 0, 0);
// $pdf->MultiCell(0, 10, $obs_ingreso);

// Encabezado tabla de detalles
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(7, 8, '#', 1);
$pdf->Cell(12, 8, 'Codigo', 1, 0, "C");
$pdf->Cell(108, 8, 'Producto', 1, 0, "C");
$pdf->Cell(17, 8, 'Venc.', 1, 0, "C");
$pdf->Cell(15, 8, 'Cant.', 1, 0, "C");
$pdf->Cell(15, 8, 'Costo', 1, 0, "C");
$pdf->Cell(17, 8, 'Total', 1, 0, "C");
$pdf->Ln();

// Detalles del ingreso
$pdf->SetFont('Arial', '', 8);
$sql_detalle = "SELECT i.cod_material, i.cantidad_unitaria, i.precio_neto, i.lote, DATE_FORMAT(i.fecha_vencimiento, '%d/%m/%Y'),
i.descuento_unitario, i.precio_bruto, m.cantidad_presentacion, cantidad_bonificacion
FROM ingreso_detalle_almacenes i
JOIN material_apoyo m ON i.cod_material = m.codigo_material
WHERE i.cod_ingreso_almacen='$codigo'";

$resp_detalle = mysqli_query($enlaceCon, $sql_detalle);
$indice = 1;
$totalIngreso = 0;

$totalCostoProducto=0;

while ($dat_detalle = mysqli_fetch_array($resp_detalle)) {
    $cod_material = $dat_detalle[0];
    $cantidad_unitaria = $dat_detalle[1];
    $cantidad_unitariaF=formatonumero($cantidad_unitaria);
    $precio_neto = $dat_detalle[2];
    $precio_netoF = formatonumeroDec($precio_neto);

    $lote_producto = $dat_detalle[3];
    $fecha_venc = $dat_detalle[4];
    $nombre_material = mysqli_fetch_array(mysqli_query($enlaceCon, "SELECT descripcion_material FROM material_apoyo WHERE codigo_material='$cod_material'"))[0];

    $descuento_unitario=$dat_detalle[5];

    $subtotal = $cantidad_unitaria * $precio_neto;

    $descuento_numerico   = $subtotal * ($descuento_unitario/100);
    $descuento_numericoF = formatonumeroDec($descuento_numerico);

    $subtotal = $subtotal-$descuento_numerico;

    $totalIngreso += $subtotal;

    $nombre_material=mb_strimwidth($nombre_material, 0, 52, "...");

    $costoProducto = costoVentaFecha($enlaceCon, $cod_material, $codigoSucursal, $fecha_ingreso);

    /*LE APLICAMOS EL 0.87 POR COMPRAS CON FACTURA*/
    $costoProducto = $costoProducto * 0.87;


    $costoProductoFila = $cantidad_unitaria * $costoProducto;    
    $totalCostoProducto+=($cantidad_unitaria * $costoProducto);


    $pdf->Cell(7, 7, $indice++, 1);
    $pdf->Cell(12, 7, $cod_material, 1);
    $pdf->Cell(108, 7, $nombre_material, 1);
    $pdf->Cell(17, 7, $fecha_venc, 1);
    $pdf->Cell(15, 7, $cantidad_unitariaF,1,0,"R");
    $pdf->Cell(15, 7, number_format($costoProducto, 2),1,0,"R");
    $pdf->Cell(17, 7, number_format($costoProductoFila, 2),1,0,"R");
    $pdf->Ln();
}

// Totales

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(174, 6, 'Total Costo(Bs.): ', 1, 0, 'R');
$pdf->Cell(17, 6, number_format($totalCostoProducto, 2), 1, 1, 'R');

/*
$pdf->Cell(170, 6, 'Descuento 1 (Bs.): ', 1, 0, 'R');
$pdf->Cell(20, 6, number_format($descuentoCab1, 2), 1, 1, 'R');

$pdf->Cell(170, 6, 'Descuento 2 (Bs.): ', 1, 0, 'R');
$pdf->Cell(20, 6, number_format($descuentoCab2, 2), 1, 1, 'R');

$pdf->Cell(170, 6, 'Total Final (Bs.): ', 1, 0, 'R');
$pdf->Cell(20, 6, number_format($totalIngreso - $descuentoCab1 - $descuentoCab2, 2), 1, 1, 'R');
*/

$y_actual = $pdf->GetY();
$espacio_entre_firmas = 15; // Espacio entre las firmas y el contenido final

// Establecer la posición para las firmas
$pdf->SetY($y_actual + $espacio_entre_firmas); 
$pdf->SetFont('Arial', '', 10);

// Líneas para las firmas
$pdf->Cell(95, 10, '_____________________________', 0, 0, 'C');
$pdf->Cell(95, 10, '_____________________________', 0, 1, 'C');

$y_actual = $pdf->GetY();
$pdf->SetY($y_actual-6); 

// Firma del Comprador
$pdf->Cell(95, 10, 'Comprador', 0, 0, 'C'); // Mitad izquierda
$pdf->Cell(95, 10, 'Proveedor', 0, 1, 'C'); // Mitad derecha


// Salida del archivo
$pdf->Output();
?>
