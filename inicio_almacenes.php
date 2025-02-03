<style>
	.centrarimagen
	{
		position: absolute;
		top:50%;
		left:50%;
		width:560px;
		margin-left:-280px;
		height:370px;
		margin-top:-185px;
		padding:5px;
	}
</style>
<?php
	require_once('conexionmysqli2.inc');
	require_once('funciones.php');

error_reporting(E_ALL);
ini_set('display_errors', '1');

	$banderaInicio=obtenerValorConfiguracion($enlaceCon, 62);

	if($banderaInicio==2){
		echo "<script>
			location.href='rptProductosVencer.php';
		</script>";
	}else{
		echo "<div class='centrarimagen'>
			<img src='imagenes/team3.jpg' width='560px' heigth='370px'>
		</div>";
	}
?>