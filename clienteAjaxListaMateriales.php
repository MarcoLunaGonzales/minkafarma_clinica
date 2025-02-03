<html>
<body>
<table align='center' class="texto">
<tr><th>Codigo</th><th>Producto</th><th>Linea</th><th>Principio Activo</th><th>Accion Terapeutica</th><th>Stock</th><th>Precio</th></tr>
<?php
require("conexionmysqli2.inc");
require("funciones.php");

$codigoMat=0;
$nomAccion="";
$nomPrincipio="";

$codigoCiudadGlobal=$_COOKIE["global_agencia"];

if(isset($_GET['codigoMat'])){
	$codigoMat=$_GET['codigoMat'];
}
if(isset($_GET['nomAccion'])){
	$nomAccion=$_GET['nomAccion'];
}
if(isset($_GET['nomPrincipio'])){
	$nomPrincipio=$_GET['nomPrincipio'];	
}
$codTipo=$_GET['codTipo'];
$nombreItem=$_GET['nombreItem'];
$globalAlmacen=$_COOKIE['global_almacen'];
$itemsNoUtilizar=$_GET['arrayItemsUtilizados'];
$tipoSalida=$_GET['tipoSalida'];

$fechaActual=date("Y-m-d");

$indexFila=0;

//SACAMOS LA CONFIGURACION PARA LA SALIDA POR VENCIMIENTO
$sqlConf="select valor_configuracion from configuraciones where id_configuracion=5";
$respConf=mysqli_query($enlaceCon,$sqlConf);
$datConf=mysqli_fetch_array($respConf);
$tipoSalidaVencimiento=$datConf[0];//$tipoSalidaVencimiento=mysql_result($respConf,0,0);

	$sql="select m.codigo_material, m.descripcion_material,
	(select concat(p.nombre_proveedor,'-',pl.nombre_linea_proveedor)as nombre_proveedor
	from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor), m.principio_activo, m.accion_terapeutica
	from material_apoyo m where estado=1 and m.codigo_material not in ($itemsNoUtilizar)";
	if($codigoMat!=""){
		$sql=$sql. " and codigo_material='$codigoMat'";
	}
	if($nombreItem!=""){
		$sql=$sql. " and descripcion_material like '%$nombreItem%' ";
	}
	if($tipoSalidaVencimiento==$tipoSalida){
		$sql=$sql. " and m.codigo_material in (select id.cod_material from ingreso_almacenes i, ingreso_detalle_almacenes id 
		where i.cod_ingreso_almacen=id.cod_ingreso_almacen and i.cod_almacen='$globalAlmacen' and i.ingreso_anulado=0 
		and id.fecha_vencimiento<'$fechaActual') ";
	}
	if((int)$codTipo>0){
    	if(isset($_GET["codProv"])){
          $sql=$sql." and m.cod_linea_proveedor in (SELECT cod_linea_proveedor from proveedores_lineas where cod_proveedor=".$_GET["codProv"].")";
    	}else{
    	  $sql=$sql." and m.cod_linea_proveedor=".$codTipo."";	
    	}        
    }
	if($nomAccion!=""){
		$sql=$sql. " and accion_terapeutica like '%$nomAccion%'";
	}
	if($nomPrincipio!=""){
		$sql=$sql. " and principio_activo like '%$nomPrincipio%'";
	}
	$sql=$sql." order by 2";
	
	//echo $sql;
	
	$resp=mysqli_query($enlaceCon,$sql);

	$numFilas=mysqli_num_rows($resp);
	if($numFilas>0){
		$cont=0;
		while($dat=mysqli_fetch_array($resp)){
			$codigo=$dat[0];
			$nombre=$dat[1];
			$linea=$dat[2];
			$principioActivo=$dat[3];
			$accionTerapeutica=$dat[4];
			
			$nombre=addslashes($nombre);
			$linea=addslashes($linea);
			
			if($tipoSalida==$tipoSalidaVencimiento){
				$stockProducto=stockProductoVencido($enlaceCon,$globalAlmacen, $codigo);
			}else{
				$stockProducto=stockProducto($enlaceCon,$globalAlmacen, $codigo);
			}
			
			//$ubicacionProducto=ubicacionProducto($enlaceCon,$globalAlmacen, $codigo);
					
			$datosProd=$codigo."|".$nombre."|".$linea;
		

			$consulta="select p.`precio` from precios p where p.`codigo_material`='$codigo' and p.`cod_precio`='1' and 
					cod_ciudad='$codigoCiudadGlobal'";
					
			$rs=mysqli_query($enlaceCon,$consulta);
			$registro=mysqli_fetch_array($rs);
			$precioProducto=$registro[0];
			if($precioProducto=="")
			{   $precioProducto=0;
			}
			$precioProducto=redondear2($precioProducto);
			$mostrarFila=1;
			if(isset($_GET["stock"])){
				 if($_GET["stock"]==1&&$stockProducto<=0){
                    $mostrarFila=0;
				 }  	              
			}
			if($mostrarFila==1){
				$indexFila++;

			  	if($stockProducto>0){
					$stockProducto="<b class='textograndenegro' style='color:#C70039'>".$stockProducto."</b>";
			  	}
				echo "<tr><td>$codigo</td><td><div class='textograndenegro'><a href='javascript:setMateriales(form1, $codigo, \"$nombre - $linea ($codigo)\", $precioProducto)'>$nombre</a></div></td>
				<td>$linea</td>
				<td><small>$principioActivo</small></td>
				<td><small>$accionTerapeutica</small></td>
				<td>$stockProducto</td>
				<td>$precioProducto</td>
				</tr>";
				$cont++;
			}
		}
	}else{
		echo "<tr><td colspan='3'>Sin Resultados en la busqueda.</td></tr>";
	}
	
?>
</table>

</body>
</html>