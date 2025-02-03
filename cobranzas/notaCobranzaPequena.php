<?php
require('../fpdf.php');
require('../conexionmysqlipdf.inc');
//require('../funciones.php');
require('../NumeroALetras.php');

function redondear2($valor) { 
	$float_redondeado=round($valor * 100) / 100; 
	return $float_redondeado; 
 }

$codCobro=$_GET['codCobro'];

$sqlNumItems="SELECT count(*) from cobros_detalle c where c.cod_cobro='$codCobro'";
$respNumItems=mysqli_query($enlaceCon, $sqlNumItems);
$cantidadItems=1;
if($datNumItems = mysqli_fetch_array($respNumItems)){
	$cantidadItems=$datNumItems[0];
}
$largoDetalle=35*$cantidadItems;

//consulta cuantos items tiene el detalle
// $tamanoLargo=144;
//$tamanoLargo=179;
//$tamanoLargo=214;
$tamanoLargo=110+$largoDetalle;

$pdf=new FPDF('P','mm',array(58,$tamanoLargo));
$pdf->SetMargins(0,0,0);
$pdf->AddPage(); 
$pdf->SetFont('Arial','',12);

$y=0;
$incremento=3;

error_reporting(E_ALL);
ini_set('display_errors', '1');


//desde aca
$sqlConf="select id, valor from configuracion_facturas where id=1";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$nombreEmpresa=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=2";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$ciudadEmpresa=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=3";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$direccionEmpresa=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=9";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$nitEmpresa=mysqli_result($respConf,0,1);


//datos documento				
$sqlDatos="select c.`cod_cobro`, c.`fecha_cobro`,c.`observaciones`,c.`monto_cobro`,
(select concat(f.paterno,' ',f.nombres) from funcionarios f where f.codigo_funcionario = c.cod_funcionario), 
c.nro_cobro, (select g.nombre_gestion from gestiones g where g.cod_gestion=c.cod_gestion) 
from `cobros_cab` c where c.cod_cobro='$codCobro' order by c.`cod_cobro` desc";

//echo $sqlDatos;

$respDatos=mysqli_query($enlaceCon, $sqlDatos);
while($datDatos=mysqli_fetch_array($respDatos)){
	$fechaCobro=$datDatos[1];
	$obsNota=$datDatos[2];
	$montoCobro=$datDatos[3];
	$nombreCliente=$datDatos[4];			
	$nroCobro=$datDatos[5];
	$gestion=$datDatos[6];
}

$pdf->SetFont('Arial','',14);
$pdf->SetXY(0,$y+10);		$pdf->Cell(0,0,"ZITROMEDICAL S.R.L.", 0,0,"C");
$pdf->SetXY(0,$y+15);		$pdf->Cell(0,0,"Cobro Nro. $nroCobro", 0,0,"C");
$pdf->SetXY(0,$y+20);		$pdf->Cell(0,0,"$fechaCobro",0,0,"C");
$pdf->SetXY(0,$y+25);		$pdf->Cell(0,0,"F: $nombreCliente",0,0,"C");
$pdf->SetXY(0,$y+30);		$pdf->Cell(0,0,"Obs: $obsNota",0,0,"C");


$y=$y-5;

$pdf->SetFont('Arial','',12);
$pdf->SetXY(0,$y+45);		$pdf->Cell(0,0,"===================================",0,0,"C");
// $pdf->SetXY(2,$y+48);		$pdf->Cell(0,0,"Fecha");
// $pdf->SetXY(15,$y+48);		$pdf->Cell(0,0,"#Recibo");
// $pdf->SetXY(30,$y+48);		$pdf->Cell(0,0,"#Venta");
// $pdf->SetXY(43,$y+48);		$pdf->Cell(0,0,"Monto");
$pdf->SetXY(0,$y+48);		$pdf->Cell(0,0,"Concepto",0,0,"C");
$pdf->SetXY(0,$y+51);		$pdf->Cell(0,0,"===================================",0,0,"C");



$pdf->SetFont('Arial','',14);
$yyy=53;

$sql_detalle="select cd.`nro_doc`, cd.`monto_detalle`, td.`abreviatura`, s.`nro_correlativo`, s.`razon_social`, DATE_FORMAT(s.fecha,'%d.%m.%Y'),
	(select cli.nombre_cliente from clientes cli where cli.cod_cliente=s.cod_cliente)as cliente,
	(select tp.abreviatura from tipos_pago tp where tp.cod_tipopago=cd.cod_tipopago)as tipopago
	from `cobros_cab` c, `cobros_detalle` cd, `salida_almacenes` s, `tipos_docs` td
	where c.`cod_cobro`=cd.`cod_cobro` and cd.`cod_venta`=s.`cod_salida_almacenes` and 
	c.`cod_cobro`='$codCobro' and td.`codigo`=s.`cod_tipo_doc`";		
$resp_detalle=mysqli_query($enlaceCon, $sql_detalle);
$montoTotal=0;
$indice=1;
while($dat_detalle=mysqli_fetch_array($resp_detalle)){	
	$nroDoc=$dat_detalle[0];
	$montoDet=$dat_detalle[1];
	$nroVenta=$dat_detalle[2]."-".$dat_detalle[3];
	$razonSocial=$dat_detalle[4];
	$fechaVenta=$dat_detalle[5];
	$nombreClienteVenta=$dat_detalle[6];
	$nombreTipoPago=$dat_detalle[7];
	
	$montoTotal=$montoTotal+$montoDet;
	$montoDet=redondear2($montoDet);
	
	$pdf->SetFont('Arial','',14);
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"$nombreClienteVenta",0,0,"L");
	$yyy+=5;	
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"Fecha     : $fechaVenta",0,0,"L");	
	$yyy+=5;	
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"NroDoc.  : $nroVenta",0,0,"L");
	$yyy+=5;	
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"Forma.    : $nombreTipoPago",0,0,"L");	
	$yyy+=5;	
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"Monto     : $montoDet",0,0,"L");	
	$indice++;

	$yyy+=5;	
	$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(10,5,"---------------------------------------",0,0,"L");	

	$yyy+=10;
}

//$pdf->SetXY(0,$y+$yyy+2);		$pdf->Cell(0,0,"=================================================================================",0,0,"C");

$y=$y-5;

$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(58,5,"Total Cobro: $montoTotal",0,0,"C");

$yyy=$yyy+20;
$pdf->SetFont('Arial','',12);

$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(58,5,"Entregue Conforme",0,0,"C");
$yyy=$yyy+20;

$pdf->SetXY(0,$y+$yyy);		$pdf->Cell(58,5,"Recibi Conforme",0,0,"C");

$nombreArchivo=$nombreClienteVenta."_C".$nroCobro.".pdf";
$pdf->Output();

?>