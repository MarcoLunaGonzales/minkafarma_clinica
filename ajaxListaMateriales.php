<html>
<body>
<?php
session_start();

require("conexionmysqli2.inc");
require("funciones.php");


error_reporting(E_ALL);
ini_set('display_errors', '1');

$provienePedidos=0;
if(isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    $recurso = 'registrar_salidapedidos.php';
    if(strpos($referer, $recurso) !== false) {
        $provienePedidos=1;
    } else {
        $provienePedidos=0;
    }
}

//echo "PROVIENE PEDIDOS: ".$provienePedidos;

// Cargo 1:Admin, 0:Nomal
$global_admin_cargo = $_COOKIE["global_admin_cargo"];

$codigoMat=0;
$nomAccion="";
$nomPrincipio="";

$codigoCiudadGlobal=$_COOKIE["global_agencia"];


if(isset($_GET['codigoMat'])){
	$codigoMat=$_GET['codigoMat'];
}
if(isset($_GET['nomAccion'])){
	$nomAccion=$_GET['nomAccion'];
}
if(isset($_GET['nomPrincipio'])){
	$nomPrincipio=$_GET['nomPrincipio'];	
}
$codTipo=$_GET['codTipo'];
$nombreItem=$_GET['nombreItem'];
$globalAlmacen=$_COOKIE['global_almacen'];
$itemsNoUtilizar=$_GET['arrayItemsUtilizados'];

if($itemsNoUtilizar==""){
	$itemsNoUtilizar=0;
}

$tipoSalida=$_GET['tipoSalida'];

$fechaActual=date("Y-m-d");

$indexFila=0;
?>
<table align='center' class="texto">
<tr>
<th><input type='checkbox' id='selecTodo'  onchange="marcarDesmarcar(form1,this)" ></th><th>Codigo</th><th style='background-color: $colorFV; text-align: center;'>FV</th>
	<th>Producto</th>
	<th>Linea</th>
	<th>Principio Activo</th>
	<th>Accion Terapeutica</th>
	<th>Stock</th>
	<?php
	$sqlOtrosAlmacenes="SELECT a.cod_almacen, a.nombre_almacen from almacenes a where a.cod_almacen<>$globalAlmacen and a.vista_stock=1";
	$respOtrosAlmacenes=mysqli_query($enlaceCon, $sqlOtrosAlmacenes);
	while($datOtrosAlmacenes=mysqli_fetch_array($respOtrosAlmacenes)){
		$codOtroAlmacen=$datOtrosAlmacenes[0];
		$nombreOtroAlmacen=$datOtrosAlmacenes[1];
		echo "<th>Stock<br>$nombreOtroAlmacen</th>";
	}
	?>
	<th>Precio</th>
</tr>
<?php
//SACAMOS LA CONFIGURACION PARA LA SALIDA POR VENCIMIENTO
$tipoSalidaVencimiento=obtenerValorConfiguracion($enlaceCon,5);
//Bandera para mostrar la Fecha de Vencimiento en la Factura o no
$banderaMostrarFV=obtenerValorConfiguracion($enlaceCon,20);
//Bandera para buscar desde el nombre de producto tambien el principio activo
$banderaBuscarPA=obtenerValorConfiguracion($enlaceCon,22);
//Bandera para mostrar el Codigo con Costo de Compra
$banderaCodigoCostoCompra=obtenerValorConfiguracion($enlaceCon,26);
//Bandera para mostrar 1 Decimal o 2 Decimales en el precio
$bandera1DecimalPrecioVenta=obtenerValorConfiguracion($enlaceCon,27);
// Obtenemos control de fecha
$numeroMesesControlVencimiento = obtenerValorConfiguracion($enlaceCon, 28);

$sql="SELECT m.codigo_material, m.descripcion_material, CONCAT(p.nombre_proveedor,'-',pl.nombre_linea_proveedor)AS nombre_proveedor, 
m.principio_activo, m.accion_terapeutica, m.bandera_venta_unidades, m.cantidad_presentacion, m.producto_controlado, m.codigo_anterior, m.precio_abierto
FROM material_apoyo m 
LEFT JOIN proveedores_lineas pl ON pl.cod_linea_proveedor=m.cod_linea_proveedor
LEFT JOIN proveedores p ON p.cod_proveedor=pl.cod_proveedor
where m.estado=1 and m.codigo_material not in ($itemsNoUtilizar)";
if($codigoMat!=""){
	$sql=$sql. " and (m.codigo_material='$codigoMat' or m.codigo_anterior like '%$codigoMat%') ";
}
if($nombreItem!="" && $banderaBuscarPA==1){
	$sql=$sql. " and (m.descripcion_material like '%$nombreItem%' or m.principio_activo like '%$nombreItem%')";
}elseif($nombreItem!="" && $banderaBuscarPA!=1){
	$sql=$sql. " and m.descripcion_material like '%$nombreItem%'";
}
/*if($tipoSalidaVencimiento==$tipoSalida){
	$sql=$sql. " and m.codigo_material in (select id.cod_material from ingreso_almacenes i, ingreso_detalle_almacenes id 
	where i.cod_ingreso_almacen=id.cod_ingreso_almacen and i.cod_almacen='$globalAlmacen' and i.ingreso_anulado=0 
	and id.fecha_vencimiento<'$fechaActual') ";
}*/
if((int)$codTipo>0){
	if(isset($_GET["codProv"])){
      $sql=$sql." and m.cod_linea_proveedor in (SELECT cod_linea_proveedor from proveedores_lineas where cod_proveedor=".$_GET["codProv"].")";
	}else{
	  $sql=$sql." and m.cod_linea_proveedor=".$codTipo."";	
	}        
}
if($nomAccion!=""){
	$sql=$sql. " and m.accion_terapeutica like '%$nomAccion%'";
}
if($nomPrincipio!=""){
	$sql=$sql. " and m.principio_activo like '%$nomPrincipio%'";
}
$sql=$sql." order by 2";

$sql=trim($sql);

//echo $sql;
	
	$resp=mysqli_query($enlaceCon,$sql);

	$numFilas=mysqli_num_rows($resp);
	if($numFilas>0){
		$cont=0;
		while($dat=mysqli_fetch_array($resp)){
			$codigo=$dat[0];
			$nombre=$dat[1];
			$linea=$dat[2];
			$principioActivo=$dat[3];
			$accionTerapeutica=$dat[4];
			$ventaSoloCajas=$dat[5];
			$cantidadPresentacion=$dat[6];
			
			$nombre = str_replace(",", "", $nombre);
			$linea = str_replace(",", "", $linea);

			$nombre=addslashes($nombre);
			$linea=addslashes($linea);
			
			
			$stockProducto=0;

			// Codigo de Producto Controlado
			$producto_controlado = $dat['producto_controlado'];

			$codigoAlterno = $dat['codigo_anterior'];
			/*if($tipoSalida==$tipoSalidaVencimiento){
				$stockProducto=stockProductoVencido($enlaceCon,$globalAlmacen, $codigo);
			}else{
				$stockProducto=stockProducto($enlaceCon,$globalAlmacen, $codigo);
			}*/
			$stockProducto=stockProducto($enlaceCon,$globalAlmacen, $codigo);

			// Stock Producto COLOR
			$stockColor = ($stockProducto <= $cantidadPresentacion) ? 'yellow' : 'transparent';
								
			$datosProd=$codigo."|".$nombre."|".$linea."|".$stockProducto."|".$stockColor;
		

			$consulta="select p.`precio` from precios p where p.`codigo_material`='$codigo' and p.`cod_precio`='1' and 
					cod_ciudad='$codigoCiudadGlobal'";
					
			$rs=mysqli_query($enlaceCon,$consulta);
			$registro=mysqli_fetch_array($rs);
			$precioProducto=empty($registro[0]) ? 0 : $registro[0];
			if($precioProducto=="")
			{   $precioProducto=0;
			}
			if($bandera1DecimalPrecioVenta==1){
				$precioProducto=round($precioProducto,1);
			}else{
				$precioProducto=redondear2($precioProducto);
			}
			$mostrarFila=1;
			if(isset($_GET["stock"])){
				 if($_GET["stock"]==1&&$stockProducto<=0){
                    $mostrarFila=0;
				 }  	              
			}

			/*Mostrar la Fecha de Vencimiento*/
			$colorFV = 'white';
			$txtFechaVencimiento="-";
			if($banderaMostrarFV==1){
				$txtFechaVencimiento=obtenerFechaVencimiento($enlaceCon, $globalAlmacen, $codigo);
				//$txtFechaVencimiento="<span class='textogranderojo'><small>$txtFechaVencimiento</small></span>";
				$txtFechaVencimiento="<small><b>$txtFechaVencimiento</b></small>";
			}
			/*Fin Fecha de Vencimiento*/
			
			/* Se obtiene la diferencia de meses con la fecha actual */
			$fechaVencimiento = obtenerFechaVencimiento($enlaceCon, $globalAlmacen, $codigo);
			
			if($fechaVencimiento!=""){
				list($mes, $anio) = explode("/", $fechaVencimiento);
				$hoy = date('m/Y');
				list($mesHoy, $anioHoy) = explode("/", $hoy);
				$mesesDiferencia = (($anio - $anioHoy) * 12) + ($mes - $mesHoy);

				$controlVencimientoArray 	   = json_decode($numeroMesesControlVencimiento, true);
				usort($controlVencimientoArray, function($a, $b) {
					return $a['meses'] <=> $b['meses'];
				});
				$colorFV = '';
				foreach ($controlVencimientoArray as $item) {
					if ($mesesDiferencia <= $item['meses']) {
						$colorFV = $item['color'];
						break;
					} else {
						$colorFV = 'white';
					}
				}				
			}

			/* Fin diferencia de fecha */

			/**  Codigo Costo Compra***/
			$txtCodigoCostoCompra="";
			$txtCodigoCostoCompra1="";
			$txtCodigoCostoCompra2="";
			$costoAlmacen1=0;
			$costoAlmacen2=0;
			if($banderaCodigoCostoCompra==1){
				//PRIMER COSTO PARA MOSTRAR TIPO CONSALUD
				$sqlCostoCompra="SELECT concat(FORMAT(id.costo_almacen,1),'0',FORMAT((id.costo_almacen*1.25),1)), id.costo_almacen from ingreso_almacenes i, ingreso_detalle_almacenes id where i.cod_ingreso_almacen=id.cod_ingreso_almacen and 
					i.ingreso_anulado=0 and i.cod_tipoingreso in (999,1000) and id.cod_material='$codigo' order by i.cod_ingreso_almacen desc limit 0,1";
				$respCostoCompra=mysqli_query($enlaceCon,$sqlCostoCompra);
				if($datCostoCompra=mysqli_fetch_array($respCostoCompra)){
					$txtCodigoCostoCompra1=$datCostoCompra[0];
					$costoAlmacen1=$datCostoCompra[1];
				}
				//$txtCodigoCostoCompra=str_replace(".", "", $txtCodigoCostoCompra);

				//SEGUNDO COSTO PARA MOSTRAR TIPO CONSALUD
				
				$sqlPrecioCosto="SELECT CONCAT(format(((p.precio*100)/125),1),'00',format(p.precio,1)) as codigocosto, p.precio from precios p where p.cod_ciudad='$codigoCiudadGlobal' and p.cod_precio=1 and p.codigo_material='$codigo'";
				$respPrecioCosto=mysqli_query($enlaceCon, $sqlPrecioCosto);
				if($datPrecioCosto=mysqli_fetch_array($respPrecioCosto)){
					$txtCodigoCostoCompra2=$datPrecioCosto[0];
					$precioProductoCosto=$datPrecioCosto[1];
					$costoAlmacen2=($precioProductoCosto*100)/125;
				}

				/*if($costoAlmacen1>$costoAlmacen2){
					$txtCodigoCostoCompra=$txtCodigoCostoCompra1;
				}else{
					$txtCodigoCostoCompra=$txtCodigoCostoCompra2;
				}*/
				$txtCodigoCostoCompra=$txtCodigoCostoCompra1."**".$txtCodigoCostoCompra2;

			}
			/*****Fin Bandera Costo Compra*****/
			if($mostrarFila==1){
				$indexFila++;
	
				if($global_admin_cargo==0 && $provienePedidos==1){
					$stockProducto=0;
				}

				if($stockProducto>0){
					$stockProductoFormat="<b class='textograndenegro' style='color:#C70039'>".$stockProducto."</b>";
			  	}else{
			  		$stockProductoFormat=$stockProducto;
			  	}
				echo "<tr><td><input type='checkbox' id='idchk$cont' name='idchk$cont' value='$datosProd' onchange='ver(this)' ></td>
					<td>$codigo - $codigoAlterno</td>
					<td style='background-color: $colorFV; text-align: center;'>
						<b>$fechaVencimiento</b>
					</td>
					<td><div class='textograndenegro'><a href='javascript:setMateriales(form1, $codigo, \"$nombre - $linea ($codigo)-$txtCodigoCostoCompra ####$txtFechaVencimiento####$cantidadPresentacion####$ventaSoloCajas####$precioProducto####$producto_controlado\",$stockProducto)'>$nombre</a></div></td>
				<td>$linea</td>
				<td><small>$principioActivo</small></td>
				<td><small>$accionTerapeutica</small></td>";
				echo "<td style='background-color: $stockColor;'>$stockProductoFormat</td>";
				
				$sqlOtrosAlmacenes="SELECT a.cod_almacen, a.nombre_almacen from almacenes a where a.cod_almacen<>$globalAlmacen and a.vista_stock=1";
				$respOtrosAlmacenes=mysqli_query($enlaceCon, $sqlOtrosAlmacenes);
				while($datOtrosAlmacenes=mysqli_fetch_array($respOtrosAlmacenes)){
					$codOtroAlmacen=$datOtrosAlmacenes[0];
					$nombreOtroAlmacen=$datOtrosAlmacenes[1];
					$stockOtroAlmacen=stockProducto($enlaceCon,$codOtroAlmacen,$codigo);
					if($stockOtroAlmacen>0){
						$stockOtroAlmacenFormat="<b class='textomedianonegro' style='color:blue'>".$stockOtroAlmacen."</b>";
					}else{
						$stockOtroAlmacenFormat="-";
					}
					echo "<th>$stockOtroAlmacenFormat</th>";
				}
				
				echo "<td>$precioProducto</td>
				</tr>";
				$cont++;
			}
		}
	}else{
		echo "<tr><td colspan='3'>Sin Resultados en la busqueda.</td></tr>";
	}
	
?>
</table>

</body>
</html>