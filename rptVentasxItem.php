<?php
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli2.inc');
require('funcion_nombres.php');
require('funciones.php');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

$sqlUTF=mysqli_query($enlaceCon, "SET NAMES utf8");

$fecha_ini=$_POST['fecha_ini'];
$fecha_fin=$_POST['fecha_fin'];

$hora_ini=$_POST['exahorainicial'];
$hora_fin=$_POST['exahorafinal'];

$rpt_ordenar=$_POST['rpt_ordenar'];
$rpt_ver=$_POST['rpt_ver'];

$globalLogo=$_COOKIE["global_logo"];

$rpt_personal=$_POST['rpt_personal'];

$rptPersonalX=implode(",",$rpt_personal);

$nombrePersonalX=nombrePersonalMultiple($enlaceCon, $rptPersonalX);


//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;

$fecha_iniconsultahora=$fecha_iniconsulta." ".$hora_ini.":00";
$fecha_finconsultahora=$fecha_finconsulta." ".$hora_fin.":59";



$rpt_territorio=$_POST['rpt_territorio'];

$fecha_reporte=date("d/m/Y H:i:s");

$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);

echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Ranking de Ventas x Item
	<br>Territorio: $nombre_territorio <br> De: $fecha_iniconsultahora A: $fecha_finconsultahora
	<br>Fecha Reporte: $fecha_reporte
	<br>Personal: $nombrePersonalX</tr></table>";
	
$sql="select m.`codigo_material`, m.`descripcion_material`, (select p.nombre_proveedor from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor)as linea, 
	m.codigo_barras, 
	(sum(sd.monto_unitario)-sum(sd.descuento_unitario))montoVenta, sum(sd.cantidad_unitaria)cantidadventa, 
	sum(((sd.monto_unitario-sd.descuento_unitario)/s.monto_total)*s.descuento)as descuentocabecera, s.cod_almacen
	from `salida_almacenes` s, `salida_detalle_almacenes` sd, `material_apoyo` m 
	where s.`cod_salida_almacenes`=sd.`cod_salida_almacen` and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fecha_iniconsultahora' and '$fecha_finconsultahora' 
	and s.`salida_anulada`= 0 and sd.`cod_material`=m.`codigo_material` and s.`cod_tiposalida`=1001 and  
	s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a where a.`cod_ciudad`='$rpt_territorio') and 
	s.cod_chofer in ($rptPersonalX)
	group by m.`codigo_material`";
if($rpt_ordenar==0){
	$sql=$sql." order by m.descripcion_material ;";
}elseif($rpt_ordenar==1){
	$sql=$sql." order by montoVenta desc;";
}elseif($rpt_ordenar==2){
	$sql=$sql." order by cantidadventa desc;";
}elseif($rpt_ordenar==3){
	$sql=$sql." order by linea, m.descripcion_material";
}
//echo $sql;
$resp=mysqli_query($enlaceCon, $sql);

echo "<br><table align='center' class='texto' width='100%'>
<tr>
<th>Codigo</th>
<th>Linea</th>
<th>Producto</th>
<th>Cantidad</th>
<th>Monto Venta</th>";
if($rpt_ver==2){
	echo "<th>Stock</th>";
}
echo "</tr>";

$totalVenta=0;
while($datos=mysqli_fetch_array($resp)){	
	$codItem=$datos[0];
	$nombreItem=$datos[1];
	$nombreMarca=$datos[2];
	$barCode=$datos[3];


	
	$montoVenta=$datos[4];
	$cantidad=$datos[5];

	$descuentoCabecera=$datos[6];
	$montoVentaF=number_format($montoVenta,2,".",",");
	$montoVentaProducto=$montoVenta-$descuentoCabecera;	
	$montoVentaProductoF=number_format($montoVentaProducto,2,".",",");



	$codAlmacenVenta=$datos[7];
	

	$stockProducto=0;
	if($rpt_ver==2){
		$stockProducto=stockProducto($enlaceCon,$codAlmacenVenta,$codItem);
	}
	
	$cantidadFormat=number_format($cantidad,0,".",",");
	
	$totalVenta=$totalVenta+$montoVentaProducto;
	echo "<tr>
	<td>$codItem</td>
	<td>$nombreMarca</td>
	<td>$nombreItem</td>
	<td>$cantidadFormat</td>
	<td>$montoVentaProductoF</td>";
	if($rpt_ver==2){
		echo "<td align='right'>$stockProducto</td>";
	}	
	echo "</tr>";
}
$totalPtr=number_format($totalVenta,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Total:</td>
	<td>$totalPtr</td>
<tr>";

echo "</table>";
?>