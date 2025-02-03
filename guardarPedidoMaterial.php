<?php
require("conexionmysqli.inc");
require("funciones.php");
require("funciones_inventarios.php");
error_reporting(E_ALL);
ini_set('display_errors', '1');


$usuarioVendedor=$_COOKIE['global_usuario'];
$globalSucursal=$_COOKIE['global_agencia'];

$tipoSalida=1000; //traspaso
$tipoDoc=$_POST['tipoDoc'];

$almacenDestino=2;//tienda  $_POST['almacen'];
$codCliente=$_POST['cliente'];

$tipoPrecio=$_POST['tipoPrecio'] ?? '';
$razonSocial=$_POST['razonSocial'];
$nitCliente=$_POST['nitCliente'];
$tipoVenta=$_POST['tipoVenta'];

$observaciones=$_POST["observaciones"];
$motivo=$_POST["motivo"];
$almacenOrigen=$global_almacen;

$totalVenta=$_POST["totalVenta"];
$descuentoVenta=$_POST["descuentoVenta"];
$totalFinal=$_POST["totalFinal"];

$totalEfectivo=$_POST["efectivoRecibido"];
$totalCambio=$_POST["cambioEfectivo"];

$totalFinalRedondeado=round($totalFinal);

//VALIDAMOS QUE NO SEA CERO EL VALOR DEL REDONDEADO PARA EL CODIGO DE ControlCode
if($totalFinalRedondeado==0){
	$totalFinalRedondeado=1;
}

$fecha=$_POST["fecha"];
$cantidad_material=$_POST["cantidad_material"];

if($descuentoVenta=="" || $descuentoVenta==0){
	$descuentoVenta=0;
}

$vehiculo="";

// $fecha=formateaFechaVista($fecha);
//$fecha=date("Y-m-d");
$hora=date("H:i:s");

//SACAMOS LA CONFIGURACION PARA EL DOCUMENTO POR DEFECTO
$sqlConf="select valor_configuracion from configuraciones where id_configuracion=1";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$tipoDocDefault=mysqli_result($respConf,0,0);

//SACAMOS LA CONFIGURACION PARA EL CLIENTE POR DEFECTO
$sqlConf="select valor_configuracion from configuraciones where id_configuracion=2";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$clienteDefault=mysqli_result($respConf,0,0);

//SACAMOS LA CONFIGURACION PARA CONOCER SI LA FACTURACION ESTA ACTIVADA
$sqlConf="select valor_configuracion from configuraciones where id_configuracion=3";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$facturacionActivada=mysqli_result($respConf,0,0);

//SACAMOS LA CONFIGURACION PARA LA SALIDA POR VENCIMIENTO
$sqlConf="select valor_configuracion from configuraciones where id_configuracion=5";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$tipoSalidaVencimiento=mysqli_result($respConf,0,0);

//HABILITAMOS LA BANDERA DE VENCIDOS PARA SACAR SOLO VENCIDOS
$banderaVencidos=0;
if($tipoSalida==$tipoSalidaVencimiento){
	$banderaVencidos=1;
}


$sql="SELECT IFNULL(max(cod_salida_almacenes)+1,1) FROM pedido_almacenes";
$resp=mysqli_query($enlaceCon,$sql);
$codigo=mysqli_result($resp,0,0);


$vectorNroCorrelativo=numeroCorrelativo($enlaceCon, $tipoDoc);
$nro_correlativo=$vectorNroCorrelativo[0];
$cod_dosificacion=$vectorNroCorrelativo[2];

if($facturacionActivada==1 && $tipoDoc==1){
		//SACAMOS DATOS DE LA DOSIFICACION PARA INSERTAR EN LAS FACTURAS EMITIDAS
	// $sqlDatosDosif="select d.nro_autorizacion, d.llave_dosificacion 
	// 	from dosificaciones d where d.cod_dosificacion='$cod_dosificacion'";
	// $respDatosDosif=mysqli_query($enlaceCon,$sqlDatosDosif);
	// $nroAutorizacion=mysqli_result($respDatosDosif,0,0);
	// $llaveDosificacion=mysqli_result($respDatosDosif,0,1);
	// include 'controlcode/sin/ControlCode.php';
	// $controlCode = new ControlCode();
	// $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
	// 							   $nro_correlativo,//Numero de factura
	// 							   $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
	// 							   str_replace('-','',$fecha),//fecha de transaccion de la forma AAAAMMDD
	// 							   $totalFinalRedondeado,//Monto de la transacción
	// 							   $llaveDosificacion//Llave de dosificación
	// 							   );
	//FIN DATOS FACTURA
}

$created_by=$usuarioVendedor;
$created_at=date("Y-m-d H:i:s");

$sql_inserta="INSERT INTO `pedido_almacenes`(`cod_salida_almacenes`, `cod_almacen`,`cod_tiposalida`, 
		`cod_tipo_doc`, `fecha`, `hora_salida`, `territorio_destino`, 
		`almacen_destino`, `observaciones`, `estado_salida`, `nro_correlativo`, `salida_anulada`, 
		`cod_cliente`, `monto_total`, `descuento`, `monto_final`, razon_social, nit, cod_chofer, cod_vehiculo, monto_cancelado, cod_dosificacion, monto_efectivo, monto_cambio,cod_tipopago,created_by,created_at,cod_observacion)
		values ('$codigo', '$almacenOrigen', '$tipoSalida', '$tipoDoc', '$fecha', '$hora', '0', '$almacenDestino', 
		'$observaciones', '1', '$nro_correlativo', 0, '$codCliente', '$totalVenta', '$descuentoVenta', '$totalFinal', '$razonSocial', 
		'$nitCliente', '$usuarioVendedor', '$vehiculo',0,'$cod_dosificacion','$totalEfectivo','$totalCambio','$tipoVenta','$created_by','$created_at','$motivo')";
$sql_inserta=mysqli_query($enlaceCon,$sql_inserta);

if($sql_inserta==1){

	$montoTotalVentaDetalle=0;
	for($i=1;$i<=$cantidad_material;$i++)
	{   	
		$codMaterial=$_POST["materiales$i"];
		if($codMaterial!=0){
			$cantidadUnitaria=$_POST["cantidad_unitaria$i"];
			$precioUnitario=obtenerPrecioProductoSucursal($codMaterial);
			//$precioUnitario=$_POST["precio_unitario$i"];
			$descuentoProducto=$_POST["descuentoProducto$i"];
			$montoMaterial=$_POST["montoMaterial$i"];
			$stock=$_POST["stock$i"];
			$montoTotalVentaDetalle=$montoTotalVentaDetalle+$montoMaterial;
			
			$respuesta=insertar_detalle_pedido($codigo, $almacenOrigen,$codMaterial,$cantidadUnitaria,$precioUnitario,$descuentoProducto,$montoMaterial,$banderaVencidos,$i,$stock);
			if($respuesta!=1){
				echo "#_#_#_#0";
			}
		}			
	}
	
	$montoTotalConDescuento=$montoTotalVentaDetalle-$descuentoVenta;
	//ACTUALIZAMOS EL PRECIO CON EL DETALLE
	$sqlUpdMonto="update pedido_almacenes set monto_total='$montoTotalVentaDetalle', monto_final='$montoTotalConDescuento' 
				where cod_salida_almacenes='$codigo'";
	$respUpdMonto=mysqli_query($enlaceCon,$sqlUpdMonto);
	
	echo "#_#_#_#1";
	
}else{
	echo "#_#_#_#0";
}

?>



