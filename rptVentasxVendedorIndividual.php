<?php
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli.php');
require('funcion_nombres.php');

$fecha_ini=$_GET['fecha_ini'];
$fecha_fin=$_GET['fecha_fin'];
$rpt_territorio=$_GET['rpt_territorio'];
$rpt_persona=$_GET['rpt_persona'];

//desde esta parte viene el reporte en si
$fecha_iniconsulta=($fecha_ini);
$fecha_finconsulta=($fecha_fin);

$fecha_reporte=date("d/m/Y");

$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);

echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Reporte Ventas x Vendedor Individual
	<br>Territorio: $nombre_territorio <br> De: $fecha_ini A: $fecha_fin
	<br>Fecha Reporte: $fecha_reporte</tr></table>";


$sql="select f.`codigo_funcionario`, concat(f.`paterno`,' ',f.`materno`,' ',f.`nombres`)as vendedor,
       (sum(sd.monto_unitario)-sum(sd.descuento_unitario)) montoVenta, sum(sd.cantidad_unitaria), sum(s.descuento), sum(s.monto_total)
from `salida_almacenes` s,
     `salida_detalle_almacenes` sd, `funcionarios` f
where s.`cod_salida_almacenes` = sd.`cod_salida_almacen` and
      s.`fecha` BETWEEN '$fecha_iniconsulta' and
      '$fecha_finconsulta'      and
      s.`salida_anulada` = 0 and
      s.`cod_almacen` in (
                           select a.`cod_almacen`
                           from `almacenes` a
                           where a.`cod_ciudad` = '$rpt_territorio'
      ) and 
      s.`cod_chofer`=f.`codigo_funcionario` and s.cod_chofer in ($rpt_persona) group by f.`codigo_funcionario`";		

     //echo $sql;
	  $resp=mysqli_query($enlaceCon, $sql);

echo "<br><table align='center' class='texto' width='70%'>
<tr>
<th>Codigo</th>
<th>Vendedor</th>
<th>Monto Venta</th>
</tr>";

$totalVenta=0;
while($datos=mysqli_fetch_array($resp)){	
	$codItem=$datos[0];
	$nombrePersona=$datos[1];
	$montoVenta=$datos[2];
	
	$cantidad=$datos[3];

	$descuentoVenta=$datos[4];
	$montoNota=$datos[5];
	
	/*if($descuentoVenta>0){
		//echo "entro descuento";
		$porcentajeVentaProd=($montoVenta/$montoNota);
		$descuentoAdiProducto=($descuentoVenta*$porcentajeVentaProd);
		$montoVenta=$montoVenta-$descuentoAdiProducto;
	}*/
	$montoVenta=$montoVenta-$descuentoVenta;
	
	$montoPtr=number_format($montoVenta,2,".",",");
	
	$totalVenta=$totalVenta+$montoVenta;
	echo "<tr>
	<td>$codItem</td>
	<td>$nombrePersona</td>
	<td align='right'>$montoPtr</td>
	</tr>";
}
$totalPtr=number_format($totalVenta,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>Total:</td>
	<td><b>$totalPtr</b></td>
<tr>";

echo "</table>";
include("imprimirInc.php");
?>