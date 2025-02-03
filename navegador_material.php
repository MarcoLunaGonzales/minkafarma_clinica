<?php
require("conexionmysqli.inc");
require('estilos.inc');
require('funciones.php');
?>

<script language="JavaScript">
function nuevoAjax(){	
	var xmlhttp=false;
	try {
			xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
	try {
		xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
	} catch (E) {
		xmlhttp = false;
	}
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
 	xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}
function enviar_nav(f){	
	console.log('consolaaaaaaaaaaaa');
	location.href='registrar_material_apoyo.php';
}
function enviar_buscador(){	
	var proveedorB=document.getElementById('proveedorBusqueda').value;
	var principioActivoB=document.getElementById('principioActivoBusqueda').value;
	var nombreB=document.getElementById('itemNombreBusqueda').value;
	var codBarrasB=document.getElementById('input_codigo_barras').value;
	var codigoProductoB=document.getElementById('itemCodigoBusqueda').value;
	var precioAbiertoB=document.getElementById('precioAbiertoBusqueda').value;
	var productoControladoB=document.getElementById('productoControladoBusqueda').value;

	console.log("datos prov: "+proveedorB+" PA: "+principioActivoB+" nombre "+nombreB+" codBarrasB: "+codBarrasB);
	location.href="navegador_material.php?vista=0&proveedorB="+proveedorB+"&nombreB="+nombreB+"&principioActivoB="+principioActivoB+"&barrasB="+codBarrasB+"&codigoProductoB="+codigoProductoB+"&precioAbiertoB="+precioAbiertoB+"&productoControladoB="+productoControladoB;
}
function mostrarBusqueda(){
	document.getElementById('divRecuadroExt').style.visibility='visible';
	document.getElementById('divProfileData').style.visibility='visible';
	document.getElementById('divProfileDetail').style.visibility='visible';
	document.getElementById('divboton').style.visibility='visible';
	//document.getElementById('divListaMateriales').innerHTML='';
	document.getElementById('itemNombreBusqueda').value='';	
	document.getElementById('itemNombreBusqueda').focus();		
}
function Hidden(){
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';
}
function deleteProducto(codProducto,nombreProducto){
	console.log(codProducto);
	if(confirm('Desea Eliminar el Producto '+nombreProducto+', la accion no se podra revertir.')){

	 	ajax=nuevoAjax();
		ajax.open("GET", "ajaxDeleteProducto.php?cod_producto="+codProducto,true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				$("#divDelete"+codProducto).html(ajax.responseText);
			}
		}
		ajax.send(null);	 
		//location.href="eliminar_material_apoyo.php?datos="+codProducto;
	}
}

function codigoBarraProducto(codMaterial){
	location.href="ticketMaterial.php?cod_material="+codMaterial;
}
		/*function eliminar_nav(f)
		{
			var i;
			var j=0;
			datos=new Array();
			for(i=0;i<=f.length-1;i++)
			{
				if(f.elements[i].type=='checkbox')
				{	if(f.elements[i].checked==true)
					{	datos[j]=f.elements[i].value;
						j=j+1;
					}
				}
			}
			if(j==0)
			{	alert('Debe seleccionar al menos un material de apoyo para proceder a su eliminaci�n.');
			}
			else
			{
				if(confirm('Esta seguro de eliminar los datos.'))
				{
					location.href='eliminar_material_apoyo.php?datos='+datos+'';
				}
				else
				{
					return(false);
				}
			}
		}
		function editar_nav(f)
		{
			var i;
			var j=0;
			var j_ciclo;
			for(i=0;i<=f.length-1;i++)
			{
				if(f.elements[i].type=='checkbox')
				{	if(f.elements[i].checked==true)
					{	j_ciclo=f.elements[i].value;
						j=j+1;
					}
				}
			}
			if(j>1)
			{	alert('Debe seleccionar solamente un material de apoyo para editar sus datos.');
			}
			else
			{
				if(j==0)
				{
					alert('Debe seleccionar un material de apoyo para editar sus datos.');
				}
				else
				{
					location.href='editar_material_apoyo.php?cod_material='+j_ciclo+'&pagina_retorno=0';
				}
			}
		}

function enviar_buscador(){	
	var proveedorB=$('#proveedorBusqueda').val();
	var principioActivoB=$('#principioActivoBusqueda').val();
	var nombreB=$('#itemNombreBusqueda').val();
	var codBarrasB=$('#input_codigo_barras').val();
	location.href='navegador_material.php?vista=1&proveedorB='+proveedorB+'&nombreB='+nombreB+'&principioActivoB='principioActivoB+'&barrasB='+codBarrasB;
}*/		
function cambiar_vista(f){
	var modo_vista=document.getElementById('vista').value;
	location.href='navegador_material.php?vista='+modo_vista;
}
	</script>

<?php	
	
	echo "<h1>Productos</h1>";

	echo "<form method='post' action=''>";

	$globalAlmacen=$_COOKIE["global_almacen"];
	//$vista=0;
	if(!isset($_GET['vista'])){
		$vista=0;
	}

	$banderaFiltro=0;

	$sql="select m.codigo_material, m.descripcion_material, m.estado, 
		(select e.nombre_empaque from empaques e where e.cod_empaque=m.cod_empaque), 
		(select f.nombre_forma_far from formas_farmaceuticas f where f.cod_forma_far=m.cod_forma_far), 
		(select pl.nombre_linea_proveedor from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor),
		(select t.nombre_tipoventa from tipos_venta t where t.cod_tipoventa=m.cod_tipoventa), m.cantidad_presentacion, m.principio_activo, bandera_venta_unidades, m.codigo_barras, m.producto_controlado, m.codigo_anterior
		from material_apoyo m
		where m.estado='1' ";
	if($vista==1)
	{	$sql="select m.codigo_material, m.descripcion_material, m.estado, 
		(select e.nombre_empaque from empaques e where e.cod_empaque=m.cod_empaque), 
		(select f.nombre_forma_far from formas_farmaceuticas f where f.cod_forma_far=m.cod_forma_far), 
		(select pl.nombre_linea_proveedor from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor),
		(select t.nombre_tipoventa from tipos_venta t where t.cod_tipoventa=m.cod_tipoventa), m.cantidad_presentacion, m.principio_activo, bandera_venta_unidades, m.codigo_barras, m.producto_controlado, m.codigo_anterior
		from material_apoyo m
		where m.estado='0' ";
	}

	//nuevos filtros
  $proveedorB=0;$principioB=0;$nombreB="";$barrasB="";$precioAbiertoB=0;
  $codigoProductoB=0;$productoControladoB=0;

	if(isset($_GET['codigoProductoB'])&&$_GET['codigoProductoB']!=0){
      $codigoProductoB=$_GET["codigoProductoB"];
      $sql.=" and m.codigo_material = '$codigoProductoB' ";
      $banderaFiltro=1;
  }
  if(isset($_GET['proveedorB'])&&$_GET['proveedorB']!=0){
      $proveedorB=$_GET["proveedorB"];
      $sql.=" and m.cod_linea_proveedor in (select cod_linea_proveedor from proveedores_lineas p where p.cod_proveedor='$proveedorB') ";
      $banderaFiltro=1;
  }
  if(isset($_GET['nombreB'])&&$_GET['nombreB']!=""){
      $nombreB=$_GET['nombreB'];
      $sql.=" and m.descripcion_material like '%$nombreB%' ";
      $banderaFiltro=1;
  }
  if(isset($_GET['principioB'])&&$_GET['principioB']!=""){
      $principioB=$_GET['principioB'];
      $sql.=" and m.principio_activo like '%$principioB%' ";
      $banderaFiltro=1;
  }
  if(isset($_GET['barrasB'])&&$_GET['barrasB']!=""){
      $barrasB=$_GET['barrasB'];
      $sql.=" and m.codigo_barras like '%$barrasB%'";   
      $banderaFiltro=1;
  }
  if(isset($_GET['precioAbiertoB'])&&$_GET['precioAbiertoB']!=""){
      $precioAbiertoB=$_GET['precioAbiertoB'];
      if($precioAbiertoB==1){
	      $sql.=" and m.precio_abierto=1 ";   
      }
      $banderaFiltro=1;
  }
  if(isset($_GET['productoControladoB'])&&$_GET['productoControladoB']!=""){
      $productoControladoB=$_GET['productoControladoB'];
      if($productoControladoB==1){
	      $sql.=" and m.producto_controlado=1 ";   
      }
      $banderaFiltro=1;
  }

	$sql.=" order by m.descripcion_material ";
	
	if($banderaFiltro==0){
		$sql.=" limit 0,100 ";
	}	
	//echo $sql;
	
	$resp=mysqli_query($enlaceCon,$sql);
	
	echo "<table align='center' class='texto'><tr><th>Ver Productos:</th>
	<th>
		<select name='vista' id='vista' class='selectpicker' data-style='btn btn-success' onChange='cambiar_vista(this.form)'>";
	if($vista==0)	echo "<option value='0' selected>Activos</option><option value='1'>Inactivos</option>";
	if($vista==1)	echo "<option value='0'>Activos</option><option value='1' selected>Inactivos</option>";
	echo "</select>";
	echo "</th></tr></table>";	
	
	echo "<div class='divBotones'>
		<input type='button' value='Adicionar' name='adicionar' class='boton' onclick='enviar_nav(this.form)'>
		<a href='#' class='boton-verde' onclick='mostrarBusqueda()'><img src='imagenes/buscar2.png' width='25'></img></a>
		</div>";
	
	echo "<center><table class='texto'>";
	echo "<tr><th>-</th><th>Codigo</th><th>Nombre Producto</th>
		<th>Presentacion</th><th>Venta Solo<br> Caja Entera</th><th>Distribuidor</th>
		<th>Precio</th><th>Principio Activo</th><th>Codigo<br>Interno</th><th>Controlado</th>
		<th>Stock</th><th>&nbsp;</th></tr>";
	
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
		$ventaSoloCajas=$dat[9];
		$barCode=$dat[10];
		$productoControlado=$dat[11];
		$codigoInterno=$dat[12];

		$txtProductoControlado="";
		if($productoControlado==1){
			$txtProductoControlado="<img src='imagenes/peligro.png' width='40px'>";
		}

		$imgVentaSoloCajas="-";
		if($ventaSoloCajas==1){
			$imgVentaSoloCajas="<img src='imagenes/bien.jpg' width='20'>";
		}

		$precioProducto=precioProducto($enlaceCon,$codigo);
		$precioProducto=formatonumeroDec($precioProducto);
		
		$txtAccionTerapeutica="";
		$sqlAccion="select a.nombre_accionterapeutica from acciones_terapeuticas a, material_accionterapeutica m
			where m.cod_accionterapeutica=a.cod_accionterapeutica and 
			m.codigo_material='$codigo'";
		$respAccion=mysqli_query($enlaceCon,$sqlAccion);
		while($datAccion=mysqli_fetch_array($respAccion)){
		//while($datAccion=i($respAccion)){
			$nombreAccionTerX=$datAccion[0];
			$txtAccionTerapeutica=$txtAccionTerapeutica." - ".$nombreAccionTerX;
		}
		
		$stockActual=stockProducto($enlaceCon, $globalAlmacen, $codigo);
		$stockActualF=formatonumero($stockActual);
		$txtEliminarProducto="";
		if($stockActual==0){
			$stockActualF="-";
			$txtEliminarProducto="<a href='javascript:deleteProducto($codigo,\"$nombreProd\");'><img src='imagenes/eliminarproceso.gif' width='25'></a>"; 
		}

		echo "<tr><td align='center'>$indice_tabla</td>
		<td>$codigo</td>
		<td>$nombreProd</td>
		<td>$cantPresentacion</td>
		<td>$imgVentaSoloCajas</td>
		<td>$nombreLinea</td>
		<td>$precioProducto</td>
		<td>$principioActivo</td>
		<td>$codigoInterno</td>
		<td align='center'>$txtProductoControlado</td>
		<td>$stockActualF</td>

		<td align='center'>
			<a href='editar_material_apoyo.php?cod_material=$codigo&pagina_retorno=0'><img src='imagenes/edit.png' width='25'></a>
			$txtEliminarProducto
			<div id='divDelete$codigo'></div>
		</td>
		</tr>";
		$indice_tabla++;
	}
	echo "</table></center><br>";
	
		echo "<div class='divBotones'>
		<input type='button' value='Adicionar' name='adicionar' class='boton' onclick='enviar_nav()'>
		<a href='#' class='boton-verde' onclick='mostrarBusqueda()'><img src='imagenes/buscar2.png' width='25'></img></a>

		</div>";
		
?>



<div id="divRecuadroExt" style="background-color:#666; position:absolute; width:850px; height: 600px; top:30px; left:150px; visibility: hidden; opacity: .70; -moz-opacity: .70; filter:alpha(opacity=70); -webkit-border-radius: 20px; -moz-border-radius: 20px; z-index:2;">
</div>

<div id="divboton" style="position: absolute; top:20px; left:970px;visibility:hidden; text-align:center; z-index:3">
	<a href="javascript:Hidden();"><img src="imagenes/cerrar4.png" height="45px" width="45px"></a>
</div>

<div id="divProfileData" style="background-color:#FFF; width:800px; height:550px; position:absolute; top:50px; left:170px; -webkit-border-radius: 20px; 	-moz-border-radius: 20px; visibility: hidden; z-index:2;">
  	<div id="divProfileDetail" style="visibility:hidden; text-align:center; height:545px; overflow-y: scroll;">
		<table align='center' class="texto">
			<tr><th colspan="2">Proveedor</th></tr>
			<tr>
			<td colspan="2">
				<select name='proveedorBusqueda' id="proveedorBusqueda" class="selectpicker form-control" data-style="btn btn-success" data-live-search="true" style="width:600px">
			<?php
			$sqlTipo="SELECT p.cod_proveedor,p.nombre_proveedor from proveedores p where p.cod_proveedor>0 order by 2;";
			$respTipo=mysqli_query($enlaceCon,$sqlTipo);
			echo "<option value='0'>--</option>";
			while($datTipo=mysqli_fetch_array($respTipo)){
				$codTipoMat=$datTipo[0];
				$nombreTipoMat=$datTipo[1];
				if($codTipoMat==$gr){
				  echo "<option value=$codTipoMat selected>$nombreTipoMat</option>";	
				}else{
					echo "<option value=$codTipoMat>$nombreTipoMat</option>";
				}
			}
			?>
			</select>
			</td>
			</tr>

			<tr>
				<th>Codigo de Producto</th>
				<th>Codigo de Barras</th>
			</tr>
			<tr>
			<td>
				<div class="codigo-barras div-center">
					<input type='text' class="form-codigo-barras" name='itemCodigoBusqueda' id="itemCodigoBusqueda" class="textomedianorojo" placeholder="Ingrese el codigo" value="<?=$nm?>">
				</div>
			</td>
			<td>
					<div class="codigo-barras div-center">
               <input type="text" class="form-codigo-barras" id="input_codigo_barras" placeholder="Ingrese el codigo de barras." autofocus autocomplete="off">
         </div>
     	</td>
			</tr>

			<tr><th colspan="2">Nombre Producto</th></tr>
			<tr>
			<td colspan="2">
				<input type='text' style="width:100%" name='itemNombreBusqueda' id="itemNombreBusqueda" class="textomedianorojo"  value="<?=$nm?>">
			</td>
			</tr>
			<tr><th colspan="2">Principio Activo</th></tr>
			<tr>
			<td colspan="2">
				<input type='text' style="width:100%" name='principioActivoBusqueda' id="principioActivoBusqueda" class="textomedianorojo"  value="<?=$nm?>">
			</td>
			</tr>
			<tr></tr>
			<tr>
				<th style="text-align:center;">Precio Abierto?
				</th>
				<td><select name="precioAbiertoBusqueda" id="precioAbiertoBusqueda" class="selectpicker">
					<option value="0" selected>NO</option>
					<option value="1">SI</option>
				</select></td>
			</tr>
			<tr>
				<th style="text-align:center;">Producto Controlado
				</th>
				<td><select name="productoControladoBusqueda" id="productoControladoBusqueda" class="selectpicker" data-style="btn btn-success">
					<option value="0" selected>NO</option>
					<option value="1">SI</option>
				</select></td>
			</tr>
		</table>
		<div class="div-center">
             <input type='button' class='boton-rojo' value='Buscar Producto' id="btnBusqueda" onClick="enviar_buscador();">
		</div>
	
	</div>
</div>


</form>
