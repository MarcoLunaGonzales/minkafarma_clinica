<?php

require("../../conexionmysqli2.inc");

$nomCli = $_GET["nomcli"];
$propietario = $_GET["propietario"];
$nit = $_GET["nit"];
$dir = $_GET["dir"];
$tel1 = $_GET["tel1"];
$mail = $_GET["mail"];
$area = $_GET["area"];
$fact = $_GET["fact"];
$edad = $_GET["edad"];
$genero = $_GET["genero"];
$ci = $_GET['ci'];
$codigoCliente = $_GET['cod_cliente']; // Obtener el código del cliente a actualizar

$nomCli = str_replace("'", "''", $nomCli);
$apCli = str_replace("'", "''", $apCli);
$nit = str_replace("'", "''", $nit);
$dir = str_replace("'", "''", $dir);
$tel1 = str_replace("'", "''", $tel1);
$mail = str_replace("'", "''", $mail);
$area = $area;
$fact = str_replace("'", "''", $fact);

$consulta = "
UPDATE clientes 
SET 
    nombre_cliente = '$nomCli',
    nombre_propietario = '$propietario', 
    nit_cliente = '$nit',
    dir_cliente = '$dir',
    telf1_cliente = '$tel1',
    email_cliente = '$mail',
    cod_area_empresa = $area,
    nombre_factura = '$fact',
    cod_tipo_precio = '0',
    cod_tipo_edad = '$edad',
    ci_cliente = '$ci',
    cod_genero = '$genero'
WHERE
    cod_cliente = '$codigoCliente'
";
ob_clean();
if(isset($_GET["dv"])){
    $resp = mysqli_query($enlaceCon,$consulta);
    if($resp) {
      echo "#####".$codigoCliente;
    } else {
      echo "#####0";
    }
}else{
    $resp = mysqli_query($enlaceCon, $consulta);

    if ($resp) {
        echo "<script type='text/javascript' language='javascript'>alert('Los datos del cliente se han actualizado correctamente.'); listadoClientes();</script>";
    } else {
        echo "<script type='text/javascript' language='javascript'>alert('Error al actualizar los datos del cliente');</script>";
    }
}
?>
