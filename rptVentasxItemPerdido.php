<?php
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli.inc');
require('funcion_nombres.php');
require('funciones.php');
set_time_limit(0);
$fecha_ini=$_GET['fecha_ini'];
$fecha_fin=$_GET['fecha_fin'];
if(!isset($_GET['rpt_ver'])){
  $rpt_ver=0;	
}else{
  $rpt_ver=$_GET['rpt_ver'];
}


//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;


$rpt_territorio=$_GET['rpt_territorio'];

$fecha_reporte=date("d/m/Y");
$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);

echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Reporte Ventas Perdidas x Item
	<br>Territorio: $nombre_territorio <br> De: $fecha_ini A: $fecha_fin
	<br>Fecha Reporte: $fecha_reporte</tr></table>";
	
$sql="SELECT m.`codigo_material`, m.`descripcion_material`, 
	sum(sd.monto_unitario)montoVenta, sum(sd.cantidad_unitaria),sum(sd.cantidad_unitaria*sd.monto_unitario),(SELECT nombre_linea_proveedor from proveedores_lineas where cod_linea_proveedor=m.cod_linea_proveedor) as linea,(SELECT nombre_proveedor from proveedores where cod_proveedor=(SELECT cod_proveedor from proveedores_lineas where cod_linea_proveedor=m.cod_linea_proveedor)) as proveedor,m.cod_linea_proveedor
	from `pedido_almacenes` s, `pedido_detalle_almacenes` sd, `material_apoyo` m 
	where s.`cod_salida_almacenes`=sd.`cod_salida_almacen` and s.`fecha` BETWEEN '$fecha_iniconsulta' and '$fecha_finconsulta'
	and s.`salida_anulada`=0 and sd.`cod_material`=m.`codigo_material` and
	s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a where a.`cod_ciudad`='$rpt_territorio')
	group by m.`codigo_material` order by 3 desc;";
$resp=mysqli_query($enlaceCon,$sql);

echo "<br><table align='center' class='texto' width='100%'>
<tr>
<th>Codigo</th>
<th>Proveedor</th>
<th>Linea</th>
<th>Item</th>
<th>Cantidad</th>
<th>Monto Venta</th>
</tr>";

$totalVenta=0;
while($datos=mysqli_fetch_array($resp)){	
	$codItem=$datos[0];
	$nombreItem=$datos[1];
	$montoVenta=$datos[4];//$datos[2]; el monto es la sumatoria del monto unitario
	$cantidad=$datos[3];
	$nombreLinea=$datos["linea"];
	$nombreProveedor=$datos["proveedor"];
	$montoPtr=number_format($montoVenta,2,".",",");
	$cantidadFormat=number_format($cantidad,0,".",",");
	
	$totalVenta=$totalVenta+$montoVenta;
	echo "<tr>
	<td>$codItem</td>
	<td>$nombreProveedor</td>
	<td>$nombreLinea</td>
	<td>$nombreItem</td>
	<td>$cantidadFormat</td>
	<td>$montoPtr</td>
	
	</tr>";
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