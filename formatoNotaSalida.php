<?php

if (!function_exists('mysqli_result')) {
    function mysqli_result($result, $number, $field=0) {
        mysqli_data_seek($result, $number);
        $row = mysqli_fetch_array($result);
        return $row[$field];
    }
}

require_once 'config.php';
$enlaceCon=mysqli_connect(DATABASE_HOST,DATABASE_USER,DATABASE_PASSWORD,DATABASE_NAME);

require('fpdf186/fpdf.php');
//require('conexionmysqlipdf.inc');
require('funciones.php');
require('NumeroALetras.php');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

$codigoVenta=$_GET["codVenta"];
$globalAlmacen=$_COOKIE['global_almacen'];

//consulta cuantos items tiene el detalle
$sqlNro="select count(*) from `salida_detalle_almacenes` s where s.`cod_salida_almacen`=$codigoVenta";
$respNro=mysqli_query($enlaceCon,$sqlNro);
$nroItems=mysqli_result($respNro,0,0);

$tamanoLargo=200+($nroItems*3)-3;

$pdf=new FPDF('P','mm',array(74,$tamanoLargo));
//$pdf=new FPDF('P','mm',array(100,$tamanoLargo));
$pdf->SetMargins(0,0,0);
$pdf->AddPage(); 
$pdf->SetFont('Arial','',10);

$y=0;
$incremento=3;

/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

// Variable de configuración - Mostrar Stock
$sqlConf  	  = "select id_configuracion, valor_configuracion from configuraciones where id_configuracion=25";
$respConf 	  = mysqli_query($enlaceCon,$sqlConf);
$mostrarStock = mysqli_result($respConf,0,1);

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

$sqlConf="select id, valor from configuracion_facturas where id=4";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$telefonoTxt=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=5";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$ciudadTxt=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=6";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$txt1=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=7";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$txt2=mysqli_result($respConf,0,1);

$sqlConf="select id, valor from configuracion_facturas where id=8";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$txt3=mysqli_result($respConf,0,1);



$sqlConf="select id, valor from configuracion_facturas where id=9";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$nitEmpresa=mysqli_result($respConf,0,1);
		
$sqlDatosVenta="select concat(s.fecha,' ',s.hora_salida) as fecha, t.`nombre`, 
(select c.nombre_cliente from clientes c where c.cod_cliente=s.cod_cliente) as nombreCliente, 
s.`nro_correlativo`, s.razon_social, s.observaciones, s.descuento, (select tp.nombre_tipopago from tipos_pago tp where tp.cod_tipopago=s.cod_tipopago), s.monto_efectivo,s.monto_cambio, s.cod_chofer, s.nombre_cliente,
(select ts.nombre_tiposalida from tipos_salida ts where ts.cod_tiposalida=s.cod_tiposalida) as tiposalida
		from `salida_almacenes` s, `tipos_docs` t
		where s.`cod_salida_almacenes`='$codigoVenta'  and
		s.`cod_tipo_doc`=t.`codigo`";
$respDatosVenta=mysqli_query($enlaceCon,$sqlDatosVenta);
$montoEfectivo2=0;
$montoCambio2=0;
while($datDatosVenta=mysqli_fetch_array($respDatosVenta)){
	$fechaVenta=$datDatosVenta[0];
	$nombreTipoDoc=$datDatosVenta[1];
	$nombreCliente=$datDatosVenta[2];
	$nroDocVenta=$datDatosVenta[3];
	$razonSocial=$datDatosVenta[4];
	$obsVenta=$datDatosVenta[5];
	$descuentoVenta=$datDatosVenta[6];
	$descuentoVenta=redondear2($descuentoVenta);
	$tipoPago=$datDatosVenta[7];

	$montoEfectivo2=$datDatosVenta['monto_efectivo'];
	$montoCambio2=$datDatosVenta['monto_cambio'];

	$codVendedor=$datDatosVenta["cod_chofer"];

	$nombrePaciente=$datDatosVenta["nombre_cliente"];

	$nombreTipoSalida=$datDatosVenta["tiposalida"];


	$montoEfectivo2=redondear2($montoEfectivo2);
	$montoCambio2=redondear2($montoCambio2);

}


//$pdf->SetXY(0,$y+3);		$pdf->Cell(0,0,$nombre1,0,0,"C");
//$pdf->SetXY(0,$y+6);		$pdf->Cell(0,0,$nombre2,0,0,"C");

$pdf->SetXY(0,$y+9);		$pdf->Cell(0,0,"$nombreTipoDoc Nro. $nroDocVenta", 0,0,"C");

$pdf->SetXY(0,$y+14);		$pdf->Cell(0,0,"Tipo Salida: $nombreTipoSalida", 0,0,"C");

$pdf->SetXY(0,$y+17);		$pdf->Cell(0,0,"-------------------------------------------------------------------------------", 0,0,"C");


$pdf->SetXY(0,$y+21);		$pdf->Cell(0,0,"FECHA: $fechaVenta",0,0,"C");
$pdf->SetXY(0,$y+26);		$pdf->Cell(0,0,"Nombre / RazonSocial: $razonSocial",0,0,"C");
$pdf->SetXY(0,$y+31);		$pdf->Cell(0,0,"Paciente: $nombrePaciente",0,0,"C");


$y=$y-11;

$pdf->SetXY(0,$y+45);		$pdf->Cell(0,0,"=================================================================================",0,0,"C");
$pdf->SetXY(10,$y+48);		$pdf->Cell(0,0,"Producto");
$pdf->SetXY(55,$y+48);		$pdf->Cell(0,0,"Cantidad");
$pdf->SetXY(0,$y+52);		$pdf->Cell(0,0,"=================================================================================",0,0,"C");



$sqlDetalle="select m.codigo_material, sum(s.`cantidad_unitaria`), m.`descripcion_material`, s.`precio_unitario`, 
		sum(s.`descuento_unitario`), sum(s.`monto_unitario`) from `salida_detalle_almacenes` s, `material_apoyo` m where 
		m.`codigo_material`=s.`cod_material` and s.`cod_salida_almacen`=$codigoVenta 
		group by s.cod_material
		order by s.orden_detalle";
$respDetalle=mysqli_query($enlaceCon,$sqlDetalle);

$yyy=55;

$montoTotal=0;
$montoUnit1=0;
$montoUnit2=0;
while($datDetalle=mysqli_fetch_array($respDetalle)){
	$codInterno=$datDetalle[0];
	$cantUnit=$datDetalle[1];
	$cantUnit=redondear2($cantUnit);
	$nombreMat=$datDetalle[2];
	$nombreMat=substr($nombreMat,0,45);
	$precioUnit=$datDetalle[3];
	$precioUnit=redondear2($precioUnit);
	$descUnit=$datDetalle[4];
	$descUnit=redondear2($descUnit);
	$montoUnit1=$datDetalle[5];
	
	$montoUnit2=$montoUnit1-$descUnit;
	$montoUnit2=redondear2($montoUnit2);
	
	$nombreMat=$nombreMat."($codInterno)";

	if(strlen($nombreMat)>41){
		$nombreMat=substr($nombreMat, 0, 41);  // abcdef
	}

	$pdf->SetFont('Arial','',7);
	//$pdf->SetXY(5,$y+$yyy);		$pdf->MultiCell(60,3,"$nombreMat",1,"C");
	$pdf->SetXY(2,$y+$yyy);		$pdf->Cell(60,5,"$nombreMat",0,0,"L");
		
	$pdf->SetXY(60,$y+$yyy);		$pdf->Cell(10,5,"$cantUnit",0,0,"R");		
	$yyy=$yyy+6;
}
$pdf->SetXY(0,$y+$yyy+2);		$pdf->Cell(0,0,"=================================================================================",0,0,"C");		
$yyy=$yyy+5;

$sqlResponsable="select CONCAT(SUBSTRING_INDEX(nombres,' ', 1),' ',SUBSTR(paterno, 1,1),'.') from funcionarios where codigo_funcionario='".$codVendedor."'";

//echo "entro respo".$sqlResponsable;

$respResponsable=mysqli_query($enlaceCon,$sqlResponsable);
$datResponsable=mysqli_fetch_array($respResponsable);
$nombreFuncionario=$datResponsable[0];

//$pdf->SetXY(4,$y+$yyy+30);		$pdf->Cell(68,0,"Usuario(a): $nombreFuncionario",0,0,"C");


$pdf->Output();

?>