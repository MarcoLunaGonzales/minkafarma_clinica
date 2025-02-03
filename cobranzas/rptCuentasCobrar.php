
<?php
require('../function_formatofecha.php');
require('../conexionmysqli.inc');
require('../funcion_nombres.php');
require('../funciones.php');
require("../estilos_almacenes.inc");

error_reporting(E_ALL);
ini_set('display_errors', '1');


$fecha_fin=$_GET['fecha_fin'];
$fecha_ini=$_GET['fecha_ini'];

$globalAlmacen=$_COOKIE["global_almacen"];


//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;

$rpt_territorio=$_GET['rpt_territorio'];

$fecha_reporte=date("d/m/Y");

$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);


$configTipoPago = obtenerValorConfiguracion($enlaceCon, 59);


echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Reporte de Cuentas x Cobrar
	<br>Territorio: $nombre_territorio <br> De: $fecha_ini A: $fecha_fin
	<br>Fecha Reporte: $fecha_reporte</tr></table>";

$sql="SELECT s.`cod_salida_almacenes`, 
	CONCAT((select t.abreviatura from tipos_docs t where t.codigo=s.cod_tipo_doc),'-',s.nro_correlativo), 
	s.`fecha`, concat(c.`nombre_cliente`), s.`monto_final`,
       (
         select COALESCE(sum(cbd.monto_detalle), 0)
         from `cobros_cab` cb, `cobros_detalle` cbd
         where cb.cod_cobro=cbd.cod_cobro and cbd.cod_venta=s.`cod_salida_almacenes`
         and cb.cod_estado<>2 
       ) cobrado
from `salida_almacenes` s, clientes c where s.`monto_final` >
      (
        select COALESCE(sum(cbd.monto_detalle), 0)
         from `cobros_cab` cb, `cobros_detalle` cbd
         where cb.cod_cobro=cbd.cod_cobro and cbd.cod_venta=s.`cod_salida_almacenes`
         and cb.cod_estado<>2 
      ) and s.`cod_cliente` = c.`cod_cliente` and
      s.`salida_anulada` = 0 and s.cod_almacen='$globalAlmacen' and s.cod_tiposalida=1001 and 
      s.`fecha` between '$fecha_iniconsulta' and
      '$fecha_finconsulta' ";
if($configTipoPago == 1){
	$sql.=" AND s.cod_tipopago = 4 ";
} 
$sql.="order by c.nombre_cliente, s.fecha";	  


//echo $sql;



$resp=mysqli_query($enlaceCon, $sql);

echo "<br><table cellspacing='0' border=1 align='center' class='texto' width='100%'>
<tr>
<th>Documento</th>
<th>Fecha</th>
<th>Cliente</th>
<th>MontoVenta</th>
<th>A Cuenta</th>
<th>Saldo</th>
</tr>";

$totalxCobrar=0;
while($datos=mysqli_fetch_array($resp)){	
	$codSalida=$datos[0];
	$nroVenta=$datos[1];
	$fechaVenta=$datos[2];
	$nombreCliente=$datos[3];
	$montoVenta=$datos[4];
	$montoCobro=$datos[5];
	$montoxCobrar=$montoVenta-$montoCobro;
	
	
	$montoCobroF=formatonumeroDec($montoCobro);
	$montoxCobrarF=formatonumeroDec($montoxCobrar);
	$montoVentaF=formatonumeroDec($montoVenta);

	if($montoxCobrar>1){
		$totalxCobrar=$totalxCobrar+$montoxCobrar;
		echo "<tr>
		<td align='center'>$nroVenta</td>
		<td align='center'>$fechaVenta</td>
		<td>$nombreCliente</td>
		<td align='right'>$montoVentaF</td>
		<td align='right'>$montoCobroF</td>
		<td align='right'>$montoxCobrarF</td>
		</tr>";
	}
}
$totalxCobrarF=formatonumeroDec($totalxCobrar);
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>Total:</td>
	<td align='right'>$totalxCobrarF</td>
</tr>";

echo "</table>";
?>