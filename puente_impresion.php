<html>

<?php
require_once "conexionmysqlipdf.inc";
require_once "funciones.php";

error_reporting(E_ALL);
ini_set('display_errors', '1');

$codVenta=$_GET["codVenta"];
$tipoDoc=$_GET["tipodoc"];



/*SACAMOS EL TIPO DE IMPRESION PDF O HTML*/
$tipoImpresion=obtenerValorConfiguracion($enlaceCon,48);
$tipoVentaCaja=obtenerValorConfiguracion($enlaceCon,17);
// Tipo de Hoja
$formatoHoja = obtenerValorConfiguracion($enlaceCon,58);

$urlFactura="formatoFactura.php?codVenta=$codVenta";
$urlNR="formatoNotaRemision.php?codVenta=$codVenta";

if($tipoImpresion==1 && $formatoHoja==1){
	$urlFactura="formatoFacturaOnLine.php?codVenta=$codVenta";
	$urlNR="formatoNotaRemisionOnLine.php?codVenta=$codVenta";	
}

if($formatoHoja==2){
	$urlFactura="formatoFacturaOnLineHoja.php?codVenta=$codVenta";
	$urlNR="formatoNotaRemisionHoja.php?codVenta=$codVenta";
}

if($formatoHoja==3){
	$urlFactura="formatoFactura.php?codVenta=$codVenta";
	$urlNotaFiscal="formatoFacturaNotaFiscal.php?codVenta=$codVenta";
	$urlNR="formatoNotaRemision.php?codVenta=$codVenta";
}


/**************  Si es 0 Vemos el PDF -> 1 HTML **********************/
if($tipoImpresion==0 && $tipoVentaCaja==0){
	if($tipoDoc==1){
		$url=$urlFactura;
	}else{
		$url="formatoNotaRemision.php?codVenta=$codVenta";
	}

	if($formatoHoja==1){
?>
	<script>
		window.open('<?=$url;?>','newwindow');
		window.open('registrar_salidaventas.php','_self');
	</script>
<?php
	}
	if($formatoHoja==3){
?>
	<script>
		window.open('<?=$url;?>','newwindow');
		window.open('<?=$urlNotaFiscal;?>','newwindow2');
		window.open('registrar_salidaventas.php','_self');
	</script>
<?php	
	}
}elseif($tipoImpresion==1 && $tipoVentaCaja==0) {
	if($tipoDoc==1){
		$url=$urlFactura;
	}else{
		$url=$urlNR;
	}
	?>
	<script>
		location.href='<?=$url;?>';
	</script>
	<?php
}elseif ($tipoVentaCaja==1) {
	?>
	<script>
		location.href="formatoTicketVentaCaja.php?codVenta=<?=$codVenta;?>";
	</script>
	<?php
}

?>
</html>