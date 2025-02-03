<html>

<body>
<table align='center' class="texto">
<tr>
	<th><input type='checkbox' id='selecTodo'  onchange="marcarDesmarcar(form1,this)"></th>
	<th>Linea</th>
	<th>Codigo</th>
	<th>Producto</th>
	<th>Stock</th>
	<th>Precio</th>
</tr>
<?php
require("conexionmysqli2.inc");
require("funciones.php");
$codTipo=$_GET['codTipo'];

$codigoItem=0;
if(isset($_GET["codigoItem"])){
	$codigoItem=$_GET['codigoItem'];
}

$nombreItem=$_GET['nombreItem'];
$globalAlmacen=$_COOKIE['global_almacen'];
$codCiudadIngreso=$_COOKIE['global_agencia'];

/*INGRESO CAJAS 0   INGRESO UNITARIOS 1*/
$banderaIngresoCajas=obtenerValorConfiguracion($enlaceCon,-7);


//$itemsNoUtilizar=$_GET['arrayItemsUtilizados'];
$itemsNoUtilizar="0";

	$sql="select m.codigo_material, m.descripcion_material, m.cantidad_presentacion, 
	(select concat(p.nombre_proveedor)
	from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor),
	m.codigo_anterior	
	from material_apoyo m, proveedores_lineas pl where pl.cod_linea_proveedor=m.cod_linea_proveedor and m.estado=1 
		and m.codigo_material not in ($itemsNoUtilizar)";
	if($codigoItem!="" && $codigoItem!=0){
		$sql=$sql. " and (m.codigo_material like '%$codigoItem%' or m.codigo_anterior like '%$codigoItem%') ";
	}
	if($nombreItem!=""){
		$sql=$sql. " and m.descripcion_material like '%$nombreItem%'";
	}
	if($codTipo!=0){
		$sql=$sql. " and pl.cod_proveedor = '$codTipo' ";
	}
	$sql=$sql." order by 2";

	//echo $sql;

	$resp=mysqli_query($enlaceCon,$sql);
	$numFilas=mysqli_num_rows($resp);
	if($numFilas>0){
		$cont=0;
		while($dat=mysqli_fetch_array($resp)){
			$cont++;
			$codigo=$dat[0];
			$nombre=$dat[1];
			$nombre=str_replace("," ,"", $nombre);
			$nombre=$nombre." (".$codigo.")";

			$nombre=addslashes($nombre);
			$cantidadPresentacion=$dat[2];

			//SI EL INGRESO ES EN UNITARIOS CAMBIAMOS LA PRESENTACION A 1
			if($banderaIngresoCajas==1){
				$cantidadPresentacion=1;
			}

			$linea=$dat[3];
			$codigoInterno=$dat[4];

			$stockProducto=stockProducto($enlaceCon,$globalAlmacen, $codigo);
			$precioProducto=precioProductoSucursalCalculadoSinMayorista($enlaceCon,$codigo,$codCiudadIngreso);
			if($precioProducto==""){
				$precioProducto=0;
			}
			$precioProductoF=formatonumeroDec($precioProducto);
			$margenLinea=margenLinea($enlaceCon,$codigo);			
			$datosProd=$codigo."|".$nombre."-".$linea."|".$cantidadPresentacion."|".$precioProducto."|".$margenLinea;
		
	
			echo "<tr><td>
			<input type='checkbox' id='idchk$cont' name='idchk$cont' value='$datosProd' onchange='ver(this)' ></td>
			<td>$linea</td>
			<td>$codigo / $codigoInterno</td>
			<td><div class='textograndenegro'><a href='javascript:setMateriales(form1, $codigo, \"$nombre - $linea\", $cantidadPresentacion, $precioProducto, $margenLinea)'>$nombre</a></div></td><td><div class='textograndenegro'>$stockProducto</div></td><td><div class='textograndenegro'>$precioProductoF</div></td>
			</tr>";
		}
	}else{
		echo "<tr><td colspan='3'>Sin Resultados en la busqueda.</td></tr>";
	}

?>
</table>

</body>
</html>