<?php 
    require_once("../conexionmysqli.inc");
    require_once("../estilos_almacenes.inc");
    require_once("../siat_folder/funciones_siat.php");
    require_once("../enviar_correo/php/send-email_anulacion.php");

 error_reporting(E_ALL);
 ini_set('display_errors', '1');  

$codSucursal=0;
$codigoSucursal=0;
$codigoPuntoVenta=2;
$cod_entidad=1;

$sql="SELECT s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
(58021,58050,58153,58243,58271,58338,58485,58752,58756,58779,58867)";

echo $sql."<br><br>";

$resp=mysqli_query($enlaceCon, $sql);
while($dat=mysqli_fetch_array($resp)){
  echo "entra";
  
  $codVenta=$dat[0];
  $cuis=$dat[1];
  $cufd=$dat[2];
  $cuf=$dat[3];
  
  echo $codVenta." ".$cuis." ".$cufd." ".$cuf."<br>";
  $respEvento=anulacionFactura_siat($codigoPuntoVenta,$codigoSucursal,$cuis,$cufd,$cuf);
  
  //echo implode(" ",$respEvento);  
  
  $mensaje=$respEvento[1];
  if($respEvento[0]==1){
    echo "anulado ".$codVenta." ".$mensaje;
  }

}
