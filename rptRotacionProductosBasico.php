<?php
//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition: attachment; filename=archivo.xls");
require('estilos_reportes_almacencentral.php');
require('function_formatofecha.php');
require('conexionmysqli.php');
require("funciones.php");
 

$rptAlmacen=$_POST["rpt_almacen"];
$rptGrupo=$_POST["rpt_grupo"];

$rptFechaInicio=$_POST["rpt_ini"];
$rptFechaFinal=$_POST["rpt_fin"];

$rptPorcentaje=$_POST["rpt_porcentaje"];

$fechaInicioPivot = $rptFechaInicio;
//restamos un dia
$fechaInicioPivot=date("Y-m-d",strtotime($fechaInicioPivot."- 1 days")); 
//echo "inicio pivot: ".$fechaInicioPivot;

//echo "almacen: ".$rptAlmacen;

$rptGrupoS="";
if($rptGrupo!=""){
	$rptGrupoS=implode(",",$rptGrupo);
}

$rptAlmacenS="";
if($rptAlmacen!=""){
	$rptAlmacenS=implode(",",$rptAlmacen);
}
$rptAlmacenPrincipal=$rptAlmacen[0];


$fecha_reporte=date("Y-m-d");
$txt_reporte="Fecha de Reporte <strong>$fecha_reporte</strong>";

	$sql_nombre_almacen="select nombre_almacen from almacenes where cod_almacen in ($rptAlmacenS)";
	$resp_nombre_almacen=mysqli_query($enlaceCon,$sql_nombre_almacen);
	$nombre_almacen="";
	while($datos_nombre_almacen=mysqli_fetch_array($resp_nombre_almacen)){
		$nombre_almacen.=$datos_nombre_almacen[0]." - ";
	}	
	
	echo "<h1>Reporte Rotación de Productos
	<br>Almacen: <strong>$nombre_almacen</strong>
	<br>Periodo: <strong>$rptFechaInicio  a  $rptFechaFinal</strong>
	<br>Ver Rotacion Menor al: <strong>$rptPorcentaje %</strong>
	<br>$txt_reporte</h1>";
	
		//desde esta parte viene el reporte en si
		
		
		$sql_item="select ma.codigo_material, ma.descripcion_material, ma.cantidad_presentacion, p.nombre_proveedor 
		from material_apoyo ma, proveedores p, proveedores_lineas pl
		where ma.codigo_material<>0 and ma.estado='1' and p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=ma.cod_linea_proveedor
		and p.cod_proveedor in ($rptGrupoS) order by p.nombre_proveedor, ma.descripcion_material";

		$resp_item=mysqli_query($enlaceCon,$sql_item);
		
		echo "<br><table border=0 align='center' class='textomediano' width='70%'>
			<thead>
				<tr><th>&nbsp;</th><th>Distribuidor</th><th>COD INT</th><th>Producto</th><th>Precio Actual</th>
				<th>Stock Inicial</th>
				<th>Ingresos en el periodo</th>
				<th>Traspasos</th>
				<th>Ventas</th>
				<th>% Rotacion</th>
				</tr>
			</thead>";				
	
		$indice=1;
		$totalStock=0;
		while($datos_item=mysqli_fetch_array($resp_item)){	
			$codigo_item=$datos_item[0];
			$nombre_item=$datos_item[1];
			$cantidadPresentacion=$datos_item[2];
			$nombreDistribuidor=$datos_item[3];
			
			$rpt_territorio=obtenerCiudadDesdeAlmacen($rptAlmacenPrincipal);
			$precio0=precioVenta($enlaceCon,$codigo_item,$rpt_territorio);

			$cadena_mostrar="";

			$cadena_mostrar.="<tr><td>$indice</td><td>$nombreDistribuidor</td><td>$codigo_item</td><td>$nombre_item</td><td align='center'>$precio0</td>";

			$stockAnterior=stockProductoAFecha($enlaceCon, $rptAlmacenS, $codigo_item, $fechaInicioPivot);

			//echo $stock2;
			$cantidadIngresosPeriodo=ingresosItemPeriodo($enlaceCon, $rptAlmacenS, $codigo_item, $rptFechaInicio, $rptFechaFinal);
			
			$cantidadSalidasTraspasoPeriodo=salidasItemPeriodoxTipoSalida($enlaceCon, $rptAlmacenS, $codigo_item, $rptFechaInicio, $rptFechaFinal,1000);

			$cantidadSalidaVentasPeriodo=salidasItemPeriodoxTipoSalida($enlaceCon, $rptAlmacenS, $codigo_item, $rptFechaInicio, $rptFechaFinal,1001);
			
			$saldoFinalItem=0;
			$saldoFinalItem=$stockAnterior+$cantidadIngresosPeriodo-$cantidadSalidasPeriodo;

			if($stockAnterior<=0){	
				$cadena_mostrar.="<td align='center'>$stockAnterior</td>";
			}
			elseif($stockAnterior>0){	
				$cadena_mostrar.="<td align='center'><span class='textomedianorojo'><b>$stockAnterior</b></span></td>";
			}			

			$cantidadIngresosPeriodoF=formatonumero($cantidadIngresosPeriodo);
			$cantidadSalidasTraspasoPeriodoF=formatonumero($cantidadSalidasTraspasoPeriodo);
			$cantidadSalidaVentasPeriodoF=formatonumero($cantidadSalidaVentasPeriodo);

			$saldoFinalItemF=formatonumero($saldoFinalItem);
			
			if($cantidadIngresosPeriodo>0){
				$cantidadIngresosPeriodoF="<span class='textomedianorojo'><b>$cantidadIngresosPeriodoF</b></span>";
			}else{
				$cantidadIngresosPeriodoF="$cantidadIngresosPeriodoF";
			}

			if($cantidadSalidasTraspasoPeriodo>0){
				$cantidadSalidasTraspasoPeriodoF="<span class='textomedianorojo'><b>$cantidadSalidasPeriodoF</b></span>";
			}else{
				$cantidadSalidasTraspasoPeriodoF="$cantidadSalidasTraspasoPeriodoF";
			}

			if($cantidadSalidaVentasPeriodo>0){
				$cantidadSalidaVentasPeriodoF="<span class='textomedianorojo'><b>$cantidadSalidaVentasPeriodo</b></span>";
			}else{
				$cantidadSalidaVentasPeriodoF="$cantidadSalidaVentasPeriodoF";
			}

			$porcentajeRotacion=($cantidadSalidaVentasPeriodo/($stockAnterior+$cantidadIngresosPeriodo))*100;
			$porcentajeRotacionF=formatonumeroDec($porcentajeRotacion);
			$porcentajeRotacionF="<span class='textomedianorojo'><b>$porcentajeRotacionF</b></span>";


			$cadena_mostrar.="<td align='center'>$cantidadIngresosPeriodoF</td>
			<td align='center'>$cantidadSalidasTraspasoPeriodoF</td>
			<td align='center'>$cantidadSalidaVentasPeriodoF</td>
			<td align='center'>$porcentajeRotacionF</td>
			</tr>";
			
			$sql_linea="select * from material_apoyo where codigo_material='$codigo_item'";
			$resp_linea=mysqli_query($enlaceCon,$sql_linea);			
			$num_filas=mysqli_num_rows($resp_linea);
			
			if( (($stockAnterior+$cantidadIngresosPeriodo) > 0) && $porcentajeRotacion<=$rptPorcentaje){
				echo $cadena_mostrar;
			}
			$indice++;
		}

		echo "</table>";
		
?>