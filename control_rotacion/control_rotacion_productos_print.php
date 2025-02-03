
<meta charset="utf-8">
<?php
//header("Pragma: public");
//header("Expires: 0");
//$filename = "Control de Rotacion de Productos.xls";
// header("Content-type: application/x-msdownload");
// header("Content-Disposition: attachment; filename=$filename");
// header("Pragma: no-cache");
// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

// require_once("../estilos2.inc");
require_once '../funciones.php';
require_once '../funcion_nombres.php';
require_once("../conexionmysqli.inc");

 error_reporting(E_ALL);
 ini_set('display_errors', '1');


$id_proveedor=$_POST['cod_proveedor'];
// $regional=$_POST['regional'];
$fechai=$_POST['exafinicial'];
$fechai_x=date('d/m/Y', strtotime($fechai));
$fechaf=$_POST['exaffinal'];
$fechaf_x=date('d/m/Y', strtotime($fechaf));

$rpt_subcategoria=$_POST['rpt_subcategoria'];
$id_linea=implode(',', $rpt_subcategoria);

// $nombre_proveedor=obtenerNombreProveedor_nuevo($id_proveedor);
$nombre_proveedor="Provees";

$stringProductos=obtenerProductosAlmacen_nuevo($id_linea);

//echo "string prods.".$stringProductos;

$array_productos=explode(',', $stringProductos);
$sql_sucursales="";
$array_datosproductos=obtenerProductosnombre_presentacionAlmacen_nuevo($id_linea);//id linea, gestion,mes, y cod almacen 
$nombre_producto_array=$array_datosproductos[0];
$presentacion_producto_array=$array_datosproductos[1];
$linea_producto_array=$array_datosproductos[2];
//$costo_producto_array=$array_datosproductos[2];

$array_comprasProductos=obtenerProductosComprasIdLinea($id_linea,$fechai,$fechaf);//id linea, gestion,mes, y cod almacen 
echo "array: ".$array_comprasProductos;

// $totaSucursal=[];
// $datos_ultimo_costo=obtenerDatosUltimoCosto();
// $array_ultimo_costo=explode(',',$datos_ultimo_costo);
// $cod_gestion_ultimo=$array_ultimo_costo[0];
// $cod_mes_ultimo=$array_ultimo_costo[1];


$cod_gestion_ultimo=2022;
$cod_mes_ultimo=07;

// echo $cod_gestion_ultimo.",".$cod_mes_ultimo;

$totaSucursal_costo=[];
$totaSucursal_costo_sin_rotacion=[];

$total_productos=count($array_productos);

$totaSucursal_verde=[];
$totaSucursal_verde_claro=[];
$totaSucursal_rojo=[];
$totaSucursal_negro=[];
$totaSucursal_naranja=[];
$totaSucursal_azul=[];

// $estilo_Verde="style='background:#16A085;'";
// $estilo_Verde_claro="style='background:#82e0aa;'";
// $estilo_rojo="style='background: #ff4747;'";
// $estilo_negro="style='background: #424949;color:white;'";
// $estilo_naranja="style='background: #F5B041;'";
// $estilo_azul="style='background: #bb8fce;'";

$estilo_Verde="";
$estilo_Verde_claro="";
$estilo_rojo="";
$estilo_negro="";
$estilo_naranja="";
$estilo_azul="";

$estilo_Verde_title="style='background:#16A085;'";
$estilo_rojo_title="style='background: #ff4747;'";
$estilo_negro_title="style='background: #424949;color:white;'";
$estilo_naranja_title="style='background: #F5B041;'";
$estilo_azul_title="style='background: #bb8fce;'";
$estilo_Verde_claro_title="style='background:#82e0aa;'";

$tipo_rotacion=$_POST['tipo_rotacion'];
$cont_tiporotacion=count($tipo_rotacion);
for ($i=0; $i < $cont_tiporotacion; $i++) {  
  switch ($tipo_rotacion[$i]) {
    case '1':
    $estilo_Verde="style='background:#16A085;'";
    break;
    case '2':
    $estilo_rojo="style='background: #ff4747;'";
    break;
    case '3':
    $estilo_negro="style='background: #424949;color:white;'";
    break;
    case '4':
    $estilo_naranja="style='background: #F5B041;'";
    break;
    case '5':
    $estilo_azul="style='background: #bb8fce;'";
    break;
    case '6':
    $estilo_Verde_claro="style='background:#82e0aa;'";
    break;
  }  
}

$tipo_stock=$_POST['tipo_stock'];

?>
<table class="table table-bordered table-condensed">
  <thead>
    <tr><th <?=$estilo_Verde_title?>></th><th align="left" colspan="2">CON ROTACIÓN</th><th colspan="20" align="center"> &nbsp;</th></tr>
    <tr><th <?=$estilo_rojo_title?>></th><th align="left" colspan="2">SIN ROTACIÓN</th><th colspan="20" align="center"><b><?=$nombre_proveedor?></b></th></tr>
    <tr><th <?=$estilo_negro_title?>></th><th align="left" colspan="2">SIN ROTACIÓN & CON INGRESOS</th><th colspan="20" align="center"><b>Reporte Control de Rotación de Productos</b></th></tr>
    <tr><th <?=$estilo_naranja_title?>></th><th align="left" colspan="2">SIN ROTACIÓN & CON TRASPASOS</th><th colspan="20" align="center"> <b> <?=$fechai_x?> al <?=$fechaf_x?></b></th></tr>
    <tr><th <?=$estilo_azul_title?>></th><th align="left" colspan="2">SIN STOCK INICIAL & SIN ROTACION DESDE INGRESO</th><th colspan="20" align="center"> <b> SI;SF Donde SI: Saldo Incial ; SF: Saldo Final ; TV: Total Ventas</b></th></tr>
    <tr><th <?=$estilo_Verde_claro_title?>></th><th align="left" colspan="2">SIN STOCK INICIAL & CON ROTACION DESDE INGRESO</th><th colspan="20" align="center"></th></tr>
    <tr><th></th><th></th><th colspan="20" align="center">&nbsp;</th></tr>
    <tr style="border:1px;">
      <th>SUBLINEA</th>
      <th>CODIGO</th>
      <th>DES</th>
      <th>DIV</th>
      <?php
        $sql_listsucursales="SELECT a.cod_almacen,a.nombre_almacen
          from almacenes a join ciudades c on a.cod_ciudad=c.cod_ciudad 
          order by a.orden";
          $string_sucursales="";
          $resp=mysqli_query($enlaceCon,$sql_listsucursales);
          while($row=mysqli_fetch_array($resp)){
            $cod_almacen=$row['cod_almacen'];
            $nombre_almacen=$row['nombre_almacen']; ?>
            <th><?=$nombre_almacen;?></th><?php 
            $informacion=cargarValoresVentasYSaldosProductosArray_prodrotacion($cod_almacen,$fechai,$fechaf,$stringProductos);
            $informacion_costo=obtenerProductosnombre_presentacionAlmacen($stringProductos,$cod_gestion_ultimo,$cod_mes_ultimo,$cod_almacen);// productos, gestion,mes, y cod almacen 
            $datosSucursal[$cod_almacen]=$informacion;
            $datosSucursal_costo[$cod_almacen]=$informacion_costo;
            $string_sucursales.=$cod_almacen.",";
          }?>
          <th>COMPRAS [U]</th>
          <th>STOCK [U]</th>
          <th>STOCK VALORADO</th>
          <th>STOCK SIN ROTACION</th>
          <th></th>
          <th colspan="7">% DEL TOTAL DE SUCURSALES</th>
          <th></th>
          <th colspan="5">% DE SUCURSALES CON EXISTENCIA</th>
          <?php
          //echo $string_sucursales;
          $string_sucursales=trim($string_sucursales,",");
          $array_sucursales=explode(",", $string_sucursales);
          $total_sucursales=count($array_sucursales);
      ?>
    </tr>
  </thead>
  <tbody>
    <?php $index=1;
    //var_dump($array_sucursales);
      for ($i=0; $i <count($array_productos) ; $i++)
      {   
        $total_stock_almacenes=0;
        $total_stock_almacenes_sinrotacion=0;

        $total_stock_almacenes_valorado=0;
        $total_stock_almacenes_valorado_sinrotacion=0;

        $contador_rojo=0;
        $contador_verde=0;
        $contador_verde_claro=0;
        $contador_negro=0;
        $contador_naranja=0;
        $contador_azul=0;

        $codigo_producto=$array_productos[$i];
        $nombre_producto=$nombre_producto_array[$codigo_producto];
        $linea_producto=$linea_producto_array[$codigo_producto];
        $cantidad_presentacion=$presentacion_producto_array[$codigo_producto];
        // $costo_producto=$costo_producto_array[$codigo_producto];
        ?>
        <tr>
          <td><?=$linea_producto?></td>
          <td><?=$codigo_producto?></td>
          <td><?=$nombre_producto?></td>
          <td><?=number_format($cantidad_presentacion,0,'.','')?></td>
        <?php
        for ($j=0; $j <count($array_sucursales) ; $j++) {
          $cod_sucursal=$array_sucursales[$j];
          //INFO PRODUCTO
          $datosFila=$datosSucursal[$cod_sucursal];
          // $datosFila_costo=$datosSucursal_costo[$cod_sucursal];

          // $costo_producto=$datosFila_costo[$codigo_producto];
          $datosFila_costo=$datosSucursal_costo[$cod_sucursal];
          $costo_producto=$datosFila_costo[$codigo_producto];

          //datos obtenidos
          $ingresos=$datosFila[0];
          $ingresos_unidad=$datosFila[1];
          $salidas=$datosFila[2];
          $salidas_unidad=$datosFila[3];
          $ventas=$datosFila[4];
          $ventas_unidad=$datosFila[5];
          //SALDO ANTERIOR
          $ingresos_ant=$datosFila[6];
          $ingresos_unidad_ant=$datosFila[7];
          $salidas_ant=$datosFila[8];
          $salidas_unidad_ant=$datosFila[9];
          if(isset($ingresos[$codigo_producto]))
            $variable_ingresos=$ingresos[$codigo_producto];
          else $variable_ingresos=0;
          if(isset($ingresos_unidad[$codigo_producto]))
            $variable_ingresos_unidad=$ingresos_unidad[$codigo_producto];
          else $variable_ingresos_unidad=0;
          if(isset($salidas[$codigo_producto]))
            $variable_salidas=$salidas[$codigo_producto];
          else $variable_salidas=0;
          if(isset($salidas_unidad[$codigo_producto]))
            $variable_salidas_unidad=$salidas_unidad[$codigo_producto];
          else $variable_salidas_unidad=0;
          if(isset($ventas[$codigo_producto]))
            $variable_ventas=$ventas[$codigo_producto];
          else $variable_ventas=0;
          if(isset($ventas_unidad[$codigo_producto]))
            $variable_ventas_unidad=$ventas_unidad[$codigo_producto];
          else $variable_ventas_unidad=0;
          //saldo ant
          if(isset($ingresos_ant[$codigo_producto]))
            $variable_ingresos_ant=$ingresos_ant[$codigo_producto];
          else $variable_ingresos_ant=0;
          if(isset($ingresos_unidad_ant[$codigo_producto]))
            $variable_ingresos_unidad_ant=$ingresos_unidad_ant[$codigo_producto];
          else $variable_ingresos_unidad_ant=0;
          if(isset($salidas_ant[$codigo_producto]))
            $variable_salidas_ant=$salidas_ant[$codigo_producto];
          else $variable_salidas_ant=0;
          if(isset($salidas_unidad_ant[$codigo_producto]))
            $variable_salidas_unidad_ant=$salidas_unidad_ant[$codigo_producto];
          else $variable_salidas_unidad_ant=0;
          $totalIngresos_ant=$variable_ingresos_ant+($variable_ingresos_unidad_ant);
          $totalSalidas_ant=abs($variable_salidas_ant)+(abs($variable_salidas_unidad_ant)); 
          $cantSaldo_ant=$totalIngresos_ant-$totalSalidas_ant;

          $totalIngresos=$variable_ingresos+($variable_ingresos_unidad);
          $totalSalidas=abs($variable_salidas)+(abs($variable_salidas_unidad)); 
          $cantSaldo=$totalIngresos-$totalSalidas;
          if($cantSaldo<0){
            $cantSaldo=0;
          }
          //VENTAS
          $cantVentas=$variable_ventas+($variable_ventas_unidad);
          // $string_text="Ventas: $cantVentas";
          
          //*****ANALISIS estilos
          if($cantVentas>0){
            if($cantSaldo_ant==0){//verde claro
              $estilo=$estilo_Verde_claro;//verde
              $totaSucursal_verde_claro[$cod_sucursal]+=1;
              $contador_verde_claro++;
            }else{
              $estilo=$estilo_Verde;//verde
              $totaSucursal_verde[$cod_sucursal]+=1;
              $contador_verde++;
            }
          }else{
            if($cantSaldo_ant==$cantSaldo){
              if($cantSaldo_ant==0){
                $estilo="";
                $cantSaldo_ant="-";
                $cantSaldo="-";
              }else{
                $estilo=$estilo_rojo; //rojo
                $totaSucursal_rojo[$cod_sucursal]+=1;
                $contador_rojo++;
                $totaSucursal_costo_sin_rotacion[$cod_sucursal]+=$cantSaldo*$costo_producto;
                $total_stock_almacenes_sinrotacion+=$cantSaldo;
                $total_stock_almacenes_valorado_sinrotacion+=$cantSaldo*$costo_producto;
              }
            }else{
              if($cantSaldo_ant<$cantSaldo){
                if($cantSaldo_ant==0){//sin Stock inicial
                  $estilo=$estilo_azul;//azul
                  $totaSucursal_azul[$cod_sucursal]+=1;
                  $contador_azul++;
                  // $totaSucursal_costo_sin_rotacion[$cod_sucursal]+=$cantSaldo*$costo_producto;
                  // $total_stock_almacenes_sinrotacion+=$cantSaldo;
                  // $total_stock_almacenes_valorado_sinrotacion+=0$cantSaldo*$costo_producto
                }else{
                  $estilo=$estilo_negro;//negro
                  $totaSucursal_negro[$cod_sucursal]+=1;
                  $contador_negro++;
                  $totaSucursal_costo_sin_rotacion[$cod_sucursal]+=$cantSaldo*$costo_producto;
                  $total_stock_almacenes_sinrotacion+=$cantSaldo;
                  $total_stock_almacenes_valorado_sinrotacion+=$cantSaldo*$costo_producto;
                }
              }else{
                $estilo=$estilo_naranja;//naranja
                $totaSucursal_naranja[$cod_sucursal]+=1;
                $contador_naranja++;
                $totaSucursal_costo_sin_rotacion[$cod_sucursal]+=$cantSaldo*$costo_producto;
                $total_stock_almacenes_sinrotacion+=$cantSaldo;
                $total_stock_almacenes_valorado_sinrotacion+=$cantSaldo*$costo_producto;
              }
            }
          }

          if($tipo_stock==1){ // INICIAL & FINAL & VENTAS?>
            <td <?=$estilo?> class="text-center"><small><?=$cantSaldo_ant;?>;<?=$cantSaldo?>;<?=$cantVentas?></small></td>
            <?php
          }elseif($tipo_stock==2){ //solo SALDO INICIAL
            ?>
            <td <?=$estilo?> class="text-center"><small><?=$cantSaldo_ant;?></small></td>
            <?php
          }elseif($tipo_stock==3){ //solo SALDO FINAL
            ?>
            <td <?=$estilo?> class="text-center"><small><?=$cantSaldo?></small></td>
            <?php
          }elseif($tipo_stock==4){ //solo CANTIDAD DE VENTAS
            ?>
            <td <?=$estilo?> class="text-center"><small><?=$cantVentas?></small></td>
            <?php
          }


          $total_stock_almacenes+=$cantSaldo;//EXISTENCIA
          $total_stock_almacenes_valorado+=$cantSaldo*$costo_producto;//existenvia valorado

          $totaSucursal_costo[$cod_sucursal]+=$cantSaldo*$costo_producto;
        }
        $total_sucursales_con_stock=$contador_verde+$contador_verde_claro+$contador_rojo+$contador_negro+$contador_naranja+$contador_azul;
        if($total_sucursales_con_stock==0){
          $total_sucursales_con_stock=1;
        }

        $porcentaje_verde=$contador_verde*100/$total_sucursales;
        $porcentaje_verde_claro=$contador_verde_claro*100/$total_sucursales;
        $porcentaje_rojo=$contador_rojo*100/$total_sucursales;
        $porcentaje_negro=$contador_negro*100/$total_sucursales;
        $porcentaje_naranja=$contador_naranja*100/$total_sucursales;
        $porcentaje_azul=$contador_azul*100/$total_sucursales;
        $porcentaje_sinexsitencia=100-$porcentaje_verde-$porcentaje_verde_claro-$porcentaje_rojo-$porcentaje_negro-$porcentaje_naranja-$porcentaje_azul;

        if($array_comprasProductos[$codigo_producto]>0){
          $totalCompra=$array_comprasProductos[$codigo_producto];
        }else{
          $totalCompra=0;
        }
        ?>
          <td><b><?=$totalCompra;?></b></td>
          <td><b><?=$total_stock_almacenes?></b></td>
          <td><b><?=round($total_stock_almacenes_valorado,2)?></b></td>
          <td><b><?=round($total_stock_almacenes_valorado_sinrotacion,2)?></b></td>
          <td></td>
          <td <?=$estilo_Verde_title?>><?=round($porcentaje_verde,2)?></td>
          <td <?=$estilo_Verde_claro_title?>><?=round($porcentaje_verde_claro,2)?></td>
          <td <?=$estilo_rojo_title?>><?=round($porcentaje_rojo,2)?></td>
          <td <?=$estilo_negro_title?>><?=round($porcentaje_negro,2)?></td>
          <td <?=$estilo_naranja_title?>><?=round($porcentaje_naranja,2)?></td>
          <td <?=$estilo_azul_title?>><?=round($porcentaje_azul,2)?></td>
          <td ><?=round($porcentaje_sinexsitencia,2)?></td>
          <td></td>
          <td <?=$estilo_Verde_title?>><?=round($contador_verde*100/$total_sucursales_con_stock,2)?></td>
          <td <?=$estilo_Verde_claro_title?>><?=round($contador_verde_claro*100/$total_sucursales_con_stock,2)?></td>
          <td <?=$estilo_rojo_title?>><?=round($contador_rojo*100/$total_sucursales_con_stock,2)?></td>
          <td <?=$estilo_negro_title?>><?=round($contador_negro*100/$total_sucursales_con_stock,2)?></td>
          <td <?=$estilo_naranja_title?>><?=round($contador_naranja*100/$total_sucursales_con_stock,2)?></td>
          <td <?=$estilo_azul_title?>><?=round($contador_azul*100/$total_sucursales_con_stock,2)?></td>
        </tr><?php
      }
      ?>
   <tr><td colspan="4" valign="top"><br><b>STOCK VALORADO SUCURSAL<br>STOCK SIN ROTACION SUCURSAL<br><br>% DE PRODUCTOS CON STOCK</b></td>
      <?php
      for ($j=0; $j <count($array_sucursales); $j++) {
          $cod_sucursal_x=$array_sucursales[$j];
          $total_productos_con_stock=$totaSucursal_verde[$cod_sucursal_x]+$totaSucursal_verde_claro[$cod_sucursal_x]+$totaSucursal_rojo[$cod_sucursal_x]+$totaSucursal_negro[$cod_sucursal_x]+$totaSucursal_naranja[$cod_sucursal_x]+$totaSucursal_azul[$cod_sucursal_x];
          if($total_productos_con_stock==0){
            $total_productos_con_stock=1;
          }
          ?>
          <td><b><br><?=round($totaSucursal_costo[$cod_sucursal_x],2)?>
          <br><?=round($totaSucursal_costo_sin_rotacion[$cod_sucursal_x],2)?>
          <br>
          <br><span style='color: #16A085;'><?=round($totaSucursal_verde[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span>
          <br><span style='color: #82e0aa;'><?=round($totaSucursal_verde_claro[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span>
          <br><span style='color: #ff4747;'><?=round($totaSucursal_rojo[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span>
          <br><span style='color: #424949;'><?=round($totaSucursal_negro[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span>
          <br><span style='color: #F5B041;'><?=round($totaSucursal_naranja[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span>
          <br><span style='color: #bb8fce;'><?=round($totaSucursal_azul[$cod_sucursal_x]*100/$total_productos_con_stock,2)?> %</span></b></td>
          <?php
      } ?>
   </tr>
   <!-- color rojo -->
   
  </tbody>
</table>
