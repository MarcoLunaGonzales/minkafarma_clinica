<?php

require("conexionmysqli.php");
require("estilos_almacenes.inc");
require("funcionRecalculoCostos.php");
require("funciones.php");

date_default_timezone_set('America/La_Paz');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


//HABILITAMOS LA BANDERA DE VENCIDOS PARA ACTUALIZAR EL PRECIO
$banderaPrecioUpd=obtenerValorConfiguracion($enlaceCon,7);

$banderaUpdPreciosSucursales=obtenerValorConfiguracion($enlaceCon,49);

$sql = "select IFNULL(MAX(cod_ingreso_almacen)+1,1) from ingreso_almacenes order by cod_ingreso_almacen desc";
$resp = mysqli_query($enlaceCon,$sql);
$dat = mysqli_fetch_array($resp);
$codigo=$dat[0];
//$codigo=mysql_result($resp,0,0);

$sql = "select IFNULL(MAX(nro_correlativo)+1,1) from ingreso_almacenes where cod_almacen='$global_almacen' order by cod_ingreso_almacen desc";
$resp = mysqli_query($enlaceCon,$sql);
$dat = mysqli_fetch_array($resp);
$nro_correlativo=$dat[0];
//$nro_correlativo=mysql_result($resp,0,0);

$hora_sistema = date("H:i:s");

$tipo_ingreso=$_POST['tipo_ingreso'];
$nota_entrega=0;
$nro_factura=$_POST['nro_factura'];
$observaciones=$_POST['observaciones'];
$proveedor=$_POST['proveedor'];

$codSucursalIngreso=$_COOKIE['global_agencia'];

$createdBy=$_COOKIE['global_usuario'];
$createdDate=date("Y-m-d H:i:s");

$fecha_real=date("Y-m-d");

// Tipo de pago "CREDITO"
$fecha_factura_proveedor = empty($_POST['fecha_factura_proveedor']) ? '' : $_POST['fecha_factura_proveedor'];

$tipo_documento = $_POST['tipo_documento'];

$descuentoAdicional = $_POST['descuento_adicional'];
$descuentoTotal 	= $_POST['descuentoTotal'];

$cod_tipopago    = $_POST['cod_tipopago'];
$dias_credito    = $_POST['dias_credito'];
$monto_ingreso   = $_POST['totalCompraSD'];

$nitProveedor = $_POST['nit_proveedor'];

$monto_cancelado = 0;

if($tipo_ingreso==1003){
	$codSalida=$_POST['cod_salida'];
	$estadoSalida=4;//recepcionado
	$sqlCambiaEstado="update salida_almacenes set estado_salida='$estadoSalida' where cod_salida_almacenes=$codSalida";
	$respCambiaEstado=mysqli_query($enlaceCon,$sqlCambiaEstado);
}



$consulta="insert into ingreso_almacenes (cod_ingreso_almacen,cod_almacen,cod_tipoingreso,fecha,hora_ingreso,observaciones,cod_salida_almacen,
nota_entrega,nro_correlativo,ingreso_anulado,cod_tipo_compra,cod_orden_compra,nro_factura_proveedor,factura_proveedor,estado_liquidacion,
cod_proveedor,created_by,modified_by,created_date,modified_date,descuento_adicional,descuento_adicional2,cod_tipopago,dias_credito,monto_ingreso,monto_cancelado,fecha_factura_proveedor,cod_tipo_doc, nit_proveedor) 
values($codigo,$global_almacen,$tipo_ingreso,'$fecha_real','$hora_sistema','$observaciones','0','$nota_entrega','$nro_correlativo',0,0,0,$nro_factura,0,0,'$proveedor','$createdBy','0','$createdDate','', '$descuentoAdicional','$descuentoTotal', '$cod_tipopago','$dias_credito','$monto_ingreso','$monto_cancelado','$fecha_factura_proveedor','$tipo_documento','$nitProveedor')";

$sql_inserta = mysqli_query($enlaceCon,$consulta);
//echo "aaaa:$consulta";

if($sql_inserta==1){
	$cantidad_material = $_POST["cantidad_material"];
	for ($i = 1; $i <= $cantidad_material; $i++) {
		$cod_material = $_POST["material$i"];
		
		if($cod_material!=0){
			$cantidadPresentacion=$_POST["cantidadpresentacion$i"];

			//La Cantidad llega en Cantidad Presentacion
			$cantidad=$_POST["cantidad_unitaria$i"];
			$cantidadBonificacion=$_POST["bonificacion$i"];
			
			$cantidad=($cantidad*$cantidadPresentacion)+$cantidadBonificacion;


			$precioBruto=$_POST["precio_unitario$i"];
			$precioBruto=$precioBruto/$cantidadPresentacion;

			// $precioFinal=0;
			// if(isset($_POST["precio$i"])){
			// 	$precioFinal=$_POST["precio$i"];
			// }
			$precioFinal=0;
			if(isset($_POST["precio_old$i"])){
				$precioFinal=$_POST["precio_old$i"];
			}
			

			//$ubicacionEstante=$_POST["ubicacion_estante$i"];
			//$ubicacionFila=$_POST["ubicacion_fila$i"];
			
			$lote = empty($_POST["lote$i"])?'':$_POST["lote$i"];
			if($lote==""){
				$lote=0;
			}

			$fechaVencimiento="";
			if(isset($_POST["fechaVenc$i"])){
				$fechaVencimiento=$_POST["fechaVenc$i"];
				$fechaVencimiento=UltimoDiaMes($fechaVencimiento);
			}


			//El precioUnitario llega en Cantidad de Presentacion
			$precioUnitario=0;
			if($precioFinal>0){
				$precioUnitario=($precioFinal/$cantidad);
			}
			
			$costo=$precioUnitario;
			
			// Nuevo Campo Descuento Unitario
			$descuento_unitario=0;
			if(isset($_POST["descuento_porcentaje$i"])){
				$descuento_unitario = $_POST["descuento_porcentaje$i"];
				//AQUI SACAMOS EL COSTO UNITARIO CON EL DESCUENTO
				$costo=$precioUnitario*(1-($descuento_unitario/100));
			}

			// AQUI COSTEAMOS MAS EL DESCUENTO FINAL 1
			$descuento_adicional=0;
			if(isset($_POST["descuento_adicional$i"])){
				$descuento_adicional = $_POST["descuento_adicional$i"];
				$descuento_adicional_producto=$descuento_adicional/$cantidad;
				//LE APLICAMOS AL COSTO UNITARIO
				$costo=$costo-$descuento_adicional_producto;
			}



			
			$consulta="insert into ingreso_detalle_almacenes(cod_ingreso_almacen, cod_material, cantidad_unitaria, cantidad_restante, lote, fecha_vencimiento, 
			precio_bruto, costo_almacen, costo_actualizado, costo_actualizado_final, costo_promedio, precio_neto, cod_ubicacionestante, cod_ubicacionfila, descuento_unitario, cantidad_bonificacion, orden) 
			values($codigo,'$cod_material',$cantidad,$cantidad,'$lote','$fechaVencimiento',$precioBruto,$costo,$costo,$costo,$costo,$costo,'0','0','$descuento_unitario','$cantidadBonificacion','$i')";
			$sql_inserta2 = mysqli_query($enlaceCon,$consulta);
			
			$precioItem=0;			
			if(isset($_POST["precioclienteguardar$i"])){
				$precioItem=$_POST["precioclienteguardar$i"];			
			}

			//ARMAMOS EL ARRAY CON LOS PRECIOS
			$arrayPreciosModificar=[];
			$sqlSucursales="select cod_ciudad, descripcion from ciudades ";
			if($banderaUpdPreciosSucursales==0){
				$sqlSucursales=$sqlSucursales." where cod_ciudad='$codSucursalIngreso'";
			}
			//echo $sqlSucursales;
			$respSucursales=mysqli_query($enlaceCon,$sqlSucursales);
			while($datSucursales=mysqli_fetch_array($respSucursales)){
				$codCiudadPrecio=$datSucursales[0];
				$precioProductoModificar=$precioItem;
				$arrayPreciosModificar[$codCiudadPrecio]=$precioProductoModificar;
			}
			
			/*SOLO CUANDO ESTAN ACTIVADOS LOS CAMBIOS DE PRECIO Y EL TIPO DE INGRESO ES POR LABORATORIO*/
			if($banderaPrecioUpd>0 && $tipo_ingreso==1000){
				//SACAMOS EL ULTIMO PRECIO REGISTRADO
				$sqlPrecioActual="select precio from precios where codigo_material='$cod_material' and cod_precio=1 and cod_ciudad='$codSucursalIngreso'";
				$respPrecioActual=mysqli_query($enlaceCon,$sqlPrecioActual);
				$numFilasPrecios=mysqli_num_rows($respPrecioActual);
				$precioActual=0;
				if($numFilasPrecios>0){
					$datPrecioActual = mysqli_fetch_array($respPrecioActual);
					$precioActual=$datPrecioActual[0];
				}
								
				//SI NO EXISTE EL PRECIO LO INSERTA CASO CONTRARIO VERIFICA QUE EL PRECIO DEL INGRESO SEA MAYOR AL ACTUAL PARA HACER EL UPDATE
				/*Con la modificacion del precio cliente en pantalla de ingreso el descuento unitario siempre debe ser 0*/
				$descuentoUnitarioPrecio=0;
				if($banderaPrecioUpd==1){
					//if($precioItem!=$precioActual){
					$respModificarPrecios=actualizarPrecios($enlaceCon,$cod_material,$arrayPreciosModificar,$descuentoUnitarioPrecio);
					//}
				}
				if($banderaPrecioUpd==2){
					if($precioItem>$precioActual){
						$respModificarPrecios=actualizarPrecios($enlaceCon,$cod_material,$arrayPreciosModificar,$descuentoUnitarioPrecio);
					}
				}
			}
			
			/************************************************************************/
			/*			NUEVO REGISTRO HISTORIAL CAMPO DESCUENTO_UNITARIO			*/
			/************************************************************************/
			$fecha_hora_cambio = date('Y-m-d H:i:s');
			$consulta="INSERT INTO precios_historico(codigo_material,cod_precio,precio,cod_ciudad,descuento_unitario,fecha_hora_cambio) 
			SELECT codigo_material,cod_precio,precio,cod_ciudad,descuento_unitario,'$fecha_hora_cambio'
			FROM precios WHERE codigo_material='$cod_material' AND cod_precio = 1 AND cod_ciudad='$codSucursalIngreso'";
			$sql_inserta2 = mysqli_query($enlaceCon,$consulta);
			/************************************************************************/

			//$aa=recalculaCostos($enlaceCon,$cod_material, $global_almacen);	
		}
	}
			
	/******************************************************/
	/*//! GENERACIÓN DE COMPROBANTE EN FINANCIERO CLINICA */
	/******************************************************/
	// $queryMaestro = "SELECT i.nro_correlativo, 
	// 						i.cod_ingreso_almacen, 
	// 						i.nro_factura_proveedor, 
	// 						i.fecha, 
	// 						p.nombre_proveedor, 
	// 						i.descuento_adicional, 
	// 						i.descuento_adicional2, 
	// 						i.fecha_factura_proveedor, 
    //                         CAST(
    //                             ROUND(
    //                                 (SELECT SUM((cantidad_unitaria * precio_bruto) - 
    //                                 ROUND((cantidad_unitaria * precio_bruto) * (descuento_unitario/100), 2))
    //                                 FROM ingreso_detalle_almacenes
    //                                 WHERE cod_ingreso_almacen = i.cod_ingreso_almacen) 
    //                                 - i.descuento_adicional 
    //                                 - i.descuento_adicional2, 
    //                             2) AS DECIMAL(10, 2)
    //                         ) AS monto_total, 
	// 						((SELECT ROUND(SUM((cantidad_unitaria * precio_bruto)), 2)
	// 						FROM ingreso_detalle_almacenes
	// 						WHERE cod_ingreso_almacen = i.cod_ingreso_almacen)) as monto_sin_descuento,
	// 						i.dias_credito, 
	// 						i.cod_proveedor,
	// 						p.razon_social, 
	// 						p.nit
	// 	FROM ingreso_almacenes i
	// 	LEFT JOIN proveedores p ON p.cod_proveedor = i.cod_proveedor
	// 	WHERE i.ingreso_anulado = 0 
	// 	AND i.contabilizado = 0 
	// 	AND i.cod_tipoingreso = 1000
	// 	AND i.cod_ingreso_almacen = '$codigo'";
	// $resultMaestro = mysqli_query($enlaceCon, $queryMaestro);
	// $data = [];

	// $total_final = 0;
	// while ($ingreso = mysqli_fetch_assoc($resultMaestro)) {
	// 	$total_final += $ingreso['monto_total'];

	// 	$codIngresoAlmacen = mysqli_real_escape_string($enlaceCon, $ingreso['cod_ingreso_almacen']);
	// 	$queryDetalle = "SELECT id.cod_material, m.codigo_anterior, m.descripcion_material, id.cantidad_unitaria, id.descuento_unitario, id.precio_bruto
	// 					 FROM ingreso_detalle_almacenes id
	// 					 LEFT JOIN material_apoyo m ON m.codigo_material = id.cod_material
	// 					 WHERE id.cod_ingreso_almacen = '$codIngresoAlmacen'
	// 					 ORDER BY id.orden ASC";
	// 	$resultDetalle = mysqli_query($enlaceCon, $queryDetalle);
	// 	$detalles = [];
	// 	while ($detalle = mysqli_fetch_assoc($resultDetalle)) {
	// 		$detalles[] = $detalle;
	// 	}
	// 	$ingreso['detalles'] = $detalles;
	// 	$data[] = $ingreso;
	
	// 	mysqli_free_result($resultDetalle);
	// }
	// mysqli_free_result($resultMaestro);
	
	// $conf_monto = obtenerValorConfiguracion($enlaceCon,'-6');
	
	// // ! Valida Monto para Generar Comprobante
	// if($total_final > $conf_monto){
	// 	// GENERA COMPROBANTE
	// 	$url_financiero = obtenerValorConfiguracion($enlaceCon,'-5');
	// 	$json_url = $url_financiero . '/comprobantes/saveComprobanteIngresoFarmacia.php';
	// 	// echo $json_url;
	// 	$ch = curl_init($json_url);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_POST, true);
	// 	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
	// 										'seleccionados' => $data,
	// 										'gestion'		=> date('Y'),
	// 										'mes'			=> date('m'),
	// 									]));
	// 	$response = curl_exec($ch);
	// 	//var_dump($response);
	// }
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	// exit;

	/******************************************************/
	
	// exit;
	
	//var_dump($arrayPreciosModificar);

	echo "<script language='Javascript'>
		alert('Los datos fueron insertados correctamente.');
		location.href='navegador_ingresomateriales.php';
		</script>";	
}else{
	echo "<script language='Javascript'>
		alert('EXISTIO UN ERROR EN LA TRANSACCION, POR FAVOR CONTACTE CON EL ADMINISTRADOR.');
		location.href='navegador_ingresomateriales.php';
		</script>";
}

?>