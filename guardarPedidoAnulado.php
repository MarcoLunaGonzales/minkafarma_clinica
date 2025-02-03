<?php
set_time_limit(0);

$start_time = microtime(true);
require("conexionmysqli.php");
require("estilos_almacenes.inc");
require("funciones.php");

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

$cod_pedido  = $_GET['cod_pedido'];

$sql_upd = "UPDATE pedidos SET estado = 2 WHERE codigo = '$cod_pedido'";
$sql_update = mysqli_query($enlaceCon,$sql_upd);

if(!$sql_update){
	echo "<script language='Javascript'>
			Swal.fire({
				title: 'Error!',
				html: 'La modificación del registro no pudo ser completada. Por favor, contacte al administrador para obtener asistencia adicional.',
				type: 'success'
			}).then(function() {
				location.href='registrar_salidapedidos.php'; 
			});
		</script>";
	exit;
}else{
	echo "<script language='Javascript'>
		Swal.fire({
		title: 'Exito!',
		html: 'Se anuló el Pedido correctamente.',
		type: 'success'
		}).then(function() {
		   location.href='navegadorPedidos.php'; 
		});
		</script>";
}

?>