<?php
set_time_limit(0);

$start_time = microtime(true);
require("conexionmysqli.php");
require("estilos_almacenes.inc");
require("funciones.php");
require("funciones_inventarios.php");
require("enviar_correo/php/send-email_anulacion.php");

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

$globalUsuario  = $_COOKIE['global_usuario'];
$globalSucursal	= $_COOKIE['global_agencia'];

// Obtiene numero correlativo
$sqlCorrelativo="SELECT IFNULL(max(numero)+1,1) FROM pedidos";
$respCorrelativo=mysqli_query($enlaceCon,$sqlCorrelativo);
$dataCorrelativo=mysqli_fetch_array($respCorrelativo);

/*VALIDACION MANUAL CASOS ESPECIALES*/
$nit = $_POST['nitCliente'];
$siat_codigotipodocumentoidentidad = $_POST['tipo_documento'];
if((int)$nit=='99001' || (int)$nit=='99002' || (int)$nit=='99003'){
	$siat_codigotipodocumentoidentidad = 5; //nit
}
// Complemento
$siat_complemento = $_POST['complemento'];
if($siat_codigotipodocumentoidentidad == 5){
	$siat_complemento = "";
}

/**
 * CABECERA DE PEDIDO
 **/
$cod_almacen 	= $_POST['almacen_origen'];
$cod_tipo_doc 	= $_POST['tipoDoc'];
$fecha 			= date('Y-m-d H:i:s');
$observaciones 	= $_POST['observaciones'] ?? '';
$estado 		= 1;
$numero 		= $dataCorrelativo[0];
$pedido_anulado = 0;
$cod_cliente 	= $_POST['cliente'];
$monto_total 	= $_POST['totalVenta'];
$descuento 		= $_POST['descuentoVenta'];
$monto_final 	= $_POST['totalFinal'];
$razon_social 	= $_POST['razonSocial'];
$cod_funcionario= $_POST['cod_vendedor']; // Codigo de vendedor
$cod_tipopago 	= $_POST['tipoVenta'];
$created_by 		   = $globalUsuario;
$created_at 		   = $fecha;
$cod_tipopreciogeneral = ''; // ! Consultar
$fecha_a_facturar	   = $_POST['fecha']; // Fecha Factura


//SACAMOS LA CONFIGURACION PARA CONOCER EL CONTROL DE STOCKS
$banderaValidacionStock=obtenerValorConfiguracion($enlaceCon,'-4');

$sql_insert = "INSERT INTO pedidos(cod_almacen, cod_tipo_doc, fecha, observaciones, estado, numero, pedido_anulado, cod_cliente, monto_total, descuento, monto_final, razon_social, nit, cod_funcionario, cod_tipopago, siat_codigotipodocumentoidentidad, siat_complemento, created_by, created_at, cod_tipopreciogeneral, fecha_a_facturar)
values ('$cod_almacen', '$cod_tipo_doc', '$fecha', '$observaciones', '$estado', '$numero', '$pedido_anulado', '$cod_cliente', '$monto_total', '$descuento', '$monto_final', '$razon_social', '$nit', '$cod_funcionario', '$cod_tipopago', '$siat_codigotipodocumentoidentidad', '$siat_complemento', '$created_by', '$created_at', '$cod_tipopreciogeneral', '$fecha_a_facturar')";
$sql_inserta=mysqli_query($enlaceCon,$sql_insert);




//echo $sql_insert;




if(!$sql_insert){
	echo "<script language='Javascript'>
			Swal.fire({
				title: 'Error!',
				html: 'No se pudo registrar los datos principales',
				type: 'success'
			}).then(function() {
				location.href='registrar_salidapedidos.php'; 
			});
		</script>";
	exit;
}

$cod_pedido = 0;
if ($sql_inserta) {
    // Capturar el ID del registro insertado
    $cod_pedido = mysqli_insert_id($enlaceCon);
}
/**
 * DETALLE DE PEDIDO
 */
$banderaErrorCantidadRestante = 0;
for($i = 1; $i <= $cantidad_material; $i++){   	
	$codMaterial = $_POST["materiales$i"];
	if($codMaterial != 0){
		$cantidadUnitaria 	= $_POST["cantidad_unitaria$i"];
		$precioUnitario 	= $_POST["precio_unitario$i"];
		$descuentoProducto	= $_POST["descuentoProducto$i"];

		
		$cod_producto 		= $_POST["materiales$i"];
		$cantidad_unitaria  = $_POST["cantidad_unitaria$i"];
		$precio 			= $_POST["precio_unitario$i"];
		$descuento 			= $_POST["descuentoProducto$i"];
		$monto 				= ($cantidad_unitaria * $precio) - $descuento;
		$orden 				= $i;

		
		// EN EL PEDIDO REGISTRAMOS SIN TOMCAR EN CUENTA EL INVENTARIO
		$sql_insert_detalle = "INSERT INTO pedidos_detalle(cod_pedido, cod_producto, cantidad_unitaria, precio, descuento, monto, orden)
		values ('$cod_pedido', '$cod_producto', '$cantidad_unitaria', '$precio', '$descuento', '$monto', '$orden')";
		$sql_inserta_detalle = mysqli_query($enlaceCon,$sql_insert_detalle);
		
		//echo "<br>".$sql_insert_detalle."<br>";

	}
}

if(!$sql_insert_detalle){
	echo "<script language='Javascript'>
			Swal.fire({
				title: 'Error!',
				html: 'No se pudo registrar los datos principales',
				type: 'success'
			}).then(function() {
				location.href='registrar_salidapedidos.php'; 
			});
		</script>";
	exit;
}else{
	echo "<script language='Javascript'>
		Swal.fire({
		title: 'Exito!',
		html: 'Se realizo correctamente el registro',
		type: 'success'
		}).then(function() {
		   location.href='navegadorPedidos.php'; 
		});
		</script>";
}

?>