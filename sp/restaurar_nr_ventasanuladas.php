<?php 
    require_once("../conexionmysqli.inc");
    require_once("../estilos_almacenes.inc");
    require_once("../siat_folder/funciones_siat.php");
    require_once("../enviar_correo/php/send-email_anulacion.php");

 error_reporting(E_ALL);
 ini_set('display_errors', '1');  

/*FACTURAS ENERO 2024*/
// $mesTransaccion=1;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (43096,43143,43329,43392,43520,43575,43598,43644,43718,43727,43871,44005,44042,44058,44099,44134,44233)";
/*FIN ENERO 2024*/


/*FACTURAS FEBRERO 2024*/
// $mesTransaccion=2;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (44511,44647,44661,44715,44786,44837,44951,45029,45072,45247,45331,45342,45343,45480,45481) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
/*FIN FEBRERO 2024*/


// /*FACTURAS MARZO 2024*/
// $mesTransaccion=3;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (45799,45891,45943,46264,46354,46379,46390,46480) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN MARZO 2024*/

// /*FACTURAS ABRIL 2024*/
// $mesTransaccion=4;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (46585,46638,46647,46654,46674,46744,46791,46822,46952,46955,47175,47240,47300,47306,47310,47352,47415,47501,47582,47680,47809,47841) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN ABRIL 2024*/


// /*FACTURAS MAYO 2024*/
// $mesTransaccion=5;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (47987,48031,48052,48081,48187,48203,48244,48254,48308,48445,48471,48561,48563,48566,48571,48596,48676,48708,48743,48814,48836,48899,48901,48935,48956,48968,49035,49070,49150) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN MAYO 2024*/


// /*FACTURAS JUNIO 2024*/
// $mesTransaccion=6;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (49302,49317,49339,49492,49534,49555,49756,49782,49871,50041,50370,50382,50396) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN JUNIO 2024*/


/*FACTURAS JULIO 2024*/
// $mesTransaccion=7;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (50436,50590,50731,51041,51187,51251,51252,51324,51407,51555,51777,51802,51930) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN JULIO 2024*/


// /*FACTURAS AGOSTO 2024*/
// $mesTransaccion=8;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (52168,52205,52228,52234,52252,52336,52540,52546,52568,52626,52675,52679,52738,52771,52836,52967,53009,53035,53079,
// 53325,53345,53414,53462,53559,53613) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN JULIO 2024*/

// /*FACTURAS SEPTIEMBRE2024*/
// $mesTransaccion=9;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (53741,53833,53984,54115,54151,54241,54300,54492,54498,54614,54634,54663,54765,54869,54928,54988,55085,55136) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN SEPTIEMBRE 2024*/


/*FACTURAS OCTUBRE 2024*/
// $mesTransaccion=10;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (55219,55369,55446,55455,55496,55611,55749,55752,55768,55776,55812,55876,55931,56085,56212,56245,56374) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
// /*FIN OCTUBRE 2024*/

/*FACTURAS NOVIEMBRE 2024*/
// $mesTransaccion=11;
// $gestionTransaccion=2024;
// $sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
// where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
// (56779,57067,57129,57263,57330,57340,57439,57485,57541,57547,57624,57646,57679,57734,57875,57912,57922) and 
// YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
/*FIN OCTUBRE 2024*/

/*FACTURAS DICIEMBRE 2024*/
$mesTransaccion=12;
$gestionTransaccion=2024;
$sql="select s.cod_salida_almacenes, s.siat_cuis, sc.cufd, s.siat_cuf from salida_almacenes s, siat_cufd sc 
where s.siat_codigocufd=sc.codigo and s.salida_anulada=0 and s.cod_salida_almacenes in 
(58021,58050,58153,58243,58271,58338,58485,58752,58756,58779,58867) and 
YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
/*FIN DICIEMBRE 2024*/

echo $sql."<br><br>";

$resp=mysqli_query($enlaceCon, $sql);
while($dat=mysqli_fetch_array($resp)){  
  $codVenta=$dat[0];
  
  /*SACAMOS EL CODIGO DE LAS VENTAS*/
  $sqlCodigo="SELECT IFNULL(max(cod_salida_almacenes)+1,1) FROM salida_almacenes";
  $respCodigo=mysqli_query($enlaceCon,$sqlCodigo);
  $datCodSalida=mysqli_fetch_array($respCodigo);
  $codigoVentaNueva=$datCodSalida[0];

  $sqlNR="select max(nro_correlativo) from salida_almacenes s where s.cod_tiposalida=1001 and s.salida_anulada=0 and 
        YEAR(s.fecha)='$gestionTransaccion' and MONTH(s.fecha)='$mesTransaccion'";
  $respNR=mysqli_query($enlaceCon, $sqlNR);
  $nroCorrelativoNuevo=0;
  if($datNR=mysqli_fetch_array($respNR)){
    $nroCorrelativoNuevo=$datNR[0];
  }

  $sqlInsertaCabecera="insert into salida_almacenes(cod_salida_almacenes,cod_almacen,cod_tiposalida,cod_tipo_doc,fecha,hora_salida,territorio_destino,almacen_destino,observaciones,estado_salida,nro_correlativo,salida_anulada,cod_cliente,monto_total,descuento,monto_final,razon_social,nit,cod_chofer,cod_vehiculo,monto_cancelado,cod_dosificacion,monto_efectivo,monto_cambio,cod_tipopago,cod_cambio,siat_cuf,siat_cuis,siat_estado_facturacion,siat_fechaemision,siat_codigotipodocumentoidentidad,siat_codigoRecepcion,siat_codigocufd,siat_codigotipoemision,siat_complemento,siat_codigoPuntoVenta,siat_excepcion,siat_cod_leyenda,created_by,created_at,cod_tipopreciogeneral,cod_tipoventa2,monto_cancelado_bs,monto_cancelado_usd,tipo_cambio,cod_delivery) 
  SELECT '$codigoVentaNueva',cod_almacen,cod_tiposalida,'2',fecha,hora_salida,territorio_destino,almacen_destino,'FAC anulada codigo:$codVenta',
  estado_salida, '$nroCorrelativoNuevo',salida_anulada,cod_cliente,monto_total,descuento,monto_final,
  razon_social,nit,cod_chofer,cod_vehiculo,monto_cancelado,cod_dosificacion,monto_efectivo,monto_cambio,cod_tipopago,cod_cambio,'','','','','','','','','',siat_codigoPuntoVenta,siat_excepcion,siat_cod_leyenda,created_by,created_at,cod_tipopreciogeneral,cod_tipoventa2,monto_cancelado_bs,monto_cancelado_usd,tipo_cambio,cod_delivery
  FROM salida_almacenes s WHERE s.cod_salida_almacenes='$codVenta'";
  $respInsertaCabecera=mysqli_query($enlaceCon, $sqlInsertaCabecera);

  if($respInsertaCabecera==1){
      $sqlInsertaDetalle="INSERT INTO salida_detalle_almacenes 
        (cod_salida_almacen,cod_material,cantidad_unitaria,lote,fecha_vencimiento,precio_unitario,descuento_unitario,monto_unitario,observaciones,costo_almacen,costo_actualizado_final,costo_actualizado,cod_ingreso_almacen,orden_detalle)
        SELECT '$codigoVentaNueva',cod_material,cantidad_unitaria,lote,fecha_vencimiento,precio_unitario,descuento_unitario,monto_unitario,observaciones,costo_almacen,costo_actualizado_final,costo_actualizado,cod_ingreso_almacen,orden_detalle FROM salida_detalle_almacenes s where s.cod_salida_almacen = '$codVenta' ";
      $respInsertaDetalle=mysqli_query($enlaceCon, $sqlInsertaDetalle);
      

      $sqlUpd="update salida_almacenes set salida_anulada=1, estado_salida=3 where cod_salida_almacenes='$codVenta'";
      $respUpd=mysqli_query($enlaceCon, $sqlUpd);
  }

  echo "INSERTADO COD NUEVO: ".$codigoVentaNueva."<br><br>";
}
