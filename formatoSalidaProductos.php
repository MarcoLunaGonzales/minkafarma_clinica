<?php
require("conexionmysqlipdf.inc");
require("fpdf186/fpdf.php");
require("funciones.php");
require("funcion_nombres.php");

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'NOTA DE SALIDA', 0, 1, 'C');
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
// Crear una instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Variables principales
$codigo_salida = $_GET['codigo_salida'];
$codigoSucursal = $_COOKIE['global_agencia'];
$globalAlmacen = $_COOKIE['global_almacen'];

// Obtener datos de la empresa
$sqlEmpresa = "SELECT nombre, nit, direccion FROM datos_empresa";
$respEmpresa = mysqli_query($enlaceCon, $sqlEmpresa);
$datEmpresa = mysqli_fetch_array($respEmpresa);
$nombreEmpresa = $datEmpresa[0];
$nitEmpresa = $datEmpresa[1];
$direccionEmpresa = $datEmpresa[2];

// Obtener datos de la salida
$sql = "SELECT s.cod_salida_almacenes, s.fecha, ts.nombre_tiposalida, s.observaciones,
    s.nro_correlativo, s.territorio_destino, s.almacen_destino, 
    (SELECT c.nombre_cliente FROM clientes c WHERE c.cod_cliente = s.cod_cliente),
    (SELECT c.dir_cliente FROM clientes c WHERE c.cod_cliente = s.cod_cliente),
    s.monto_total, s.descuento, s.monto_final, s.cod_almacen
    FROM salida_almacenes s, tipos_salida ts
    WHERE s.cod_tiposalida = ts.cod_tiposalida AND s.cod_almacen = '$globalAlmacen' 
    AND s.cod_salida_almacenes = '$codigo_salida'";
$resp = mysqli_query($enlaceCon, $sql);
$dat = mysqli_fetch_array($resp);

// Asignar datos
$codigo = $dat[0];
$fecha_salida = $dat[1];
$fecha_salida_mostrar = date('d-m-Y', strtotime($fecha_salida));
$nombre_tiposalida = $dat[2];
$obs_salida = $dat[3];
$nro_correlativo = $dat[4];
$territorio_destino=$dat[5];
$almacen_destino=$dat[6];

$nombreCliente = $dat[7];
$direccionCliente = $dat[8];
$montoFinal = redondear2($dat[11]);

$codAlmacenOrigen=$dat[12];

$nombreAlmacenOrigen=nombreAlmacen($enlaceCon, $codAlmacenOrigen);
$nombreAlmacenDestino=nombreAlmacen($enlaceCon, $almacen_destino);


$imgLogo=obtenerValorConfiguracion($enlaceCon,13);
$pdf->Image('imagenes/'.$imgLogo, 10, 12, 50, 15);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 20, "NOTA DE SALIDA: $nro_correlativo", 0, 1, 'C');
$pdf->Ln(5);

// Información general
$pdf->SetFont('Arial', '', 10);

$x=10; $y=30;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Fecha: ',0,0,"L",0);
$pdf->Cell(40, 5, $fecha_salida_mostrar, 0, 1);

$x=90; $y=30;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Tipo de Salida: ',0, 0, "L",0);
$pdf->Cell(20, 5, $nombre_tiposalida, 0, 1);

$x=10; $y=35;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Almacen Origen:',0,0,"L",0);
$pdf->Cell(40, 5, $nombreAlmacenOrigen,0,0,1);

$x=90; $y=35;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Almacen Destino:',0,0,"L",0);
$pdf->Cell(40, 5, $nombreAlmacenDestino,0,0,1);

$x=10; $y=40;
$pdf->SetXY($x,$y);
$pdf->Cell(40, 5, 'Observaciones:',0, 0,"L", 0);
$pdf->Cell(20, 5, $obs_salida,0, 0, 1);

 $pdf->Ln(10);

// $pdf->Cell(40, 7, "Fecha:", 1, 0);
// $pdf->Cell(50, 7, "$fecha_salida_mostrar", 0, 0);
// $pdf->Cell(60, 7, "Tipo de Salida: $nombre_tiposalida", 0, 1);
// $pdf->Cell(0, 7, "Observaciones: ", 0, 1);
// $pdf->Cell(50, 7, "$obs_salida", 0, 1);
// $pdf->Ln(5);

// Encabezado de la tabla
$pdf->SetFillColor(200, 220, 255);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(10, 7, 'Codigo', 1, 0, 'C', true);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135, 7, 'Producto', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Costo', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Total', 1, 1, 'C', true);

// Detalle de productos
$sql_detalle = "SELECT s.cod_material, m.descripcion_material, s.lote, s.fecha_vencimiento, 
    s.cantidad_unitaria, s.precio_unitario, s.`descuento_unitario`, s.`monto_unitario`, m.codigo_anterior
    FROM salida_detalle_almacenes s, material_apoyo m
    WHERE s.cod_salida_almacen='$codigo' AND s.cod_material=m.codigo_material";
$resp_detalle = mysqli_query($enlaceCon, $sql_detalle);

$pdf->SetFont('Arial', '', 10);

$totalCostoProducto=0;
while ($dat_detalle = mysqli_fetch_array($resp_detalle)) {
    $cod_material = $dat_detalle[0];
    $codigoInterno = $dat_detalle[8];
    $nombre_material = $dat_detalle[1];
    $cantidad_unitaria = $dat_detalle[4];
    $cantidad_unitariaF = formatonumero($dat_detalle[4]);

    $costoProducto = costoVentaFecha($enlaceCon, $cod_material, $codigoSucursal, $fecha_salida);

    /*LE APLICAMOS EL 0.87 POR COMPRAS CON FACTURA*/
    $costoProducto = $costoProducto * 0.87;
    

    $costoProductoFila = $cantidad_unitaria * $costoProducto;
    
    $totalCostoProducto+=($cantidad_unitaria * $costoProducto);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(10, 7, $cod_material, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(135, 7, $nombre_material, 1);
    $pdf->Cell(15, 7, $cantidad_unitariaF, 1, 0, 'R');
    $pdf->Cell(15, 7, number_format($costoProducto, 2), 1, 0, 'R');
    $pdf->Cell(15, 7, number_format($costoProductoFila, 2), 1, 1, 'R');
}

// Total
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(175, 7, 'Total:', 1, 0, 'R');
$pdf->Cell(15, 7, number_format($totalCostoProducto, 2), 1, 1, 'R');

// Salida del archivo PDF
$pdf->Output();
?>
