<script language='Javascript'>
	function validar(f)
	{
		if(f.material.value=='')
		{	alert('El campo Nombre esta vacio.');
			f.material.focus();
			return(false);
		}
		if(f.codLinea.value=='')
		{	alert('Debe seleccionar Linea.');
			f.codLinea.focus();
			return(false);
		}
		if(f.codForma.value=='')
		{	alert('Debe seleccionar Forma Farmaceutica.');
			f.codForma.focus();
			return(false);
		}
		if(f.codEmpaque.value=='')
		{	alert('Debe seleccionar Empaque.');
			f.codEmpaque.focus();
			return(false);
		}
		if(f.codTipoVenta.value=='')
		{	alert('Debe seleccionar Tipo de Venta.');
			f.codTipoVenta.focus();
			return(false);
		}
		
		
		var codAccionTerapeutica=new Array();
		var j=0;
		for(i=0;i<=f.codAccionTerapeutica.options.length-1;i++)
		{	if(f.codAccionTerapeutica.options[i].selected)
			{	codAccionTerapeutica[j]=f.codAccionTerapeutica.options[i].value;
				j++;
			}
		}
		f.arrayAccionTerapeutica.value=codAccionTerapeutica;
		
		f.submit();
	}

</script>

<head>
    <script src="//code.jquery.com/jquery-3.1.1.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link href="autoComplete/tokenize2.css" rel="stylesheet" />
    <script src="autoComplete/tokenize2.js"></script>
    <link href="autoComplete/demo.css" rel="stylesheet" />
</head>
<?php
require("conexionmysqli.php");
require('estilos.inc');
require('funciones.php');


$codProducto=$_GET['cod_material'];
$paginaRetorno=$_GET['pagina_retorno'];


$sqlEdit="select m.codigo_material, m.descripcion_material, m.estado, m.cod_linea_proveedor, m.cod_forma_far, m.cod_empaque, 
	m.cantidad_presentacion, m.principio_activo, m.cod_tipoventa, m.producto_controlado from material_apoyo m where m.codigo_material='$codProducto'";
$respEdit=mysqli_query($enlaceCon,$sqlEdit);
while($datEdit=mysqli_fetch_array($respEdit)){
	$nombreProductoX=$datEdit[1];
	$codLineaX=$datEdit[3];
	$codFormaX=$datEdit[4];
	$codEmpaqueX=$datEdit[5];
	$cantidadPresentacionX=$datEdit[6];
	$principioActivoX=$datEdit[7];
	$codTipoVentaX=$datEdit[8];
	$productoControlado=$datEdit[9];
}

$sqlPrecio="select p.`precio` from `precios` p where p.`cod_precio`=1 and p.`codigo_material`='$codProducto'";
$respPrecio=mysqli_query($enlaceCon,$sqlPrecio);
$numFilas=mysqli_num_rows($respPrecio);
if($numFilas>=1){
	$datPrecio=mysqli_fetch_array($respPrecio);
	$precio1=$datPrecio[0];
	//$precio1=mysql_result($respPrecio,0,0);
	$precio1=redondear2($precio1);
}else{
	$precio1=0;
	$precio1=redondear2($precio1);
}

echo "<form action='guarda_editarproducto.php' method='post' name='form1'>";

echo "<h1>Editar Producto</h1>";


echo "<input type='hidden' name='pagina_retorno' id='pagina_retorno' value='$paginaRetorno'>";
echo "<input type='hidden' name='linea_anterior' id='linea_anterior' value='$codLineaX'>";
echo "<input type='hidden' name='codProducto' id='codProducto' value='$codProducto'>";

echo "<center><table class='texto'>";
echo "<tr><th align='left'>Nombre</th>";
echo "<td align='left'>
	<input type='text' class='texto' name='material' size='40' style='text-transform:uppercase;' value='$nombreProductoX'>
	</td>";
	
echo "<tr><th align='left'>Linea</th>";
$sql1="select pl.cod_linea_proveedor, CONCAT(p.nombre_proveedor,' - ',pl.nombre_linea_proveedor) from proveedores p, proveedores_lineas pl 
where p.cod_proveedor=pl.cod_proveedor and pl.estado=1 order by 2;";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<td>
<div class='container'>
		<div class='col-md-4'>
		<select name='codLinea' id='codLinea' class='tokenize-limit-demo2'>
		<option value=''></option>";
		while($dat1=mysqli_fetch_array($resp1))
		{	$codLinea=$dat1[0];
			$nombreLinea=$dat1[1];
			if($codLinea==$codLineaX){
				echo "<option value='$codLinea' selected>$nombreLinea</option>";
			}else{
				echo "<option value='$codLinea'>$nombreLinea</option>";
			}
		}
		echo "</select>
	</div>
	</div>
</td>";
echo "</tr>";

echo "<tr><th>Forma Farmaceutica</th>";
$sql1="select f.cod_forma_far, f.nombre_forma_far from formas_farmaceuticas f 
where f.estado=1 order by 2;";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<td>
<div class='container'>
		<div class='col-md-4'>
			<select name='codForma' id='codForma' class='tokenize-limit-demo2'>
			<option value=''></option>";
			while($dat1=mysqli_fetch_array($resp1))
			{	$codForma=$dat1[0];
				$nombreForma=$dat1[1];
				if($codForma==$codFormaX){
					echo "<option value='$codForma' selected>$nombreForma</option>";
				}else{
					echo "<option value='$codForma'>$nombreForma</option>";
				}
			}
			echo "</select>
	</div>
	</div>
</td>";
echo "</tr>";

echo "<tr><th>Empaque</th>";
$sql1="select e.cod_empaque, e.nombre_empaque from empaques e where e.estado=1 order by 2;";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<td>
	<div class='container'>
		<div class='col-md-4'>
			<select name='codEmpaque' id='codEmpaque' class='tokenize-limit-demo2'>
				<option value=''></option>";
			while($dat1=mysqli_fetch_array($resp1))
			{	$codEmpaque=$dat1[0];
				$nombreEmpaque=$dat1[1];
				if($codEmpaque==$codEmpaqueX){
					echo "<option value='$codEmpaque' selected>$nombreEmpaque</option>";
				}else{
					echo "<option value='$codEmpaque'>$nombreEmpaque</option>";					
				}
			}
echo "</select>
	</div>
	</div>
</td>";
echo "</tr>";

echo "<tr><th>Cantidad Presentacion</th>
	<td><input type='number' name='cantidadPresentacion' id='cantidadPresentacion' min='1' max='1000' value='$cantidadPresentacionX'></td>
	</tr>";
	
echo "<tr><th>Principio Activo</th>
	<td><input type='text' name='principioActivo' id='principioActivo' style='text-transform:uppercase;' value='$principioActivoX'></td>
	</tr>";

echo "<tr><th>Tipo Venta</th>";
$sql1="select t.cod_tipoventa, t.nombre_tipoventa from tipos_venta t where t.estado=1;";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<td>
	<div class='container'>
		<div class='col-md-4'>
			<select name='codTipoVenta' id='codTipoVenta' class='tokenize-limit-demo2'>
			<option value=''></option>";
			while($dat1=mysqli_fetch_array($resp1))
			{	$codTipoVenta=$dat1[0];
				$nombreTipoVenta=$dat1[1];
				if($codTipoVenta==$codTipoVentaX){
					echo "<option value='$codTipoVenta' selected>$nombreTipoVenta</option>";
				}else{
					echo "<option value='$codTipoVenta'>$nombreTipoVenta</option>";					
				}
			}
echo "</select>
	</div>
	</div>
</td>";
echo "</tr>";


echo "<tr><th>Accion Terapeutica</th>";
$sql1="select l.cod_accionterapeutica as value, l.nombre_accionterapeutica as texto from acciones_terapeuticas l;";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<td>
	<div class='container'>
		<div class='col-md-6'>
			<select name='codAccionTerapeutica' id='codAccionTerapeutica' class='tokenize-sample-demo1' multiple>
			<option value=''></option>";
			while($dat1=mysqli_fetch_array($resp1))
			{	$codigo=$dat1[0];
				$nombre=$dat1[1];
				$sqlRevisa="select count(*) from material_accionterapeutica m where m.cod_accionterapeutica='$codigo' and 
				m.codigo_material='$codProducto'";
				$respRevisa=mysqli_query($enlaceCon,$sqlRevisa);
				$datRevisa=mysqli_fetch_array($respRevisa);
				$numRevisa=$datRevisa[0];
				//$numRevisa=mysql_result($respRevisa,0,0);
				if($numRevisa>0){
					echo "<option value='$codigo' selected>$nombre</option>";
				}else{
					echo "<option value='$codigo'>$nombre</option>";
				}
			}
echo "</select>
	</div>
	</div>
</td>";
echo "</tr>";

echo "<tr><th>Producto Controlado</th>";
if($productoControlado==0){
	echo "<td>
			<input type='radio' name='producto_controlado' value='0' checked>NO
			<input type='radio' name='producto_controlado' value='1'>SI
	</td>";	
}else{
	echo "<td>
			<input type='radio' name='producto_controlado' value='0' checked>NO
			<input type='radio' name='producto_controlado' value='1' checked>SI
	</td>";
}
echo "</tr>";

echo "<tr><th align='left'>Precio de Venta</th>";
echo "<td align='left'>
	<input type='number' class='texto' name='precio_producto' id='precio_producto' value='$precio1' step='0.01'>
	</td></tr>";

?>	

	<script>
		$('.tokenize-sample-demo1').tokenize2();
		$('.tokenize-limit-demo2').tokenize2({
                tokensMaxItems: 1
        });
	</script>
	
<?php
	echo "</td></tr>";
echo "</table></center>";
echo "<input type='hidden' name='arrayAccionTerapeutica' id='arrayAccionTerapeutica'>";
echo "<div class='divBotones'>
<input type='button' class='boton' value='Guardar' onClick='validar(this.form)'>
<input type='button' class='boton2' value='Cancelar' onClick='location.href=\"navegador_material.php\"'>
</div>";
echo "</form>";
?>

<script>
    $('.tokenize-sample-demo1').tokenize2();
</script>

