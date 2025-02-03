<html>
    <head>
        <title>Salida</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!--script type="text/javascript" src="lib/js/xlibPrototipoSimple-v0.1.js"></script-->
        <script type="text/javascript" src="lib/js/jquery-3.2.1.min.js"></script>

<?php

$indexGerencia=1;

require('conexionmysqli.inc');
require('estilos_almacenes.inc');
require('funciones.php');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

$usuarioVentas=$_COOKIE['global_usuario'];
$globalAgencia=$_COOKIE['global_agencia'];
$globalAlmacen=$_COOKIE['global_almacen'];


// CODIGO DE COTIZACIÓN
$cod_cotizacion = empty($_GET['codigo']) ? '' : $_GET['codigo'];
$sqlCotizacion = "SELECT c.razon_social, c.nit, c.observaciones, c.descuento, c.cod_cliente
					FROM cotizaciones c
					WHERE c.cod_salida_almacenes = '$cod_cotizacion'";
$respUsd=mysqli_query($enlaceCon,$sqlCotizacion);
$cab_razon_social = '';	
$cab_nit 		  = '';	
$cab_observacion  = '';	
$cab_descuento 	  = 0;	
$cab_cod_cliente  = 0;	
while($rowCot=mysqli_fetch_array($respUsd)){
	$cab_razon_social = $rowCot['razon_social'];
	$cab_nit 		  = $rowCot['nit'];	
	$cab_observacion  = $rowCot['observaciones'];	
	$cab_descuento    = $rowCot['descuento'];
	$cab_cod_cliente  = $rowCot['cod_cliente'];	
}

//echo "cotizacion: ".$cod_cotizacion;

?>
		
<script type='text/javascript' language='javascript'>
function nuevoAjax()
{	var xmlhttp=false;
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

function listaMateriales(f){

	var stock=0;
	if($("#solo_stock").is(":checked")){
		stock=1;
	}

	var contenedor;
	var codTipo=f.itemTipoMaterial.value;
	var nombreItem=f.itemNombreMaterial.value;
	var tipoSalida=(f.tipoSalida.value);
	contenedor = document.getElementById('divListaMateriales');
	
	var arrayItemsUtilizados=new Array();	
	var i=0;
	for(var j=1; j<=num; j++){
		if(document.getElementById('materiales'+j)!=null){
			console.log("codmaterial: "+document.getElementById('materiales'+j).value);
			arrayItemsUtilizados[i]=document.getElementById('materiales'+j).value;
			i++;
		}
	}
	
	ajax=nuevoAjax();
	ajax.open("GET", "ajaxListaMateriales.php?codTipo="+codTipo+"&nombreItem="+nombreItem+"&arrayItemsUtilizados="+arrayItemsUtilizados+"&tipoSalida="+tipoSalida+"&stock="+stock,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText
		}
	}
	ajax.send(null)
}

function ajaxTipoDoc(f){
	var contenedor;
	contenedor=document.getElementById("divTipoDoc");
	ajax=nuevoAjax();
	var codTipoSalida=(f.tipoSalida.value);
	ajax.open("GET", "ajaxTipoDoc.php?codTipoSalida="+codTipoSalida,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText
		}
	}
	ajax.send(null);
}


function ajaxPesoMaximo(codVehiculo){
	var contenedor;
	contenedor=document.getElementById("divPesoMax");
	ajax=nuevoAjax();
	var codVehiculo=codVehiculo;
	ajax.open("GET", "ajaxPesoMaximo.php?codVehiculo="+codVehiculo,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText
		}
	}
	ajax.send(null);
}

function ajaxNroDoc(f){
	var contenedor;
	contenedor=document.getElementById("divNroDoc");
	ajax=nuevoAjax();
	var codTipoDoc=(f.tipoDoc.value);
	ajax.open("GET", "ajaxNroDoc.php?codTipoDoc="+codTipoDoc,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText
		}
	}
	ajax.send(null);
}

function actStock(indice){
	var contenedor;
	contenedor=document.getElementById("idstock"+indice);
	var codmat=document.getElementById("materiales"+indice).value;
    var codalm=document.getElementById("global_almacen").value;
	
	ajax=nuevoAjax();
	ajax.open("GET", "ajaxStockSalidaMateriales.php?codmat="+codmat+"&codalm="+codalm+"&indice="+indice,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null);
	
	
}

function buscarMaterial(f, numMaterial){
	f.materialActivo.value=numMaterial;
	document.getElementById('divRecuadroExt').style.visibility='visible';
	document.getElementById('divProfileData').style.visibility='visible';
	document.getElementById('divProfileDetail').style.visibility='visible';
	document.getElementById('divboton').style.visibility='visible';
	
	document.getElementById('divListaMateriales').innerHTML='';
	document.getElementById('itemNombreMaterial').value='';	
	document.getElementById('itemNombreMaterial').focus();
}
function Hidden(){
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';

}
function setMateriales(f, cod, nombreMat, stockProducto){
	var numRegistro=f.materialActivo.value;
	var nombre_material_x, fecha_venc_x, cantidad_presentacionx, venta_solo_cajax, precio_productox;
	var datos_material=nombreMat.split("####");
 	nombre_material_x=datos_material[0];  
 	fecha_venc_x=datos_material[1];  
 	cantidad_presentacionx=datos_material[2];
 	venta_solo_cajax=datos_material[3];
	precio_productox=datos_material[4];
	
	document.getElementById('materiales'+numRegistro).value=cod;

	document.getElementById('cod_material'+numRegistro).innerHTML=nombre_material_x;

	document.getElementById('div_idprecio'+numRegistro).innerHTML=precio_productox;
	
	
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';


	document.getElementById("cantidad_unitaria"+numRegistro).focus();

	actStock(numRegistro);
}

num=0;
cantidad_items=0;

function mas(obj) {
	if(num>=50){
		alert("No puede registrar mas de 50 items en una nota.");
	}else{
		//aca validamos que el item este seleccionado antes de adicionar nueva fila de datos
		var banderaItems0=0;
		for(var j=1; j<=num; j++){
			if(document.getElementById('materiales'+j)!=null){
				if(document.getElementById('materiales'+j).value==0){
					banderaItems0=1;
				}
			}
		}
		//fin validacion
		console.log("bandera: "+banderaItems0);

		if(banderaItems0==0){
			num++;
			cantidad_items++;
			console.log("num: "+num);
			console.log("cantidadItems: "+cantidad_items);
			fi = document.getElementById('fiel');
			contenedor = document.createElement('div');
			contenedor.id = 'div'+num;  
			fi.type="style";
			fi.appendChild(contenedor);
			var div_material;
			div_material=document.getElementById("div"+num);			
			ajax=nuevoAjax();
			ajax.open("GET","ajaxMaterialSalida.php?codigo="+num,true);
			ajax.onreadystatechange=function(){
				if (ajax.readyState==4) {
					div_material.innerHTML=ajax.responseText;
					buscarMaterial(form1, num);
				}
			}		
			ajax.send(null);
		}

	}
	
}


function masMultiple(form) {
	var banderaItems0=0;
	console.log("bandera: "+banderaItems0);
	var numFilas=num;
	console.log("numFilas: "+numFilas);

	menos(numFilas);
	console.log("numFilasActualizado: "+numFilas);

	var productosMultiples=new Array();		
	for(i=0;i<=form.length-1;i++){
		if(form.elements[i].type=='checkbox'){  	   
			if(form.elements[i].checked==true && form.elements[i].name.indexOf("idchk")!==-1 ){ 
				cadena=form.elements[i].value;
				console.log("i: "+i+" cadena: "+cadena+" name: "+form.elements[i].name);
				productosMultiples.push(cadena);
				banderaItems0=1;
				num++;
			}
		}
	}
	num--;

	console.log("bandera: "+banderaItems0);
	if(banderaItems0==1){
		num++;
		div_material_linea=document.getElementById("fiel");			

		/*recuperamos las cantidades de los otros productos*/
		var inputs = $('form input[name^="cantidad_unitaria"]');
		var arrayCantidades=[];
		inputs.each(function() {
		  var name = $(this).attr('name');
		  var value = $(this).val();
		  var index = name.charAt(name.length - 1);
		  console.log("index: "+index);
		  arrayCantidades.push([name,value,index]);
		});
		/*fin recuperar*/
		/*recuperamos los stocks de los otros productos*/
		var inputs = $('form input[name^="stock"]');
		var arrayStocks=[];
		inputs.each(function() {
		  var name = $(this).attr('name');
		  var value = $(this).val();
		  var index = name.charAt(name.length - 1);
		  console.log("index: "+index);
		  arrayStocks.push([name,value,index]);
		});
		/*fin recuperar*/

		ajax=nuevoAjax();
		ajax.open("POST","ajaxMaterialSalidaMultiple.php?codigo="+numFilas+"&productos_multiple="+productosMultiples,true);
		ajax.onreadystatechange=function(){
			if (ajax.readyState==4) {
				div_material_linea.innerHTML=div_material_linea.innerHTML+ajax.responseText;
			}
			for (x=0;x<arrayCantidades.length;x++) {
				console.log("Iniciando recorrido Matriz");
				var name_set_stock=arrayStocks[x][0];
				var value_set_stock=arrayStocks[x][1];
				var index_set_stock=arrayStocks[x][2];
				document.getElementById(name_set_stock).value=value_set_stock;

				var name_set=arrayCantidades[x][0];
				var value_set=arrayCantidades[x][1];
				var index_set=arrayCantidades[x][2];
				document.getElementById(name_set).value=value_set;
			}
		}		
		ajax.send(null);
	}
	console.log("CONTROL NUM: "+num);
	Hidden();
}	

function marcarDesmarcar(f,elem){
	 var i;
      var j=0;
	 if(elem.checked==true){      	       
      for(i=0;i<=f.length-1;i++){
       if(f.elements[i].type=='checkbox'){       
		f.elements[i].checked=true;
        }
      }	
    }else{
		for(i=0;i<=f.length-1;i++){
       if(f.elements[i].type=='checkbox'){       
		f.elements[i].checked=false;
        }
      }	
	}
}

function menos(numero) {
	cantidad_items--;
	console.log("TOTAL ITEMS: "+num);
	console.log("NUMERO A DISMINUIR: "+numero);
	if(numero==num){
		num=parseInt(num)-1;
 	}
	fi = document.getElementById('fiel');
	fi.removeChild(document.getElementById('div'+numero));
	//totales();
}

function pressEnter(e, f){
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==13){
		document.getElementById('itemNombreMaterial').focus();
		listaMateriales(f);
		return false;
	}
}
	
function validar(f){

	f.cantidad_material.value=num;
	var tipoSalida=document.getElementById("tipoSalida").value;
	console.log("tipoSalida: "+tipoSalida);
	var mensajeGuardar="";
		
	/**** Recuperamos el nombre del Cliente  *****/
	const comboCliente = document.getElementById('cliente');
	const nombreClienteSelect = comboCliente.options[comboCliente.selectedIndex].text;
	document.getElementById("nombre_cliente").value=nombreClienteSelect;  

	var cantidadItems=num;
	if(tipoSalida==0){
		Swal.fire("Tipo de Salida!", "El tipo de Salida no puede estar vacio.", "warning");
		return(false);
	}else{
		var tipoDoc=document.getElementById("tipoDoc").value;
		var almacenDestino=document.getElementById("almacen").value;
		if(tipoDoc==0){
			Swal.fire("Tipo de Documento!", "El tipo de Documento no puede estar vacio.", "warning");
			return(false);
		}
		console.log("tipoDoc: " + tipoDoc);
	}

	/* Validamos el almacen cuando es traspaso. */
	if(almacenDestino==0 && tipoSalida==1000){   
		Swal.fire("Almacen Destino!", "El Almacen Destino no puede estar vacio.", "warning");
		return(false);
	}
	
	/******** Validacion productos vacios ********/
	var banderaValidacionDetalle=0;
	var inputs = $('form input[name^="materiales"]');
	inputs.each(function() {
  	var value = $(this).val();
  	if(value==0 || value==""){
			banderaValidacionDetalle=1;
  	}else{
  	}
	});
	if(banderaValidacionDetalle==1){
		Swal.fire("Productos!", "Debe seleccionar un producto para cada fila.", "info");
		return(false);
	}
	/******** Fin validacion productos vacios ********/

	/******** Validacion Cantidades ********/
	var sumaCantidades=0;
	var banderaValidacionDetalle=0;
	var inputs = $('form input[name^="cantidad_unitaria"]');
	inputs.each(function() {
  	var value = $(this).val();
  	if(value<=0 || value==""){
			banderaValidacionDetalle=1;
  	}else{
  		sumaCantidades=sumaCantidades+value;
  	}
	});
	if(banderaValidacionDetalle==1){
		Swal.fire("Cantidades!", "Hay algún campo con la cantidad vacia o en cero.", "info");
		return(false);
	}
	if(sumaCantidades==0){
		Swal.fire("Cantidades!", "No hay ningun producto en la Salida.", "info");
		return(false);
	}
	/******** Fin validacion Cantidades ********/


	/**************************************************/
	/************ Validacion de Stocks ****************/
	/**************************************************/
	var banderaValidacionStock=document.getElementById("bandera_validacion_stock").value;
	console.log("bandera stocks valid: "+banderaValidacionStock);
	banderaValidacionDetalle=0;
	var inputs_stocks = $('form input[name^="stock"]');
	var inputs_cantidades = $('form input[name^="cantidad_unitaria"]');
	for (var i = 0; i < inputs_stocks.length; i++) {
  	var cantidadFila = parseFloat(inputs_cantidades[i].value);
  	var stockFila = parseFloat(inputs_stocks[i].value);
  	if(banderaValidacionStock==1){
  		if(cantidadFila>stockFila){
  			banderaValidacionDetalle=1;
  		}
  	}else{ 				//para todos los otros casos de stocks
  		if(cantidadFila>stockFila){
  			banderaValidacionDetalle=2;
  		}
  	}
  	console.log("cantidadStock: "+stockFila);
  	console.log( "cantidadFila: "+cantidadFila);
	}
	console.log("validacionDetalleStocks:"+banderaValidacionDetalle);
	if(banderaValidacionDetalle==1){
		Swal.fire("Stocks!", "NO puede sacar cantidades mayores al stock.", "error");
		return(false);
	}
	if(banderaValidacionDetalle==2){
		mensajeGuardar="La venta provocara NEGATIVO en el STOCK!";				
	}
	/**************************************************/
	/************ Fin Validacion de Stocks ************/
	/**************************************************/

	Swal.fire({
		title: '¿Esta seguro de Realizar la Salida?',
		text: mensajeGuardar,
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Si, Realizar Salida!'
		}).then((result) => {
				console.log(result);
				console.log("resultado: "+result.value);
		    if (result.value) {
		      console.log("Enviando");
		      document.getElementById("btsubmit").value = "Enviando...";
					document.getElementById("btsubmit").disabled = true;   
					document.forms[0].submit();
		    }if(result.dismiss){
						console.log("Cancelando....");
						return false;
		    }
	});		

	return false;
}
	
	
</script>	
<?php
echo "<body>";

$fecha=date("d/m/Y");

$sql="select nro_correlativo from salida_almacenes where cod_almacen='$global_almacen' order by cod_salida_almacenes desc";
$resp=mysqli_query($enlaceCon,$sql);
$dat=mysqli_fetch_array($resp);
$num_filas=mysqli_num_rows($resp);
if($num_filas==0)
{   $codigo=1;
}
else
{   $codigo=$dat[0];
    $codigo++;
}
?>
<form action='guardarSalidaMaterial.php' method='POST' name='form1' onsubmit="return validar(this)">
<h1>Registrar Salida de Almacen</h1>

<input type="hidden" id="almacen_origen" name="almacen_origen" value="<?=$globalAlmacen?>">
<input type="hidden" id="sucursal_origen" name="sucursal_origen" value="<?=$globalAgencia?>">
<input type="hidden" id="no_venta" name="no_venta" value="100">
<input type="hidden" id="bandera_validacion_stock" name="bandera_validacion_stock" value="<?=obtenerValorConfiguracion($enlaceCon,4)?>">
<input type="hidden" id="cod_cotizacion" name="cod_cotizacion" value="<?=$cod_cotizacion?>">



<table class='texto' align='center' width='90%'>
<tr>
	<th align='center'>
	Fecha: <input type='date' class='texto' value='<?php echo $fecha?>' id='fecha' name='fecha'>
	</th>
	<th>Tipo Salida:
		<select name='tipoSalida' id='tipoSalida' onChange='ajaxTipoDoc(form1)' class="selectpicker" data-style="btn btn-success">
		<option value="0">--------</option>
		<?php
			$sqlTipo="select cod_tiposalida, nombre_tiposalida from tipos_salida where cod_tiposalida<>1001 order by 2";
			$respTipo=mysqli_query($enlaceCon,$sqlTipo);
			while($datTipo=mysqli_fetch_array($respTipo)){
				$codigo=$datTipo[0];
				$nombre=$datTipo[1];
		?>
				<option value='<?php echo $codigo?>'><?php echo $nombre?></option>
		<?php		
			}
		?>
		</select>
	</th>
	<th>Tipo Documento:
		<div id='divTipoDoc'>
			
		</div>
	</th>
	<th>Nro.:
		<div id='divNroDoc' class='textogranderojo'>
		</div>
	</th>

	<th>Almacen Destino:
	<select name='almacen' id='almacen' class='texto'>
		<?php
			$sql3="select cod_almacen, nombre_almacen from almacenes where cod_almacen<>'$global_almacen' order by nombre_almacen";
			$resp3=mysqli_query($enlaceCon,$sql3);
		?>
				<option value="0">--</option>
		<?php			
			while($dat3=mysqli_fetch_array($resp3)){
				$cod_almacen=$dat3[0];
				$nombre_almacen="$dat3[1] $dat3[2] $dat3[3]";
		?>
				<option value="<?php echo $cod_almacen?>"><?php echo $nombre_almacen?></option>
		<?php		
			}
		?>
	</select>
	</th>

</tr>
<tr>
	<th colspan="2" align='center' id='divCliente'>Cliente
			<?php
			$vistaCliente=1;
			$vistaCliente=obtenerValorConfiguracion($enlaceCon, -1);
			?>
			<input type="hidden" id="vistaCliente" name="vistaCliente" value="<?=$vistaCliente;?>">
			<input type="hidden" id="nombre_cliente" name="nombre_cliente" value="-">
			<!--span class="input-group-btn" style="position:absolute;width:100px !important;"-->		
				<select name='cliente' class='selectpicker form-control' data-live-search="true" id='cliente' onChange='ajaxRazonSocialCliente(this.form);' required data-style="btn btn-rose">
					<option value='146'>Seleccionar Cliente/Paciente</option>
					<?php
						if($vistaCliente==1){
							$sql = "SELECT c.cod_cliente, c.nombre_cliente, c.nit_cliente
								FROM clientes c WHERE c.cod_cliente = '$cab_cod_cliente' AND c.cod_cliente != 146";
							$resp=mysqli_query($enlaceCon,$sql);
							while($rowCot=mysqli_fetch_array($resp)){ 
					?>
						<option value='<?=$rowCot['cod_cliente']?>' selected><?=$rowCot['nombre_cliente']?></option>
					<?php
							}
					?>
					<?php
						}else{
							$url = obtenerValorConfiguracion($enlaceCon,-2);
							$response = file_get_contents($url);
							if ($response !== false) {
							    $data = json_decode($response, true);
							    if (json_last_error() === JSON_ERROR_NONE) {
							        foreach ($data as $paciente) {
							            ?>
							            <option value="<?=($paciente['codigo'])==$cab_cod_cliente?"selected":"";?>" ><?=$paciente['nombre_completo'];?></option>
							            <?php
							        }
							    } 
							} else {
							?>
					    <option value='-1' selected>Error en el WS</option>
							<?php
							}						
						}
						?>
				</select>
	</th>

	<th align='center' colspan="3">
		Observaciones:
		<input type='text' class='texto' name='observaciones' value='' size='80' rows="2">
	</th>

</tr>
	
</table>

<br>

<fieldset id="fiel" style="width:100%;border:0;">
	<table align="center" class="texto" width="100%" id="data0" border="1">
	<tr>
		<td align="center" colspan="9">
			<b>Detalle de la Transaccion   </b><input class="boton" type="button" value="Agregar (+)" onclick="mas(this)" />
		</td>
	</tr>	
	<tr align="center">
		<td width="50%"><b>Material</b></td>
		<td width="20%"><b>Stock</b></td>
		<td width="10%"><b>Precio</b></td>
		<td width="10%"><b>Cantidad</b></td>
		<td width="10%">&nbsp;</td>
	</tr>
	</table>
	<!-- ITEMS DE COTIZACIÓN -->
	<?php
	$nro_materialActivo 	= 0;
	$cotizacion_total_final = 0;
	if(!empty($cod_cotizacion)){
		$sql2="SELECT cd.cod_material, CONCAT(m.descripcion_material,' - ', (select concat(p.nombre_proveedor,'-',pl.nombre_linea_proveedor)as nombre_proveedor
		from proveedores p, proveedores_lineas pl where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor), ' (', cd.cod_material, ') - ', (SELECT concat(FORMAT(id.costo_almacen,1),'0',FORMAT((id.costo_almacen*1.25),1)) from ingreso_almacenes i, ingreso_detalle_almacenes id where i.cod_ingreso_almacen=id.cod_ingreso_almacen and 
						i.ingreso_anulado=0 and i.cod_tipoingreso in (999,1000) and id.cod_material=cd.cod_material order by i.cod_ingreso_almacen desc limit 1)) as nombre_material, m.cantidad_presentacion, cd.cantidad_unitaria, cd.monto_unitario, cd.precio_unitario, cd.descuento_unitario
				FROM cotizaciones_detalle cd
				LEFT JOIN material_apoyo m ON m.codigo_material = cd.cod_material
				WHERE cd.cod_salida_almacen = '$cod_cotizacion'";
		$resp2=mysqli_query($enlaceCon,$sql2);
		while($rawCotizacion = mysqli_fetch_array($resp2)){
			$nro_materialActivo++;
			
			$stockProducto 		  = stockProducto($enlaceCon,$globalAlmacen, $rawCotizacion['cod_material']);
			$cantidadPresentacion = $rawCotizacion['cantidad_presentacion'];
			$cotizacion_cantidad  = (int)$rawCotizacion['cantidad_unitaria'];
			$cotizacion_total  	  = $rawCotizacion['monto_unitario'];
			$cotizacion_precio_unitario    = $rawCotizacion['precio_unitario'];
			$cotizacion_precio_unitario = round($cotizacion_precio_unitario,2);
			$cotizacion_descuento_unitario = $rawCotizacion['descuento_unitario'];
	?>
	<div id="div<?=$nro_materialActivo?>">

		<input type="hidden" value="1000" name="<?=$globalAlmacen?>" id="<?=$globalAlmacen?>">
		<table border="0" align="center" width="100%"  class="texto" id="data<?php echo $nro_materialActivo?>" >
			<tr bgcolor="#FFFFFF">
			<td>
				<a href="javascript:buscarMaterial(form1, <?php echo $nro_materialActivo;?>)"><img src='imagenes/buscar2.png' title="Buscar Producto" width="30"></a>
			</td>
			<td width="50%" align="left">
				<input type="hidden" name="materiales<?php echo $nro_materialActivo;?>" id="materiales<?php echo $nro_materialActivo;?>" value="<?=$rawCotizacion['cod_material'];?>">
				<div id="cod_material<?php echo $nro_materialActivo;?>" class='textomedianonegro'>
					<?=$rawCotizacion['nombre_material']?>
				</div>
			</td>
			<td width="20%" align="center">
				<div id='idstock<?php echo $nro_materialActivo;?>'>
					<input type="number" id="stock<?=$nro_materialActivo?>" name="stock<?=$nro_materialActivo?>" value="<?=$stockProducto?>" readonly="" size="5" style="height:20px;font-size:19px;width:80px;color:red;">
				</div>
			</td>
			<td width="10%" align="center">
				<div id='div_idprecio<?php echo $nro_materialActivo;?>' class='textomedianonegro'>
					<?=$cotizacion_precio_unitario;?>
				</div>
			</td>
			<td align="center" width="10%">
				<input class="inputnumber" type="number" min="1" id="cantidad_unitaria<?php echo $nro_materialActivo;?>" name="cantidad_unitaria<?php echo $nro_materialActivo;?>" value="<?=$cotizacion_cantidad?>" required> 
			</td>
			<td align="center"  width="10%" ><input class="boton2peque" type="button" value="-" onclick="menos(<?php echo $nro_materialActivo;?>)" /></td>

			</tr>
		</table>
	
	</div>
	<?php
		}
	}
	?>
</fieldset>


<?php
echo "<div class='divBotones'>
	<input type='submit' class='boton' value='Guardar' id='btsubmit' name='btsubmit'>
	<input type='button' class='boton2' value='Cancelar' onClick='location.href=\"navegador_salidamateriales.php\"'>
</div>";

echo "</div>";
?>



<div id="divRecuadroExt" style="background-color:#666; position:absolute; width:900px; height: 400px; top:30px; left:150px; visibility: hidden; opacity: .70; -moz-opacity: .70; filter:alpha(opacity=70); -webkit-border-radius: 20px; -moz-border-radius: 20px; z-index:2; overflow: auto;">
</div>

<div id="divboton" style="position: absolute; top:20px; left:980px;visibility:hidden; text-align:center; z-index:3">
	<a href="javascript:Hidden();"><img src="imagenes/cerrar4.png" height="45px" width="45px"></a>
</div>

<div id="divProfileData" style="background-color:#FFF; width:850px; height:350px; position:absolute; top:50px; left:170px; -webkit-border-radius: 20px; 	-moz-border-radius: 20px; visibility: hidden; z-index:2; overflow: auto;">
  	<div id="divProfileDetail" style="visibility:hidden; text-align:center">
		<table align='center'>
			<tr><th>Linea</th><th>Material</th><th>&nbsp;</th></tr>
			<tr>
			<td><select class="textogranderojo" name='itemTipoMaterial' style="width:300px">
			<?php
			$sqlTipo="select pl.cod_linea_proveedor, CONCAT(p.nombre_proveedor,' - ',pl.nombre_linea_proveedor) from proveedores p, proveedores_lineas pl 
			where p.cod_proveedor=pl.cod_proveedor and pl.estado=1 order by 2;";
			$respTipo=mysqli_query($enlaceCon,$sqlTipo);
			echo "<option value='0'>--</option>";
			while($datTipo=mysqli_fetch_array($respTipo)){
				$codTipoMat=$datTipo[0];
				$nombreTipoMat=$datTipo[1];
				echo "<option value=$codTipoMat>$nombreTipoMat</option>";
			}
			?>

			</select>
			</td>
			<td>
				<input type='text' name='itemNombreMaterial' id='itemNombreMaterial' class="textogranderojo" onkeypress="return pressEnter(event, this.form);">
			</td>
			<td>
				<input type='button' class='boton' value='Buscar' onClick="listaMateriales(this.form)">
			</td>
			</tr>
			<tr>
				<td>
					<div class="custom-control custom-checkbox small float-left">
                    	<input type="checkbox" class="" id="solo_stock" checked="">
                    	<label class="text-dark font-weight-bold" for="solo_stock">&nbsp;&nbsp;&nbsp;Solo Productos con Stock</label>
                    	<input type="button" class="boton2peque" onclick="javascript:masMultiple(this.form);" value="Incluir Productos Seleccionados">
         			</div>
				</td>
			</tr>
		</table>
		<div id="divListaMateriales">
		</div>
	
	</div>
</div>

<script>
	num 		   = <?=$nro_materialActivo?>;
	cantidad_items = <?=$nro_materialActivo?>;
	console.log("NUM: "+num);
</script>

<input type='hidden' name='materialActivo' value="<?=$nro_materialActivo?>">
<input type='hidden' name='cantidad_material' value="<?=$nro_materialActivo?>">

</form>
</body>