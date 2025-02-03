<?php
require("conexionmysqli.php");
require("funciones.php");

// $sql="update ingreso_almacenes set ingreso_anulado=1 where cod_ingreso_almacen='$codigo_registro'";
// $resp=mysqli_query($enlaceCon,$sql);

/************************************************/
$sql_select = "SELECT cod_ingreso_almacen, cod_comprobante FROM ingreso_almacenes WHERE cod_ingreso_almacen='$codigo_registro'";
$result = mysqli_query($enlaceCon, $sql_select);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $cod_ingreso_almacen = $row['cod_ingreso_almacen'];
    $cod_comprobante 	 = $row['cod_comprobante'];
	
    $sql_update = "UPDATE ingreso_almacenes SET ingreso_anulado=1 WHERE cod_ingreso_almacen='$cod_ingreso_almacen'";
    $resp_update = mysqli_query($enlaceCon, $sql_update);

    if ($resp_update) {
		
		// GENERA COMPROBANTE
		$url_financiero = obtenerValorConfiguracion($enlaceCon,'-5');
		$json_url = $url_financiero . '/comprobantes/saveComprobanteIngresoFarmaciaAnulado.php';
		// echo $json_url;
		$ch = curl_init($json_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
											'cod_comprobante' => $cod_comprobante,
										]));
		$response = curl_exec($ch);
		if ($response ===false) {
			echo "Error en CURL: " . curl_error($ch);
		} else {
			echo "<pre>";
			print_r(json_decode($response, true)); // Intenta decodificar si es JSON
			echo "</pre>";
		}

        echo "Ingreso Anulado exitosamente.";
    } else {
        echo "Error al actualizar el ingreso: " . mysqli_error($enlaceCon);
    }
}
/************************************************/


//SACAMOS LA VARIABLE PARA ENVIAR EL CORREO O NO SI ES 1 ENVIAMOS CORREO DESPUES DE LA TRANSACCION
//$banderaCorreo=obtenerValorConfiguracion(8);

$banderaCorreo=0;

if($banderaCorreo==1){
	header("location:sendEmailAnulacionIngreso.php?codigo=$codigo_registro");
}
else{
	echo "<script language='Javascript'>
			alert('El registro fue anulado.');
			location.href='navegador_ingresomateriales.php';			
			</script>";	
}


?>