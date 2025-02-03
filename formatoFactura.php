<?php
//header('Content-Type: text/html; charset=ISO-8859-1');

require('fpdf186/fpdf.php');
require('funciones.php');
require('funcion_nombres.php');
require('NumeroALetras.php');
include('phpqrcode/qrlib.php');

require('conexionmysqlipdf.inc');

//header("Content-Type: text/html; charset=iso-8859-1 ");
mysqli_query($enlaceCon,"SET NAMES utf8");


/* error_reporting(E_ALL);
 ini_set('display_errors', '1');
*/


$codigoVenta=$_GET["codVenta"];
$cod_ciudad=$_COOKIE["global_agencia"];

//consulta cuantos items tiene el detalle
$sqlNro="select count(*) from `salida_detalle_almacenes` s where s.`cod_salida_almacen`=$codigoVenta";
$respNro=mysqli_query($enlaceCon,$sqlNro);
$datNro=mysqli_fetch_array($respNro);
$nroItems=$datNro[0];
//$nroItems=mysqli_result($respNro,0,0);

$tamanoLargo=470+($nroItems*3)-3;

$pdf=new FPDF('P','mm',array(74,$tamanoLargo));
$pdf->SetMargins(0,0,0);
$pdf->AddPage(); 
$pdf->SetFont('Arial','',8);

//echo "entro 1";

$sqlConf="select id, valor from configuracion_facturas where id=1 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$nombreTxt=$datConf[1];//$nombreTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=10 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$nombreTxt2=$datConf[1];//$nombreTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=2 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$sucursalTxt=$datConf[1];//$sucursalTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=3 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$direccionTxt=$datConf[1];//$direccionTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=4 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$telefonoTxt=$datConf[1];//$telefonoTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=5 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$ciudadTxt=$datConf[1];//$ciudadTxt=mysql_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=6 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$txt1=$datConf[1];//$txt1=mysql_result($respConf,0,1);


$sqlConf="select id, valor from siat_leyendas where id=1";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$txt2=$datConf[1];


$sqlConf="select id, valor from configuracion_facturas where id=9 and cod_ciudad='$cod_ciudad'";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$nitTxt=$datConf[1];//$nitTxt=mysql_result($respConf,0,1);

/*$sqlDatosFactura="select d.nro_autorizacion, DATE_FORMAT(d.fecha_limite_emision, '%d/%m/%Y'), f.codigo_control, f.nit, f.razon_social from facturas_venta f, dosificaciones d
	where f.cod_dosificacion=d.cod_dosificacion and f.cod_venta=$codigoVenta";*/
$sqlDatosFactura="select '' as nro_autorizacion, '', '' as codigo_control, f.nit, f.razon_social, DATE_FORMAT(f.siat_fechaemision, '%d/%m/%Y') 
from salida_almacenes f where f.cod_salida_almacenes=$codigoVenta";

//echo $sqlDatosFactura;

$respDatosFactura=mysqli_query($enlaceCon,$sqlDatosFactura);
$datDatosFactura=mysqli_fetch_array($respDatosFactura);

$nroAutorizacion=$datDatosFactura[0];//$nroAutorizacion=mysql_result($respDatosFactura,0,0);
$fechaLimiteEmision=$datDatosFactura[1];//$fechaLimiteEmision=mysql_result($respDatosFactura,0,1);
$codigoControl=$datDatosFactura[2];//$codigoControl=mysql_result($respDatosFactura,0,2);
$nitCliente=$datDatosFactura[3];//$nitCliente=mysql_result($respDatosFactura,0,3);
$razonSocialCliente=$datDatosFactura[4];//$razonSocialCliente=mysql_result($respDatosFactura,0,4);
$razonSocialCliente=strtoupper($razonSocialCliente);
$fechaFactura=$datDatosFactura[5];
$cod_funcionario=$_COOKIE["global_usuario"];


//datos documento

$sqlDatosVenta="SELECT DATE_FORMAT(s.fecha, '%d/%m/%Y'), t.`nombre`, c.`nombre_cliente`, s.`nro_correlativo`, 
s.descuento, s.hora_salida,s.monto_total,s.monto_final,s.monto_cancelado_bs,s.monto_cambio,s.cod_chofer,s.cod_tipopago,s.cod_tipo_doc,s.fecha,
(SELECT cod_ciudad from almacenes where cod_almacen=s.cod_almacen)as cod_ciudad,s.cod_cliente,
(SELECT cufd from siat_cufd where codigo=s.siat_codigocufd) as cufd,siat_cuf,siat_complemento,s.siat_codigoPuntoVenta,
s.siat_codigotipoemision,(SELECT descripcionLeyenda from siat_sincronizarlistaleyendasfactura where codigo=s.siat_cod_leyenda) as leyenda, 
	s.nombre_cliente as clienteexterno, s.estado_salida, s.salida_anulada, s.observaciones,
	(select tp.nombre_tipopago from tipos_pago tp where tp.cod_tipopago=s.cod_tipopago) as nombretipopago
		from `salida_almacenes` s
		LEFT JOIN `clientes` c ON s.`cod_cliente`=c.`cod_cliente`
		LEFT JOIN tipos_docs t ON s.`cod_tipo_doc`=t.`codigo`
		where s.`cod_salida_almacenes`='$codigoVenta'";

//echo "<br>".$sqlDatosVenta;

$respDatosVenta=mysqli_query($enlaceCon,$sqlDatosVenta);
$tipoPago=1;
$siat_codigotipoemision=0;
$siat_codigopuntoventa=0;

while($datDatosVenta=mysqli_fetch_array($respDatosVenta)){
	$fechaVenta=$datDatosVenta[0];
	$nombreTipoDoc=$datDatosVenta[1];
	$nombreCliente=$datDatosVenta[2];
	$nroDocVenta=$datDatosVenta[3];
	$descuentoVenta=$datDatosVenta[4];
	$descuentoVenta=redondear2($descuentoVenta);
	$horaFactura=$datDatosVenta[5];
	$montoTotal2=$datDatosVenta['monto_total'];
	$montoFinal2=$datDatosVenta['monto_final'];

	$montoEfectivo2=$datDatosVenta['monto_cancelado_bs'];
	$montoCambio2=$datDatosVenta['monto_cambio'];
	$montoCambio2=$montoEfectivo2-$montoFinal2;

	$montoTotal2=redondear2($montoTotal2);
	$montoFinal2=redondear2($montoFinal2);

	$montoEfectivo2=redondear2($montoEfectivo2);
	$montoCambio2=redondear2($montoCambio2);

	$descuentoCabecera=$datDatosVenta['descuento'];
	$cod_funcionario=$datDatosVenta['cod_chofer'];
	$tipoPago=$datDatosVenta['cod_tipopago'];
	$tipoDoc=$datDatosVenta['nombre'];
	$codTipoDoc=$datDatosVenta['cod_tipo_doc'];

	$fecha_salida=$datDatosVenta['fecha'];
	$hora_salida=$datDatosVenta['hora_salida'];
	$cod_ciudad_salida=$datDatosVenta['cod_ciudad'];
	$cod_cliente=$datDatosVenta['cod_cliente'];

	$nroCufd=$datDatosVenta['cufd'];
	$cuf=$datDatosVenta['siat_cuf'];
	$siat_complemento=$datDatosVenta['siat_complemento'];
	$siat_codigopuntoventa=$datDatosVenta['siat_codigoPuntoVenta'];
	$siat_codigotipoemision=$datDatosVenta['siat_codigotipoemision'];
	$txt3=$datDatosVenta['leyenda'];

	$txtNombreCliente=$datDatosVenta['clienteexterno'];

	$estadoSalida=$datDatosVenta['estado_salida'];
	$ventaAnulada=$datDatosVenta['salida_anulada'];

	$observacionesVenta=$datDatosVenta['observaciones'];
	$nombretipopago=$datDatosVenta['nombretipopago'];

	if($txtNombreCliente==""){
		$txtNombreCliente=$observacionesVenta;
	}
	//echo "entro detalle";
}
$sqlResponsable="select CONCAT(SUBSTR(nombres, 1,1),SUBSTR(paterno, 1,1),SUBSTR(materno, 1,1)) from funcionarios where codigo_funcionario='".$cod_funcionario."'";
$respResponsable=mysqli_query($enlaceCon,$sqlResponsable);
$nombreFuncionario=mysqli_result($respResponsable,0,0);
//$nombreFuncionario=nombreVisitador($cod_funcionario);


if($siat_codigotipoemision==2){
    $sqlConf="select id, valor from siat_leyendas where id=3";
}else{
    $sqlConf="select id, valor from siat_leyendas where id=2";
}
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$txtLeyendaFin=$datConf[1];//$txtLeyendaFin=mysqli_result($respConf,0,1);


$y=0;
$incremento=3;
//$pdf->SetFont('Arial','',10);
$pdf->SetXY(0,$y+3);		$pdf->Cell(68,0,"FACTURA",0,0,"C");
//$pdf->SetFont('Arial','',8);
$pdf->SetXY(4,$y+6);		$pdf->Cell(68,0,utf8_decode("(Con Derecho a Crédito Fiscal)"),0,0,"C");
$pdf->SetXY(4,$y+10);		$pdf->Cell(68,0,utf8_decode($nombreTxt)." ",0,0,"C");
$pdf->SetXY(4,$y+13);		$pdf->Cell(68,0,$sucursalTxt,0,0,"C");
$pdf->SetXY(4,$y+16);		$pdf->Cell(68,0,"No. Punto de Venta ".$siat_codigopuntoventa,0,0,"C");
$pdf->SetXY(4,$y+19);		$pdf->MultiCell(68,3,utf8_decode($direccionTxt), 0,"C");
$pdf->SetXY(4,$y+27);		$pdf->Cell(68,0,"Telefono:  ".$telefonoTxt,0,0,"C");
//$y=$y+6;
$pdf->SetXY(4,$y+30);		$pdf->Cell(68,0,$ciudadTxt,0,0,"C");
$pdf->SetXY(4,$y+32);		$pdf->Cell(68,0,"-------------------------------------------------------------------------", 0,0,"C");
$pdf->SetXY(4,$y+35);		$pdf->Cell(68,0,"NIT: $nitTxt", 0,0,"C");
$pdf->SetFont('Arial','B',8);
$pdf->SetXY(4,$y+38);		$pdf->Cell(68,0,"$nombreTipoDoc Nro. $nroDocVenta", 0,0,"C");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(4,$y+40);		$pdf->MultiCell(68,3,utf8_decode("COD. DE AUTORIZACIÓN: ").$cuf, 0,"C");
$pdf->SetXY(4,$y+49);		$pdf->Cell(68,0,"----------------------------------------------------------------------", 0,0,"C");
//$pdf->SetXY(4,$y+52);		$pdf->MultiCell(68,3,utf8_decode($txt1),0,"C");
$y=$y-10;
//$pdf->SetXY(4,$y+59);		$pdf->Cell(68,0,"----------------------------------------------------------------------", 0,0,"C");

$pdf->SetXY(4,$y+60);		$pdf->MultiCell(68,3,utf8_decode("Nombre/RazonSocial:").utf8_decode($razonSocialCliente),0,"C");
$y=$y+2;

$pdf->SetXY(4,$y+66);		$pdf->Cell(68,0,"NIT/CI/CEX:".$nitCliente." ".$siat_complemento,0,0,"C");
$pdf->SetXY(4,$y+69);		$pdf->Cell(68,0,"COD. CLIENTE:: $cod_cliente",0,0,"C");
$pdf->SetXY(4,$y+72);		$pdf->Cell(68,0,utf8_decode("FECHA EMISIÓN: ").$fechaFactura."  ".$horaFactura,0,0,"C");

$pdf->SetXY(4,$y+74);		$pdf->Cell(68,0,"---------------------------------------------------------------------------",0,0,"C");
$pdf->SetXY(4,$y+76);		$pdf->Cell(15,0,"Cantidad",0,0,"R");
$pdf->SetXY(19,$y+76);		$pdf->Cell(15,0,"P.U.",0,0,"R");
$pdf->SetXY(34,$y+76);		$pdf->Cell(15,0,"Desc.",0,0,"R");
$pdf->SetXY(45,$y+76);		$pdf->Cell(23,0,"SubTotal",0,0,"R");
$pdf->SetXY(4,$y+78);		$pdf->Cell(68,0,"---------------------------------------------------------------------------",0,0,"C");


$sqlDetalle="select m.codigo_material, sum(s.`cantidad_unitaria`), m.`descripcion_material`, s.`precio_unitario`, 
		sum(s.`descuento_unitario`), sum(s.`monto_unitario`) from `salida_detalle_almacenes` s, `material_apoyo` m where 
		m.`codigo_material`=s.`cod_material` and s.`cod_salida_almacen`=$codigoVenta 
		group by s.cod_material
		order by s.orden_detalle";

//echo "detail".$sqlDetalle;

$respDetalle=mysqli_query($enlaceCon,$sqlDetalle);

$yyy=79;

$montoTotal=0;$descuentoVentaProd=0;
while($datDetalle=mysqli_fetch_array($respDetalle)){

	$codInterno=$datDetalle[0];
	$cantUnit=$datDetalle[1];
	$nombreMat=$datDetalle[2];
	$nombreMat=substr($nombreMat,0,34);

	$precioUnit=$datDetalle[3];
	$descUnit=$datDetalle[4];	
	$montoUnit=($cantUnit*$precioUnit)-$descUnit;	
	//recalculamos el precio unitario para mostrar en la factura.
	$precioUnitFactura=($cantUnit*$precioUnit)/$cantUnit;
	$cantUnit=redondear2($cantUnit);
	$precioUnit=redondear2($precioUnit);
	$montoUnit=redondear2($montoUnit);	
	$precioUnitFactura=redondear2($precioUnitFactura);
	// - $descUnit
	$descUnit=redondear2($descUnit);	
	$descuentoVentaProd+=$descUnit;
	$montoUnitProd=($cantUnit*$precioUnit);
	$montoUnitProdDesc=$montoUnitProd-$descUnit;
	$montoUnitProdDesc=redondear2($montoUnitProdDesc);
	$montoUnitProd=redondear2($montoUnitProd);
	//////////////
		
	$pdf->SetXY(4,$y+$yyy);		$pdf->MultiCell(68,3,utf8_decode("($codInterno) $nombreMat"),"C");
	$yyy=$yyy+3; 
	$pdf->SetXY(4,$y+$yyy+2);		$pdf->Cell(10,0,"$cantUnit",0,0,"C");
	$pdf->SetXY(19,$y+$yyy+2);		$pdf->Cell(15,0,"$precioUnitFactura",0,0,"R");
	$pdf->SetXY(34,$y+$yyy+2);		$pdf->Cell(15,0,"$descUnit",0,0,"R");
	$pdf->SetXY(45,$y+$yyy+2);		$pdf->Cell(25,0,"$montoUnitProdDesc",0,0,"R");
	


   $montoTotal=$montoTotal+$montoUnitProdDesc;
	//montoTotal=$montoTotal+$montoUnit;
	
	$yyy=$yyy+6; 

	//echo "entro final detalle";

}

$pdf->SetXY(4,$y+$yyy+1);		$pdf->Cell(68,0,"---------------------------------------------------------------------------",0,0,"C");		
$yyy=$yyy+5;

$descuentoVenta=number_format($descuentoVenta,2,'.','');
$montoFinal=$montoTotal-$descuentoVenta;
//$montoFinal=$montoTotal-$descuentoVenta;
//$montoTotal=number_format($montoTotal,1,'.','')."0";
$montoFinal=number_format($montoFinal,2,'.','');


$pdf->SetXY(4,$y+$yyy);		$pdf->Cell(65,0,"Subtotal Bs. $montoTotal",0,0,"R");
$pdf->SetXY(4,$y+$yyy+4);		$pdf->Cell(65,0,"Descuento Bs. $descuentoVenta",0,0,"R");
$pdf->SetXY(4,$y+$yyy+8);		$pdf->Cell(65,0,"Total Bs. $montoFinal",0,0,"R");
$pdf->SetFont('Arial','B',8);
$pdf->SetXY(4,$y+$yyy+12);		$pdf->Cell(65,0,"Monto a Pagar Bs. $montoFinal",0,0,"R");
$pdf->SetFont('Arial','',8);
$pdf->SetXY(4,$y+$yyy+16);		$pdf->Cell(65,0,"Importe Base Credito Fiscal Bs. $montoFinal",0,0,"R");
$pdf->SetXY(4,$y+$yyy+18);		$pdf->Cell(65,0,"---------------------------------------------------------------------------",0,0,"C");	
	


/////////////////
$arrayDecimal=explode('.', $montoFinal);
if(count($arrayDecimal)>1){
	list($montoEntero, $montoDecimal) = explode('.', $montoFinal);
}else{
	list($montoEntero,$montoDecimal)=array($montoFinal,0);
}

if($montoDecimal==""){
	$montoDecimal="00";
}
$txtMonto=NumeroALetras::convertir($montoEntero);
/////////////////////


if($montoCambio2<0){
	$montoCambio2=0;
}

$pdf->SetXY(4,$y+$yyy+19);		$pdf->MultiCell(68,3,"Son:  $txtMonto"." ".$montoDecimal."/100 Bolivianos",0,"L");
//$pdf->SetXY(0,$y+$yyy+28);		$pdf->Cell(0,0,"",0,0,"C");
$yyy=$yyy+3;
$pdf->SetXY(4,$y+$yyy+24);		$pdf->Cell(68,0,"Total Recibido:  $montoEfectivo2 Total Cambio:  $montoCambio2",0,0,"C");	


//if($tipoPago==2){
	$pdf->SetXY(0,$y+$yyy+28);		$pdf->Cell(0,0,"Tipo de Pago: $nombretipopago",0,0,"C");	
//}	

$yyy=$yyy-5;
$pdf->SetXY(4,$y+$yyy+35);		$pdf->Cell(68,0,"---------------------------------------------------------------------------",0,0,"C");
$pdf->SetXY(4,$y+$yyy+38);		$pdf->Cell(68,0,"Proceso: $codigoVenta",0,0,"C");
$pdf->SetXY(4,$y+$yyy+41);		$pdf->Cell(68,0,"Usuario: $nombreFuncionario",0,0,"C");
$pdf->SetXY(4,$y+$yyy+44);		$pdf->Cell(68,0,"Paciente: $txtNombreCliente",0,0,"C");
//$pdf->SetXY(4,$y+$yyy+46);		$pdf->Cell(68,0,"---------------------------------------------------------------------------",0,0,"C");

//echo "entro 4";
$yyy=$yyy+2;
$pdf->SetXY(4,$y+$yyy+46);		$pdf->MultiCell(35,3,utf8_decode($txt2),0,"L");

$sqlDir="select valor_configuracion from configuraciones where id_configuracion=46";
$respDir=mysqli_query($enlaceCon,$sqlDir);
$datDir=mysqli_fetch_array($respDir);
$urlDir=$datDir[0];//$urlDir=mysqli_result($respDir,0,0);
$cadenaQR=$urlDir."/consulta/QR?nit=$nitTxt&cuf=$cuf&numero=$nroDocVenta&t=2";
$codeContents = $cadenaQR; 

$fechahora=date("dmy.His");
$fileName="qrs/".$fechahora.$nroDocVenta.".png"; 
    
QRcode::png($codeContents, $fileName,QR_ECLEVEL_L, 4);

//echo "entro 5";

$pdf->Image($fileName , 43 ,$y+$yyy+45, 30, 30,'PNG');

$pdf->SetXY(4,$y+$yyy+74);		
$pdf->MultiCell(68,3,utf8_decode($txt3),0,"C"); 
$auxY=$pdf->GetY();
$pdf->SetXY(4,$auxY+2);	
$pdf->MultiCell(68,3,utf8_decode($txtLeyendaFin),0,"C"); 

$pdf->Output("I","FAC-".$nroDocVenta);

?>