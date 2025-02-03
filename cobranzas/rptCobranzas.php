<?php
require('../function_formatofecha.php');
require('../conexionmysqli.inc');
require('../funcion_nombres.php');
require('../funciones.php');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


$fecha_ini=$_GET['fecha_ini'];
$fecha_fin=$_GET['fecha_fin'];

$rpt_cliente=$_GET['rpt_cliente'];
$rpt_funcionario=$_GET['rpt_funcionario'];


//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;

$rpt_territorio=$_GET['rpt_territorio'];

$fecha_reporte=date("d/m/Y");

$nombre_territorio=nombreTerritorio($enlaceCon, $rpt_territorio);

echo "<table align='center' class='textotit' width='100%'><tr><td align='center'>Reporte Cobranzas
	<br>Territorio: $nombre_territorio <br> De: $fecha_ini A: $fecha_fin
	<br>Fecha Reporte: $fecha_reporte</tr></table>";

$sql="select c.`cod_cobro`, c.`fecha_cobro`, cd.`nro_doc`, concat(cl.`nombre_cliente`), 
	concat(t.abreviatura,'-',s.nro_correlativo), cd.`monto_detalle`, c.`observaciones`,
	(select concat(f.paterno, ' ',f.materno, ' ',f.nombres) from funcionarios f where f.codigo_funcionario=c.cod_funcionario)as funcionario,
	cd.observaciones as detalle
	from `cobros_cab` c, `cobros_detalle` cd, clientes cl, `salida_almacenes` s, tipos_docs t
	where c.`cod_cobro`=cd.`cod_cobro` and  t.codigo=s.cod_tipo_doc and c.`fecha_cobro` BETWEEN '$fecha_iniconsulta' and
    '$fecha_finconsulta' and cd.`cod_cliente`=cl.`cod_cliente` and cd.`cod_venta`=s.`cod_salida_almacenes` and c.cod_estado<>2";
if($rpt_cliente!=0){
	$sql=$sql." and cl.cod_cliente in ($rpt_cliente)";
}
if($rpt_funcionario!=0){
	$sql=$sql." and c.cod_funcionario in ($rpt_funcionario)";
}
$sql=$sql." order by 1";
$resp=mysqli_query($enlaceCon, $sql);

echo "<br><table cellspacing='0' border=1 align='center' class='texto' width='95%'>
<thead>
<tr>
<th width='7%'>Nro.Cob</th>
<th width='7%'>Doc.Cob</th>
<th width='15%'>Fecha</th>
<th width='15%'>Funcionario</th>
<th width='15%'>Cliente</th>
<th width='10%'>Nota Venta</th>
<th width='10%'>Observaciones</th>
<th width='10%'>Detalle</th>
<th width='10%'>Monto Cobranza</th>
</tr>
</thead>";

echo "<tbody>";
$totalCobro=0;
while($datos=mysqli_fetch_array($resp)){	
	$codCobro=$datos[0];
	$fecha=$datos[1];
	$nroCobro=$datos[2];
	$cliente=$datos[3];
	$nroVenta=$datos[4];
	$montoCobro=$datos[5];

	$totalCobro=$totalCobro+$montoCobro;

	$montoCobroF=formatonumeroDec($montoCobro);
	$obs=$datos[6];

	$nombreFuncionario=$datos[7];

	$obsDetalle=$datos[8];

	echo "
	<tr>
	<td align='center'>$codCobro</td>
	<td align='center'>$nroCobro</td>
	<td align='center'>$fecha</td>
	<td>$nombreFuncionario</td>
	<td>$cliente</td>
	<td align='center'>$nroVenta</td>
	<td>$obs</td>
	<td>$obsDetalle</td>
	<td align='right'>$montoCobroF</td>
	</tr>
	";
}
$totalCobroF=formatonumeroDec($totalCobro);
echo "</tbody>";
echo "<tfoot>
	<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><b>Total:</b></td>
	<td align='right'><b>$totalCobroF</b></td>
</tr></tfoot>";

echo "</table>";
?>