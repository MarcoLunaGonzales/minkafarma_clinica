<?php
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli2.inc');
require('funcion_nombres.php');
require('funciones.php');


$sqlUTF=mysqli_query($enlaceCon, "SET NAMES utf8");

$fecha_ini=$_POST['fecha_ini'];
$fecha_fin=$_POST['fecha_fin'];
$rpt_ver=$_POST['rpt_ver'];
$rpt_distribuidor=$_POST['rpt_distribuidor'];

$rpt_distribuidor=implode(",",$rpt_distribuidor);

//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;


$rpt_territorio=$_POST['rpt_territorio'];

$fecha_reporte=date("d/m/Y");

$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);

$cod_almacen=0;
$sql_almacen="select cod_almacen from almacenes where cod_ciudad='$rpt_territorio'";
$resp_almacen=mysqli_query($enlaceCon, $sql_almacen);
if($dat_almacen=mysqli_fetch_array($resp_almacen)){
	$cod_almacen=$dat_almacen[0];
}


echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Productos a Reponer
	<br>Territorio: $nombre_territorio <br> De: $fecha_ini A: $fecha_fin
	<br>Fecha Reporte: $fecha_reporte</tr></table>";
	
$sql="select m.`codigo_material`, m.`descripcion_material`, p.nombre_proveedor as linea, m.codigo_barras, 
	(sum(sd.monto_unitario)-sum(sd.descuento_unitario))montoVenta, sum(sd.cantidad_unitaria), s.descuento, s.monto_total, max(s.fecha) as ultimaventa
	from `salida_almacenes` s
	INNER JOIN `salida_detalle_almacenes` sd ON s.`cod_salida_almacenes`=sd.`cod_salida_almacen`
	INNER JOIN `material_apoyo` m ON sd.`cod_material`=m.`codigo_material`
	LEFT JOIN proveedores_lineas pl ON pl.cod_linea_proveedor=m.cod_linea_proveedor
	LEFT JOIN proveedores p ON p.cod_proveedor=pl.cod_proveedor
	where s.`fecha` BETWEEN '$fecha_iniconsulta' and '$fecha_finconsulta'
	and s.`salida_anulada`=0 and s.`cod_tiposalida`=1001 and  
	s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a where a.`cod_ciudad`='$rpt_territorio')
	and p.cod_proveedor in ($rpt_distribuidor)
	group by linea, m.`codigo_material` order by linea, m.descripcion_material";
	
	//echo $sql;
$resp=mysqli_query($enlaceCon, $sql);

echo "<br><table align='center' class='texto' width='100%'>
<tr>
<th>BarCode</th>
<th>Codigo</th>
<th>Linea</th>
<th>Producto</th>
<th>Cantidad</th>
<th>Monto Venta</th>
<th>Fecha Ultima Venta</th>
<th>Stock Actual</th>
<th>Observaciones</th>
</tr>";

$totalVenta=0;
while($datos=mysqli_fetch_array($resp)){	
	$codItem=$datos[0];
	$nombreItem=$datos[1];
	$nombreMarca=$datos[2];
	$barCode=$datos[3];
	
	$montoVenta=$datos[4];
	$cantidad=$datos[5];

	$descuentoVenta=$datos[6];
	$montoNota=$datos[7];

	$fechaUltimaVenta=$datos[8];
	
	if($descuentoVenta>0){
		$porcentajeVentaProd=($montoVenta/$montoNota);
		$descuentoAdiProducto=($descuentoVenta*$porcentajeVentaProd);
		$montoVenta=$montoVenta-$descuentoAdiProducto;
	}

	//$stockFechaUltimaVenta=stockProductoAFecha($enlaceCon, $cod_almacen, $codItem, $fechaUltimaVenta);

	//el Stock debe ser al ultimo momento para evaluar la necesidad de comprar
	$stockFechaUltimaVenta=stockProducto($enlaceCon, $cod_almacen, $codItem);

	$obsStock="";
	if($stockFechaUltimaVenta<=0){
		$stockFechaUltimaVenta=0;
		$obsStock="<span class='textomedianorojo'>El Producto debe Reponerse!</span>";
	}
	
	$montoPtr=number_format($montoVenta,2,".",",");
	$cantidadFormat=number_format($cantidad,0,".",",");
	
	if( ($rpt_ver==0 && $stockFechaUltimaVenta==0) || $rpt_ver==1 ){

		$totalVenta=$totalVenta+$montoVenta;

		echo "<tr>
		<td>$barCode</td>
		<td>$codItem</td>
		<td>$nombreMarca</td>
		<td>$nombreItem</td>
		<td align='center'>$cantidadFormat</td>
		<td align='right'>$montoPtr</td>
		<td align='center'>$fechaUltimaVenta</td>
		<td align='center'>$stockFechaUltimaVenta</td>
		<td>$obsStock</td>	
		</tr>";		
	}
}
$totalPtr=number_format($totalVenta,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Total:</td>
	<td>$totalPtr</td>
<tr>";

echo "</table>";
include("imprimirInc.php");
?>