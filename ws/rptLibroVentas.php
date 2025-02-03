<?php
header('Content-Type: application/json');
ini_set('memory_limit','1G');

require('../function_formatofecha.php');
require('../conexionmysqli.inc');
require('../funcion_nombres.php');

$codAnio 		= $_GET['codAnio'] ?? '';
$codMes 		= $_GET['codMes'] ?? '';
$rpt_territorio = $_GET['codTipoTerritorio'] ?? '';
$tipo 			= $_GET['tipo'] ?? '';
$fecha_reporte 	= date("d/m/Y");

$sqlConf 	= "SELECT id, valor FROM configuracion_facturas WHERE id=1";
$respConf 	= mysqli_query($enlaceCon, $sqlConf);
$nombreTxt  = mysqli_result($respConf, 0, 1);

$sqlConf  = "SELECT id, valor FROM configuracion_facturas WHERE id=9";
$respConf = mysqli_query($enlaceCon, $sqlConf);
$nitTxt   = mysqli_result($respConf, 0, 1);

if ($tipo > 0) {
    $sqlTipo = $tipo == 1 ? " AND s.cod_tipo_doc='1' " : " AND s.cod_tipo_doc='4' ";
} else {
    $sqlTipo = "";
}

$sql = "SELECT s.nro_correlativo, s.fecha AS fecha, s.monto_final, s.razon_social, s.nit, s.siat_cuf AS nro_autorizacion, s.salida_anulada, '0' AS cod_control, 
	(SELECT c.descripcion FROM ciudades c, almacenes a WHERE a.cod_ciudad=c.cod_ciudad AND a.cod_almacen=s.cod_almacen) AS nombre_ciudad, s.cod_tipo_doc, s.siat_complemento 
	FROM salida_almacenes s 
	WHERE YEAR(s.fecha)='$codAnio' AND MONTH(s.fecha)='$codMes' ";

if(!empty($rpt_territorio)){
    $sql .= " AND s.cod_almacen IN (SELECT a.cod_almacen FROM almacenes a WHERE a.cod_ciudad IN ($rpt_territorio)) ";
}

$sql .= " AND s.siat_estado_facturacion = 1 ORDER BY s.nro_correlativo";

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
        'fecha' 			=> $datos['fecha'],
        'nroFactura' 		=> $datos['nro_correlativo'],
        'nroAutorizacion' 	=> $datos['nro_autorizacion'],
        'nit'			 	=> $datos['nit'],
        "complementoDocumento" => $datos['siat_complemento'],
        'razonSocial' 		=> $datos['razon_social'],
        'importeTotalVenta' => number_format($importe, 2, ".", ""),
        "ice"           	=> 0,
        "iehd"          	=> 0,
        "ipj"           	=> 0,
        "tasa"          	=> 0,
        "otros"         	=> 0,
        "exportacionesExentas" => 0,
        'ventasTasaCero' 	   => 0,
        'subtotal'			   => number_format($importe, 2, ".", ""),
        'descuentos' 		   => 0,
        "giftcard"      	   => 0,
        "importeBaseDebitoFiscal" => number_format($importe, 2, ".", ""),
        'debitoFiscal' 			  => number_format($montoIVA, 2, ".", ""),
        'estado' 				  => $nombreEstado,
        'codigoControl' 		  => $datos['cod_control'],
        "tipoVenta"               => "OTROS",
		"derechoCreditoFiscal"	  => "SI",
		"estadoConsolidacion"	  => "PENDIENTE",
		"area"					  => "FARMACIA"



        // 'ciudad' 			=> $datos['nombre_ciudad'],
        // 'tipo' 				=> $nomTipo,
        // 'esp' 				=> '3',
        // 'nro' 				=> $datos['nro_correlativo'],
        // 'importeBaseDebitoFiscal' => number_format($importe, 2, ".", ""),
    ];
}

$response = [
    'fechaReporte' 		=> $fecha_reporte,
    'nombreRazonSocial' => $nombreTxt,
    'nit' 				=> $nitTxt,
    'ventasTotales' 	=> number_format($totalVentas, 2, ".", ""),
    'totalImpuestos' 	=> number_format($totalImpuestos, 2, ".", ""),
    'detalleVentas' 	=> $data
];
ob_clean();
echo json_encode($response, JSON_PRETTY_PRINT);
?>
