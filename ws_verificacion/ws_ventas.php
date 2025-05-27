<?php
header('Content-Type: application/json');
ini_set('memory_limit','1G');

require('../function_formatofecha.php');
require('../conexionmysqli.inc');
require('../funcion_nombres.php');

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin    = $_GET['fecha_fin'] ?? '';

$sqlConf 	= "SELECT id, valor FROM configuracion_facturas WHERE id=1";
$respConf 	= mysqli_query($enlaceCon, $sqlConf);
$nombreTxt  = mysqli_result($respConf, 0, 1);

$sqlConf  = "SELECT id, valor FROM configuracion_facturas WHERE id=9";
$respConf = mysqli_query($enlaceCon, $sqlConf);
$nitTxt   = mysqli_result($respConf, 0, 1);



$sql = "SELECT s.cod_salida_almacenes, s.nro_correlativo, s.fecha AS fecha, s.monto_final, s.razon_social, s.nit, s.siat_cuf AS nro_autorizacion, s.salida_anulada, '0' AS cod_control, 
	(SELECT c.descripcion FROM ciudades c, almacenes a WHERE a.cod_ciudad=c.cod_ciudad AND a.cod_almacen=s.cod_almacen) AS nombre_ciudad, s.cod_tipo_doc, s.siat_complemento 
	FROM salida_almacenes s 
	WHERE s.fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'  
    AND s.cod_tiposalida = 1001
    AND s.siat_estado_facturacion = 1 
    ORDER BY s.fecha DESC";

$resp = mysqli_query($enlaceCon, $sql);

$data = [];
$totalVentas = 0;
$totalImpuestos = 0;

while ($datos = mysqli_fetch_array($resp)) {
    $importe 		= (float) $datos['monto_final'];
    $montoIVA 		= $importe * 0.13;
    $totalVentas 	+= $importe;
    $totalImpuestos += $montoIVA;
    
    $nombreEstado = $datos['salida_anulada'] == 0 ? "V" : "A";
    $codTipoDoc   = $datos['cod_tipo_doc'];
    $nomTipo 	  = $codTipoDoc == 1 ? "A" : ($codTipoDoc == 4 ? "M" : "");

    $data[] = [
        'cod_salida_almacenes' => $datos['cod_salida_almacenes'],
        'fecha' 			=> $datos['fecha'],
        'nroFactura' 		=> $datos['nro_correlativo'],
        'nroAutorizacion' 	=> $datos['nro_autorizacion'],
        'nit'			 	=> $datos['nit'],
        "complementoDocumento" => $datos['siat_complemento'],
        'razonSocial' 		=> $datos['razon_social'],
        'importeTotalVenta' => $importe,
        "ice"           	=> 0,
        "iehd"          	=> 0,
        "ipj"           	=> 0,
        "tasa"          	=> 0,
        "otros"         	=> 0,
        "exportacionesExentas" => 0,
        'ventasTasaCero' 	   => 0,
        'subtotal'			   => $importe,
        'descuentos' 		   => 0,
        "giftcard"      	   => 0,
        "importeBaseDebitoFiscal" => $importe,
        'debitoFiscal' 			  => $montoIVA,
        'estado' 				  => $nombreEstado,
        'codigoControl' 		  => $datos['cod_control'],
        "tipoVenta"               => "OTROS",
		"derechoCreditoFiscal"	  => "SI",
		"estadoConsolidacion"	  => "PENDIENTE",
		"area"					  => "FARMACIA"
    ];
}

$response = [
    'nombreRazonSocial' => $nombreTxt,
    'nit' 				=> $nitTxt,
    'ventasTotales' 	=> number_format($totalVentas, 2, ".", ""),
    'totalImpuestos' 	=> number_format($totalImpuestos, 2, ".", ""),
    'detalleVentas' 	=> $data
];
ob_clean();
echo json_encode($response, JSON_PRETTY_PRINT);
?>
