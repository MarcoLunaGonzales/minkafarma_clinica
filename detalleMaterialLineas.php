<?php	
	require("conexionmysqli.php");
	require('estilos.inc');
	require('funciones.php');
	
	$globalAlmacen=$_COOKIE['global_almacen'];
	$lineaDistribuidor=$_GET['linea'];
	
	echo "<h1>Detalle de Producto x Linea de Distribuidor</h1>";

	echo "<form method='post' action=''>";
	$sql="select m.codigo_material, m.descripcion_material, m.estado, 
		(select e.nombre_empaque from empaques e where e.cod_empaque=m.cod_empaque), 
		(select f.nombre_forma_far from formas_farmaceuticas f where f.cod_forma_far=m.cod_forma_far), 
		(select pl.nombre_linea_proveedor from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor),
		(select t.nombre_tipoventa from tipos_venta t where t.cod_tipoventa=m.cod_tipoventa), m.cantidad_presentacion, m.principio_activo 
		from material_apoyo m
		where m.estado='1' and cod_linea_proveedor=$lineaDistribuidor order by m.descripcion_material";	
	//echo $sql;
	$resp=mysqli_query($enlaceCon,$sql);
	
	echo "</th></tr></table><br>";
		
	echo "<center><table class='texto'>";
	echo "<tr><th>Indice</th><th>&nbsp;</th><th>Nombre Producto</th><th>Empaque</th>
		<th>Cant.Presentacion</th><th>Forma Farmaceutica</th><th>Linea Distribuidor</th><th>Principio Activo</th><th>Tipo Venta</th>
		<th>Accion Terapeutica</th><th>Stock</th><th>&nbsp;</th></tr>";
	
	$indice_tabla=1;
	while($dat=mysqli_fetch_array($resp))
	{
		$codigo=$dat[0];
		$nombreProd=$dat[1];
		$estado=$dat[2];
		$empaque=$dat[3];
		$formaFar=$dat[4];
		$nombreLinea=$dat[5];
		$tipoVenta=$dat[6];
		$cantPresentacion=$dat[7];
		$principioActivo=$dat[8];
		
		$stockProducto=stockProducto($enlaceCon,$globalAlmacen, $codigo);
		if($stockProducto>0){
			$stockProducto="<b class='textograndenegro' style='color:#C70039'>".$stockProducto."</b>";
		}
		
		$txtAccionTerapeutica="";
		$sqlAccion="select a.nombre_accionterapeutica from acciones_terapeuticas a, material_accionterapeutica m
			where m.cod_accionterapeutica=a.cod_accionterapeutica and 
			m.codigo_material='$codigo'";
		$respAccion=mysqli_query($enlaceCon,$sqlAccion);
		while($datAccion=mysqli_fetch_array($respAccion)){
			$nombreAccionTerX=$datAccion[0];
			$txtAccionTerapeutica=$txtAccionTerapeutica." - ".$nombreAccionTerX;
		}
		
		echo "<tr><td align='center'>$indice_tabla</td><td align='center'>
		<input type='checkbox' name='codigo' value='$codigo'></td>
		<td><a href='editar_material_apoyo.php?cod_material=$codigo&pagina_retorno=0' target='_blank'>$nombreProd</a></td><td>$empaque</td>
		<td>$cantPresentacion</td><td>$formaFar</td>
		<td>$nombreLinea</td><td>$principioActivo</td><td>$tipoVenta</td>
		<td>$txtAccionTerapeutica</td>
		<td>$stockProducto</td>
		<td><a href='editar_material_apoyo.php?cod_material=$codigo&pagina_retorno=1' target='_parent'><img src='imagenes/edit.png' width='20'></a></td>
		</tr>";
		$indice_tabla++;
	}
	echo "</table></center><br>";		
	echo "</form>";
?>
