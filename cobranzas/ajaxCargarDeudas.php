<?php
require("../conexionmysqli.inc");
require("../funciones.php");

$codCliente=$_GET['codCliente'];

$globalAlmacen=$_COOKIE['global_almacen'];


$sql="SELECT s.`cod_salida_almacenes`, s.`nro_correlativo`, 
	(select td.`nombre` from `tipos_docs` td where td.`codigo`=s.cod_tipo_doc),
	s.`fecha`, s.`monto_final`, s.`monto_cancelado`
	from `salida_almacenes` s where s.`cod_cliente`='$codCliente' and s.`salida_anulada`=0 and 
	s.`monto_final`>s.`monto_cancelado` and  s.cod_almacen='$globalAlmacen' and s.cod_tiposalida=1001 and s.cod_tipopago=4 order by s.`fecha`;";
//echo $sql;
$resp=mysqli_query($enlaceCon, $sql);
$numFilas=mysqli_num_rows($resp);

echo "<input type='hidden' name='nroFilas' id='nroFilas' value='$numFilas'>";

$i=1;
$totalSaldoCabVentas=0;
while($dat=mysqli_fetch_array($resp)){
	$codigo=$dat[0];
	$numero=$dat[1];
	$nombreDoc=$dat[2];
	$fecha=$dat[3];
	$monto=$dat[4];
	$montoCancelado=$dat[5];
	$saldo=$monto-$montoCancelado;
	
	$montoV=redondear2($monto);
	$montoCanceladoV=redondear2($montoCancelado);
	$saldoV=redondear2($saldo);
	
	$selectTipoPago = '';
	$sqlTipoPago  = "SELECT cod_tipopago, nombre_tipopago from tipos_pago where cod_tipopago < 4 order by 1";
	$respTipoPago = mysqli_query($enlaceCon,$sqlTipoPago);
	$selectTipoPago .= "<select name='tipoPago$i' class='selectpicker form-control' id='tipoPago$i' data-style='btn-info'>";
	while($dat=mysqli_fetch_array($respTipoPago)){
		$codigoTipoPago = $dat[0];
		$nombreTipoPago = $dat[1];
		$selectTipoPago .= "<option value='$codigoTipoPago'>$nombreTipoPago</option>";
	}
	$selectTipoPago .= "</select>";

	/*Acumulamos en esta variable $totalSaldoCabVentas para validacion del saldo por cliente*/
	$totalSaldoCabVentas += $saldo;
	
	echo "<tr>
		<input type='hidden' value='$codigo' name='codCobro$i' id='codCobro$i'>
		<td>$nombreDoc</td>
		<td>$numero</td>
		<td>$fecha</td>
		<td>$montoV</td>
		<td>$montoCanceladoV</td>
		<td>$saldoV</td>
		<input type='hidden' value='$saldo' name='saldo$i' id='saldo$i'>
		<td>$selectTipoPago</td>
		<td align='center'><input type='number' class='texto' name='montoPago$i' id='montoPago$i' size='10' onKeyPress='javascript:return solonumeros(event)' value='0' max='$saldo' step='any'></td>
		<td align='center'><input type='text' class='texto' name='nroDoc$i' id='nroDoc$i' size='10' onKeyPress='javascript:return solonumeros(event)' value='0'></td>
		</tr>";
	$i++;
}

$montoSaldoVerificacion=obtenerMontoCuentaCobrarCliente($codCliente, $globalAlmacen);

echo "REVISION****** ".$montoSaldoVerificacion." ".$totalSaldoCabVentas;

?>