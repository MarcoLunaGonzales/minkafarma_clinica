<?php

$indexGerencia=1;
require("conexionmysqli.inc");
require("estilos_almacenes.inc");
require("funciones.php");
?>

<html>
    <head>
        <title>Busqueda</title>
        <link  rel="icon"   href="imagenes/card.png" type="image/png" />
        <link href="assets/style.css" rel="stylesheet" />
		<style>
			input[type="number"],
			input[type="text"] {
				padding: 2px;
				border: 1px solid #ccc; 
				border-radius: 5px;
				box-shadow: 0 0 5px rgba(0, 0, 0, 0.1); 
				transition: border-color 0.3s, box-shadow 0.3s;
			}
			input[type="number"]:focus,
			input[type="text"]:focus {
				border-color: #007bff;
				box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
			}
			input[type="number"]:invalid,
			input[type="text"]:invalid {
				border-color: #ff0000;
			}
			select {
				padding: 2px;
				border: 1px solid #ccc;
				border-radius: 5px;
				background-color: #fff;
				color: #333;
				font-size: 16px;
				box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
				transition: border-color 0.3s, box-shadow 0.3s;
				-moz-appearance: none;
				appearance: none;
			}
			select:focus {
				border-color: #007bff;
				box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
			}
			select::-ms-expand {
				display: none;
			}
			select::before {
				content: '\25BC';
				position: absolute;
				top: 50%;
				right: 10px;
				transform: translateY(-50%);
				pointer-events: none;
			}
		</style>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
        <script type='text/javascript' language='javascript'>
		
function number_format(amount, decimals) {
    amount += ''; // por si pasan un numero en vez de un string
    amount = parseFloat(amount.replace(/[^0-9\.-]/g, '')); // elimino cualquier cosa que no sea numero o punto
    decimals = decimals || 0; // por si la variable no fue fue pasada
    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimals);
    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimals);
    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;
    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');
    return amount_parts.join('.');
}

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
function ajaxNroSalida(){
	var contenedor;
	var nroSalida = parseInt(document.getElementById('nro_salida').value);
	if(isNaN(nroSalida)){
		nroSalida=0;
	}
	contenedor = document.getElementById('divNroSalida');
	ajax=nuevoAjax();

	ajax.open("GET", "ajaxNroSalida.php?nroSalida="+nroSalida,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null)
}
function listaMateriales(f){
	var contenedor;
	var codTipo=f.itemTipoMaterial.value;
	var nombreItem=f.itemNombreMaterial.value;
	contenedor = document.getElementById('divListaMateriales');
	ajax=nuevoAjax();
	ajax.open("GET", "ajaxListaMaterialesIngreso.php?codTipo="+codTipo+"&nombreItem="+nombreItem,true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText;
		}
	}
	ajax.send(null)
}

function buscarMaterialLinea(f, numMaterial){
	f.prodItemActivo.value=numMaterial;
	document.getElementById('divRecuadroExt').style.visibility='visible';
	document.getElementById('divProfileData').style.visibility='visible';
	document.getElementById('divProfileDetail').style.visibility='visible';
	document.getElementById('divboton').style.visibility='visible';
	document.getElementById('divListaMateriales').innerHTML='';
	document.getElementById('itemNombreMaterial').value='';	
	document.getElementById('itemNombreMaterial').focus();	
}

function buscarMaterial(f, numMaterial){
	f.prodItemActivo.value=numMaterial;
	document.getElementById('divRecuadroExt').style.visibility='visible';
	document.getElementById('divProfileData').style.visibility='visible';
	document.getElementById('divProfileDetail').style.visibility='visible';
	document.getElementById('divboton').style.visibility='visible';
	document.getElementById('divListaMateriales').innerHTML='';
	document.getElementById('itemNombreMaterial').value='';	
	document.getElementById('itemNombreMaterial').focus();	
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
function buscarMaterialSelec(f, numMaterial){
	f.prodItemActivo.value=numMaterial;

	document.getElementById('divListaMateriales').innerHTML='';
	document.getElementById('itemNombreMaterial').value='';	
	document.getElementById('itemNombreMaterial').focus();	
}

function ver(elem){
	//alert(elem.value);
	}
function setMateriales(f, cod, nombreMat, cantidadpresentacion, precio, margenlinea){
	var numRegistro=f.prodItemActivo.value;
		
	document.getElementById('material'+numRegistro).value=cod;
	document.getElementById('cantidadpresentacion'+numRegistro).value=cantidadpresentacion;
	document.getElementById('cod_material'+numRegistro).innerHTML=nombreMat+" - <span class='textomedianonegro'>CP:"+cantidadpresentacion+"</span>";
	document.getElementById('divpreciocliente'+numRegistro).innerHTML="PrecioActual:"+number_format(precio,2);
	document.getElementById('precioclienteguardar'+numRegistro).value=number_format(precio,2);
	document.getElementById('margenlinea'+numRegistro).value=margenlinea;
	
	
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';

	document.getElementById("cantidad_unitaria"+numRegistro).focus();	
}
function setMaterialesSelec(f, cod, nombreMat, cantidadpresentacion, precio, margenlinea){
	var numRegistro=f.prodItemActivo.value;
//alert(numRegistro);
	document.getElementById('material'+numRegistro).value=cod;
	document.getElementById('cantidadpresentacion'+numRegistro).value=cantidadpresentacion;
	document.getElementById('cod_material'+numRegistro).innerHTML=nombreMat;
	document.getElementById('divpreciocliente'+numRegistro).innerHTML=number_format(precio,2);
	document.getElementById('precioclienteguardar'+numRegistro).value=number_format(precio,2);
	document.getElementById('margenlinea'+numRegistro).value=margenlinea;
}
function masSelec() {	
 	console.log("entrando masSelec num="+num);
	num++;
	fi = document.getElementById('fiel');
	contenedor = document.createElement('div');
	contenedor.id = 'div'+num;  
	fi.type="style";
	fi.appendChild(contenedor);
	var div_material;
	div_material=document.getElementById("div"+num);			
	ajax=nuevoAjax();
	ajax.open("GET","ajaxMaterial.php?codigo="+num,true);
	ajax.onreadystatechange=function(){
		if (ajax.readyState==4) {
			div_material.innerHTML=ajax.responseText;
			//buscarMaterial(form1, num);
			return (true);
		}
	}		
	ajax.send(null);
}	
function setSeleccionados(f){
	var i;
	var cadena="";
	var aux="";
	var corrnum="";
	var prodArray;
	 var sw=0;
	 var x=0;
	 var cont=1;
	 var sw=0;
	 var numRegistro;
	 numRegistro=f.prodItemActivo.value;
	//alert("numRegistro="+numRegistro);
	for(i=0;i<=f.length-1;i++){
    	if(f.elements[i].type=='checkbox'){  	   
			if(f.elements[i].checked==true){ 
				numRegistro=num;
				cadena=f.elements[i].value;
				console.log("i: "+i+" cadena: "+cadena+" name: "+f.elements[i].name);
				
				
				prodArray=new Array();
				prodArray =cadena.split("|");
				aux=prodArray[0]+prodArray[1]+prodArray[2]+prodArray[3]+prodArray[4];
			    //console.log("datoSelec"+prodArray[0]);
				if(sw==0){
					sw=1;
				}else{
					masSelec();
				}
				
				console.log("num: "+num);
				console.log("CodMaterialF: "+prodArray[0]);
				console.log("MaterialF: "+prodArray[1]);
				//console.log("material"+num+" = "+document.getElementById('material'+num).value);
				 
				// document.getElementById('material'+num).value=prodArray[0];
				// document.getElementById('cod_material'+num).innerHTML=prodArray[1];

				
				//document.getElementById('material2').value=10101010;
				//document.getElementById('cod_material2').innerHTML="vamos carajo";

				//setMateriales(f,prodArray[0], prodArray[1], prodArray[2], prodArray[3], prodArray[4]);
				//document.getElementById('material'+numRegistro).value=prodArray[1];
				//document.getElementById('material'+numRegistro).value=prodArray[1];
				
				//document.getElementById('cod_material'+numRegistro).innerHTML=prodArray[0];
				//document.getElementById('divpreciocliente'+num).innerHTML=number_format(precio,2);
				//document.getElementById('margenlinea'+num).value=margenlinea;
				//numRegistro;
				// setMaterialesSelec(f,prodArray[0], prodArray[1], prodArray[2], prodArray[3], prodArray[4]);
				 ////////////
				 //alert("hola"+num);
				// numRegistro=num*1;
				 /////////////
			}
        }
      }	
	//alert("numRegistro=="+numRegistro);
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';

}
	

function Hidden(){
	document.getElementById('divRecuadroExt').style.visibility='hidden';
	document.getElementById('divProfileData').style.visibility='hidden';
	document.getElementById('divProfileDetail').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';
	document.getElementById('divboton').style.visibility='hidden';
}
		
function enviar_form(f)
{   f.submit();
}
function fun13(cadIdOrg,cadIdDes)
{   var num=document.getElementById(cadIdOrg).value;
    num=(100-13)*num/100;
    document.getElementById(cadIdDes).value=num;
}

	var num=0;

	function modalMasLinea(form){
		buscarMaterialLinea(form1,0);
	}

	function masMultiple(form) {
		var banderaItems0=0;
		console.log("bandera: "+banderaItems0);
		var numFilas=num;

		menos(numFilas);

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
			  	var partes = name.split("cantidad_unitaria");
				var name_form = partes[0]; // Contiene todo antes de "cantidad_unitaria"
				var index_form = partes[1]; // Contiene todo después de "cantidad_unitaria"
			  	arrayCantidades.push([name,value,index_form]);
			});
			var inputs = $('form input[name^="precio_unitario"]');
			var arrayPreciosCaja=[];
			inputs.each(function() {
			  	var name = $(this).attr('name');
			  	var value = $(this).val();
			  	var partes = name.split("precio_unitario");
				var name_form = partes[0]; 
				var index_form = partes[1];
			  	arrayPreciosCaja.push([name,value,index_form]);
			});
			var inputs = $('form input[name^="fechaVenc"]');
			var arrayFV=[];
			inputs.each(function() {
			  	var name = $(this).attr('name');
			  	var value = $(this).val();
			  	var partes = name.split("fechaVenc");
				var name_form = partes[0]; 
				var index_form = partes[1];
			  	arrayFV.push([name,value,index_form]);
			});
			var inputs = $('form input[name^="descuento_porcentaje"]');
			var arrayDescuentoPorcentaje=[];
			inputs.each(function() {
			  	var name = $(this).attr('name');
			  	var value = $(this).val();
			  	var partes = name.split("descuento_porcentaje");
				var name_form = partes[0]; 
				var index_form = partes[1];
			  	arrayDescuentoPorcentaje.push([name,value,index_form]);
			});
			var inputs = $('form input[name^="preciocliente"]');
			var arrayPrecioCliente=[];
			inputs.each(function() {
			  	var name = $(this).attr('name');
			  	var value = $(this).val();
			  	var partes = name.split("preciocliente");
			  	if(!name.includes('precioclienteguardar') && !name.includes('precioclienteOf')){
					var name_form = partes[0]; 
					var index_form = partes[1];
				  	arrayPrecioCliente.push([name,value,index_form]);
			  	}
			});
			var inputs = $('form input[name^="precioclienteguardar"]');
			var arrayPrecioClienteGuardar=[];
			inputs.each(function() {
			  	var name = $(this).attr('name');
			  	var value = $(this).val();
			  	var partes = name.split("precioclienteguardar");
				var name_form = partes[0]; 
				var index_form = partes[1];
			  	arrayPrecioClienteGuardar.push([name,value,index_form]);
			});			
			console.log("Array Recuperado Cantidad: "+arrayCantidades);
			console.log("Array Recuperado Precios: "+arrayPreciosCaja);
			console.log("Array Recuperado FV: "+arrayFV);
			console.log("Array Recuperado Descuentos: "+arrayDescuentoPorcentaje);
			console.log("Array Recuperado PrecioCliente: "+arrayPrecioCliente);
			console.log("Array Recuperado PrecioClienteGuardar: "+arrayPrecioClienteGuardar);
			/*fin recuperar*/

			ajax=nuevoAjax();
			ajax.open("GET","ajaxMaterialesIngresoMultiple.php?codigo="+numFilas+"&productos_multiple="+productosMultiples,true);
			ajax.onreadystatechange=function(){
				if (ajax.readyState==4) {
					div_material_linea.innerHTML=div_material_linea.innerHTML+ajax.responseText;
				}
				for (x=0;x<arrayCantidades.length;x++) {
					console.log("Iniciando recorrido Matriz");
					/*reponiendo cantidades*/
					var name_set=arrayCantidades[x][0];
					var value_set=arrayCantidades[x][1];
					var index_set=arrayCantidades[x][2];
					document.getElementById(name_set).value=value_set;
					/*reponiendo preciosCaja*/
					name_set=arrayPreciosCaja[x][0];
					value_set=arrayPreciosCaja[x][1];
					index_set=arrayPreciosCaja[x][2];
					document.getElementById(name_set).value=value_set;
					calculaPrecioCliente(value_set, index_set);
					/*reponiendo FV*/
					name_set=arrayFV[x][0];
					value_set=arrayFV[x][1];
					index_set=arrayFV[x][2];
					document.getElementById(name_set).value=value_set;
					/*reponiendo Descuento*/
					name_set=arrayDescuentoPorcentaje[x][0];
					value_set=arrayDescuentoPorcentaje[x][1];
					index_set=arrayDescuentoPorcentaje[x][2];
					document.getElementById(name_set).value=value_set;
					calcularDescuentoUnitario(1,index_set);
					/*reponiendo PrecioCliente*/
					name_set=arrayPrecioCliente[x][0];
					value_set=arrayPrecioCliente[x][1];
					index_set=arrayPrecioCliente[x][2];
					document.getElementById(name_set).value=value_set;
					console.log("PRECIOCLIENTE: "+name_set+" "+index_set);
					/*reponiendo PrecioClienteGuardar*/
					name_set=arrayPrecioClienteGuardar[x][0];
					value_set=arrayPrecioClienteGuardar[x][1];
					index_set=arrayPrecioClienteGuardar[x][2];
					document.getElementById(name_set).value=value_set;
					calculaMargen(document.getElementById(name_set),index_set);
					console.log("PRECIOCLIENTEGUARDAR: "+name_set+" "+index_set);
				}
			}		
			ajax.send(null);
		}
		console.log("CONTROL NUM: "+num);
		Hidden();
	}	

	
	function mas(obj) {
		console.log(num)
		var banderaItems0=0;
		// for(var j=1; j<=num; j++){
		// 	if(document.getElementById('material'+j)!=null){
		// 		if(document.getElementById('material'+j).value==0){
		// 			banderaItems0=1;
		// 		}
		// 	}
		// }
		//fin validacion
		console.log("bandera: "+banderaItems0);
		if(banderaItems0==0){
			num++;
			fi = document.getElementById('fiel');
			contenedor = document.createElement('div');
			contenedor.id = 'div'+num;  
			fi.type="style";
			fi.appendChild(contenedor);
			var div_material;
			div_material=document.getElementById("div"+num);			
			ajax=nuevoAjax();
			ajax.open("GET","ajaxMaterial.php?codigo="+num,true);
			ajax.onreadystatechange=function(){
				if (ajax.readyState==4) {
					div_material.innerHTML=ajax.responseText;
					buscarMaterial(form1, num);
				}
			}		
			ajax.send(null);
		}
		console.log("CONTROL NUM: "+num);
	}	
	
	function menos(numero) {
		if(numero==num){
			num=parseInt(num)-1;
		}
		//num=parseInt(num)-1;
		fi = document.getElementById('fiel');
		fi.removeChild(document.getElementById('div'+numero));		
	}

function pressEnter(e, f){
	tecla = (document.all) ? e.keyCode : e.which;
	if (tecla==13){
		document.getElementById('itemNombreMaterial').focus();
		listaMateriales(f);
		return false;
	}
}
function calculaMargen(precioclienteForm, index){
	preciocliente=parseFloat(precioclienteForm.value);
	var preciocompra=document.getElementById("precio"+index).value;
	var costo=parseFloat(preciocompra);
	var cantidad=document.getElementById('cantidad_unitaria'+index).value;
	var cantidad_presentacion=document.getElementById('cantidadpresentacion'+index).value;

	cantidad=cantidad*cantidad_presentacion;
	
	var costounitario=parseFloat(costo)/parseFloat(cantidad);

	console.log("preciocompra: "+preciocompra);
	console.log("cantidad: "+cantidad);
	console.log("costoUnitario: "+costounitario);

	console.log("nuevo precio cliente: "+preciocliente);

	var margenNuevo=(preciocliente-costounitario)/costounitario;
	
	console.log("nuevo margen cliente: "+margenNuevo);

	var margenNuevoF="Margen["+ number_format((margenNuevo*100),0) + "%]";
	document.getElementById('divmargen'+index).innerHTML=margenNuevoF;
}
// CALCULO DE DESCUENTOS EN PORCENTAJE
function calcularDescuentoUnitario(tipo, index){
	let precio_old = parseFloat(document.getElementById('precio_old'+index).value);
	if(tipo == 0){
		//  # Numerico
		let descuento_numero = parseFloat(document.getElementById('descuento_numero'+index).value);
		let total_descuento_numero = (descuento_numero/precio_old)*100;
		document.getElementById('descuento_porcentaje'+index).value = total_descuento_numero.toFixed(2);
	}else{
		//  % Porcentaje
		let descuento_porcentaje = parseFloat(document.getElementById('descuento_porcentaje'+index).value);
		let total_descuento_porcentaje = (descuento_porcentaje/100) * precio_old;
		document.getElementById('descuento_numero'+index).value = total_descuento_porcentaje.toFixed(2);
	}
	// Ajuste Descuento Adicional
	ajusteDescuento();
	totalesMonto();
	// Ajuste Monto Total sin DESCUENTO ADICIONAL
	calculaPrecioCliente(0,index);
}
// Calculo de montos TOTALES
function calculaPrecioCliente(preciocompra, index){
	console.log("****** Ingresando calculaPrecioCliente **********");
	/*** Cuando no existe la variable de % descuento maximo aplicamos el 100 ****/
	var porcentajeDescMaxAplicarProducto=parseFloat(document.getElementById('porcentaje_max_descuento_aplicar').value);
	if(porcentajeDescMaxAplicarProducto<=0){
		porcentajeDescMaxAplicarProducto=100;
	}

	/************ Todo se calcula con descuentos *******************/
	// CALCULAR SUBTOTAL
	var cantidad 		= parseFloat(document.getElementById('cantidad_unitaria'+index).value);
	var cantidad_bonificacion = parseFloat(document.getElementById('bonificacion'+index).value);
	if(cantidad_bonificacion>0){
		cantidad_bonificacion=cantidad_bonificacion;
	}else{
		cantidad_bonificacion=0;
	}
	var precio_unitario = parseFloat(document.getElementById('precio_unitario'+index).value);
	var cantidad_presentacion = parseFloat(document.getElementById('cantidadpresentacion'+index).value);
	var descuento_porcentaje = parseFloat(document.getElementById('descuento_porcentaje'+index).value);

	var subtotal = cantidad * precio_unitario;
	subtotalF = redondear(subtotal,2);
	document.getElementById('precio_old'+index).value = subtotalF;

	var precio_sd = parseFloat(document.getElementById('precio_old'+index).value);
	var margen		  = document.getElementById('margenlinea'+index).value;

	console.log("cantidad: "+cantidad+" precio: "+precio_unitario+" bonificacion: "+cantidad_bonificacion);
	/****************************************************************************/
	//var costo=preciocompra.value;
	var costo = parseFloat(document.getElementById("precio"+index).value);
	var costounitario=costo/((cantidad*cantidad_presentacion)+cantidad_bonificacion);
	console.log("costoUnitario1: "+costounitario); // s dejo esta parte de codigo
	
	
	/****** Para el calculo del precio cliente tomamos en cuenta los porcentajes maximos aplicados ******/
	var precio_unitario_sd=precio_sd/((cantidad*cantidad_presentacion)+cantidad_bonificacion);
	console.log("precioUnitarioSD: "+precio_unitario_sd); // s dejo esta parte de codigo

	var preciocliente=precio_unitario_sd-(precio_unitario_sd*(descuento_porcentaje*(porcentajeDescMaxAplicarProducto/100)/100));
	preciocliente=preciocliente+(preciocliente*(margen/100));
	//var preciocliente=costounitario+(costounitario*(margen/100));
	console.log("preciocliente1: "+preciocliente); // s dejo esta parte de codigo
	preciocliente=redondear(preciocliente,2);
	// preciocliente=number_format(preciocliente,2);
	document.getElementById('preciocliente'+index).value=preciocliente;	
	document.getElementById('precioclienteOf'+index).value=preciocliente;

	var margenNuevo=(preciocliente-costounitario)/costounitario;
	var margenNuevoF="Margen["+ number_format((margenNuevo*100),0) + "%] CU:"+number_format(costounitario,2);

	var precioActual=parseFloat(document.getElementById("precioclienteguardar"+index).value);
	var margenPrecioActual=(precioActual-costounitario)/costounitario;
	var margenPrecioActualF="Margen["+ number_format((margenPrecioActual*100),0) + "%]";
	document.getElementById('divmargen'+index).innerHTML=margenPrecioActualF;
	// Margen del PRECIO VENTA CALCULADO
	document.getElementById('divmargenOf'+index).innerHTML=margenNuevoF;
	console.log("**** fin  calculaPrecioCliente ******");
	// Ajuste Descuento Adicional
	ajusteDescuento();
	totalesMonto();
}

function totalesMonto(){	
	var cantidadTotal=0;
	var precioTotal=0;
	var montoTotal=0;
    for(var ii=1;ii<=num;ii++){
		if(document.getElementById('material'+ii)!=null){
			var precio=document.getElementById("precio"+ii).value;
			montoTotal=montoTotal+parseFloat(precio);
		}
	}
	montoTotal=Math.round(montoTotal*100)/100;
	
    document.getElementById("totalCompra").value=montoTotal;
	//alert(montoTotal);
	var descuentoTotal=document.getElementById("descuentoTotal").value;
	var totalSD=montoTotal-descuentoTotal;
	//alert(totalSD);
	document.getElementById("totalCompraSD").value=totalSD;
	
}

/*****************************************
 * Descuento Adicional a ITEMS
*****************************************/
function changeDescuentoAdicional(){
	// Ajuste Descuento Adicional
	ajusteDescuento();
	// Ajuste Monto Total sin DESCUENTO ADICIONAL
	totalesMonto();
}

/*****************************************
 * Ajuste de Descuento del Monto de Venta
*****************************************/
function ajusteDescuento(){
	var cantidadTotal=0;
	var precioTotal=0;
	var montoTotal=0;
	// Total Monto sin descuento adicional
	var monto_total_old = 0;
    for(var ii=1;ii<=num;ii++){
		if(document.getElementById('material'+ii)!=null){
			monto_total_old += parseFloat(document.getElementById("precio_old"+ii).value);
		}
	}
	// Detalle
    for(var ii=1;ii<=num;ii++){
		if(document.getElementById('material'+ii)!=null){
			var precio 	    = document.getElementById("precio_old"+ii).value;
			// Total Compra
			var total_compra= monto_total_old;
			// Descuento Adicional Superior
			var desc_ad_sup = document.getElementById("descuento_adicional").value;

			// Total Descuento ITEM
			console.log("------------calculando descuento Adicional--------------")
			document.getElementById("descuento_adicional"+ii).value = parseFloat(((parseFloat(precio)/parseFloat(total_compra))*desc_ad_sup).toFixed(2));

			/*********************/
			/*		TOTAL		 */
			/*********************/
			var item_descuento_unitario = parseFloat(document.getElementById("descuento_numero"+ii).value);
			var item_descuento = parseFloat(document.getElementById("descuento_adicional"+ii).value);
			var item_precio    = parseFloat(document.getElementById("precio_old"+ii).value);
			var total_final = item_precio - (item_descuento+item_descuento_unitario);
			document.getElementById("precio"+ii).value = total_final.toFixed(2);
			
		}
	}
	
}
/**
 * Visualización Bonificación
 */
function toggleBonificacion(num) {
    var bonificacionInput = document.getElementById('bonificacion' + num);
    var toggleIcon = document.getElementById('toggleIcon' + num);
    var toggleText = document.getElementById('toggleText' + num);
    var badgeSpan = document.getElementById('badgeSpan' + num);

    // Cambiar la visibilidad del input de bonificación
    if (bonificacionInput.style.display === 'none') {
        bonificacionInput.style.display = 'block';
        badgeSpan.style.backgroundColor = '#ffcccc';  // Cambiar el color de fondo a rojo suave
        document.getElementById('bonificacion' + num).value = '';
    } else {
        bonificacionInput.style.display = 'none';
        badgeSpan.style.backgroundColor = '#d3d3d3';  // Restaurar el color de fondo original
    }
}



function validar(f){ 
	
	// Verificamos cuantos items hay
	var cantidadItems = $(".row-item").length;
	// Verificar si no hay elementos
	if (cantidadItems === 0) {
		Swal.fire({
			type: 'warning',
			title: 'Ops!',
			text: 'Por favor, agregue al menos un item.',
		});
		return false;
	}

	// En caso de seleccionar el TIPO DE PAGO "Credito"
	if ($('#cod_tipopago').val() == "4" && $('#dias_credito').val() == "") {
		Swal.fire({
			type: 'warning',
			title: 'Ops!',
			text: 'Debe adicionar los Días de Credito.',
		});
		return false;
	}

	//VALIDAMOS QUE INGRESEN ITEMS DUPLICADOS PRODUCTO - LOTE
	const materiales = $('form input[name^="material"]');
    const lotes = $('form input[name^="lote"]');
    const combinaciones = new Set();
    for (let i = 0; i < materiales.length; i++) {
        const material = materiales[i].value.trim();
        const lote = lotes[i].value.trim();
        const combinacion = `${material}-${lote}`;

        if (combinaciones.has(combinacion)) {
            alert(`Error: El Producto "${material} y Lote  ${lote}" está duplicado. Cambie el Lote del Producto.`);
            return false;
        }
        combinaciones.add(combinacion);
    }
    //FIN VALIDAR ITEMS DUPLICADOS

	f.cantidad_material.value=num;
	var cantidadItems=num;
	
	var banderaValidacionDetalle=0;
	var inputs = $('form input[name^="cantidadunitaria"]');
	inputs.each(function() {
  	var value = $(this).val();
  	if(value==0 || value==""){
			banderaValidacionDetalle=1;
  	}else{
  	}
	});
	if(banderaValidacionDetalle==1){
		alert("Las cantidades para los productos no son validas.");
		return(false);
	}
	
	// Verificación de costos
	Swal.fire({
		title: 'Modificaciones de Precio',
		html: verificarCambio(),
		showCancelButton: true,
		confirmButtonText: 'Continuar',
		cancelButtonText: 'Cancelar',
	}).then((result) => {
		if (result.value) {
		document.getElementById("btsubmit").value = "Enviando...";
		document.getElementById("btsubmit").disabled = true;
		f.submit();
		}
	});
	return false;

    // document.getElementById("btsubmit").value = "Enviando...";
    // document.getElementById("btsubmit").disabled = true;
    // return true;
}


/*function checkSubmit() {
    return true;
}*/

function redondear(value, precision) {
    var multiplier = Math.pow(10, precision || 0);
    return Math.round(value * multiplier) / multiplier;
}


	/***************************
	 * Captura Detalle de Items
	 */
	function masDetalle() {
		num++;
		fi = document.getElementById('fiel');
		contenedor = document.createElement('div');
		contenedor.id = 'div'+num;  
		fi.type="style";
		fi.appendChild(contenedor);
		var div_material;
		div_material=document.getElementById("div"+num);			
		ajax=nuevoAjax();
		ajax.open("GET","ajaxMaterial.php?codigo="+num,true);
		ajax.onreadystatechange=function(){
			div_material.innerHTML=ajax.responseText;
		}		
		ajax.send(null);
	}	
	</script>
<?php


if($fecha=="")
{   $fecha=date("d/m/Y");
}

$banderaUpdPreciosSucursales=obtenerValorConfiguracion($enlaceCon,49);
$txtUpdPrecios="";
if($banderaUpdPreciosSucursales==0){
	$txtUpdPrecios="*** Los precios seran actualizados solo en ESTA SUCURSAL.";
}else{
	$txtUpdPrecios="*** Los precios seran actualizados en TODAS LAS SUCURSALES del sistema.";
}

$porcentajeDescMaxAplicar=obtenerValorConfiguracion($enlaceCon,53);
$txtPorcentajeDescMaxAplicar="*** El porcentaje de descuento por producto a aplicar en el precio es de: ".$porcentajeDescMaxAplicar."%";


$global_almacen=$_COOKIE["global_almacen"];

$sql="select IFNULL(max(nro_correlativo)+1,1) from ingreso_almacenes where cod_almacen='$global_almacen'";
$resp=mysqli_query($enlaceCon,$sql);
$dat=mysqli_fetch_array($resp);
$num_filas=mysqli_num_rows($resp);
if($num_filas==1){   
	$nro_correlativo=$dat[0];
}
echo "<form action='guarda_ingresomateriales.php' method='post' name='form1' id='form1' onsubmit='return validar(this);'>";

echo "<input type='hidden' name='porcentaje_max_descuento_aplicar' id='porcentaje_max_descuento_aplicar' value='$porcentajeDescMaxAplicar''>";

echo "<table border='0' class='textotit' align='center'>
		<tr><th width='40%'></th><th width='30%'>Registrar Ingreso de Productos</th><th></th></tr>
		<tr><th align='left'><span class='textopequenorojo' style='background-color:yellow;'><b>$txtUpdPrecios</b></span></th>
			<th></th>
			<th align='left'><span class='textopequenorojo' style='background-color:aqua;'><b>$txtPorcentajeDescMaxAplicar</b></span></th>
		</tr>
		</table>";
		
// ************* CABECERA ************* //
$cab_codigo_ingreso = $_GET['codigo_ingreso'];
$sqlCabecera="SELECT ia.cod_tipo_doc, ia.nro_factura_proveedor, ia.cod_proveedor, ia.cod_tipopago, ia.dias_credito, ia.fecha_factura_proveedor, ia.observaciones
			FROM ingreso_almacenes ia
			WHERE ia.cod_ingreso_almacen = '$cab_codigo_ingreso'";
$respCabecera=mysqli_query($enlaceCon,$sqlCabecera);
$cab_cod_tipo_doc			= '';
$cab_nro_factura_proveedor	= '';
$cab_cod_proveedor			= '';
$cab_cod_tipopago			= '';
$cab_dias_credito			= '';
$cab_fecha_factura_proveedor= '';
$cab_observaciones			= '';
while($data=mysqli_fetch_array($respCabecera)){
	$cab_cod_tipo_doc			= $data[0];
	$cab_nro_factura_proveedor	= $data[1];
	$cab_cod_proveedor			= $data[2];
	$cab_cod_tipopago			= $data[3];
	$cab_dias_credito			= $data[4];
	$cab_fecha_factura_proveedor= $data[5];
	$cab_observaciones			= $data[6];
}
// ************************************ //
echo "<table border='0' class='texto' cellspacing='0' align='center' width='90%' style='border:#ccc 1px solid;'>";
echo "<tr>
	<th>Nro. Ingreso: <b>$nro_correlativo<b></th>";
echo"<th><input type='text' disabled='true' class='texto' value='$fecha' id='fecha' size='10' name='fecha'></th>
	<th>Tipo de Ingreso: </td><th>";
$sql1="select cod_tipoingreso, nombre_tipoingreso from tipos_ingreso where cod_tipoingreso='1000' order by nombre_tipoingreso";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<select name='tipo_ingreso' id='tipo_ingreso' class='texto'>";
while($dat1=mysqli_fetch_array($resp1))
{   $cod_tipoingreso=$dat1[0];
    $nombre_tipoingreso=$dat1[1];
    echo "<option value='$cod_tipoingreso'>$nombre_tipoingreso</option>";
}
echo "</select></td>";

echo"<th colspan='1'>Tipo de Documento: </th><th>";
$sql1="SELECT td.codigo, td.nombre, td.abreviatura
		FROM tipos_docs td
		WHERE td.codigo IN (1,2)";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<select name='tipo_documento' id='tipo_documento' class='selectpicker' data-style='btn btn-info'>";
while($dat1=mysqli_fetch_array($resp1))
{   $cod_tipoingreso=$dat1[0];
    $nombre_tipo_documento=$dat1[2];
	$select_cab_cod_tipo_doc = isset($cab_cod_tipo_doc) && $cab_cod_tipo_doc == $cod_tipoingreso ? 'selected' : '';
    echo "<option value='$cod_tipoingreso' $select_cab_cod_tipo_doc>$nombre_tipo_documento</option>";
}
echo "</select></td>";

echo "<th>Nro. Documento: </th>
	<th><input type='number' class='texto' name='nro_factura' value='$cab_nro_factura_proveedor' id='nro_factura' required>
	</th></tr>";

echo "<tr><th>Proveedor/Distribuidor:</th>";
$sql1="SELECT p.cod_proveedor, concat(p.nombre_proveedor, ' (',tp.nombre_tipoventa,')') 
		FROM proveedores p 
		LEFT JOIN tipos_proveedor tp ON tp.cod_tipoventa = p.cod_tipoproveedor
		ORDER BY 2";
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<th align='center'>
<select name='proveedor' id='proveedor' class='selectpicker' data-style='btn btn-info' data-live-search='true' required>";
echo "<option value=''>-</option>";
while($dat1=mysqli_fetch_array($resp1))
{   $codigo=$dat1[0];
    $nombre=$dat1[1];
	$margenPrecio=$dat1[2];
	
	$select_cab_cod_proveedor = isset($cab_cod_proveedor) && $cab_cod_proveedor == $codigo ? 'selected' : '';

    echo "<option value='$codigo' $select_cab_cod_proveedor>$nombre</option>";
}
echo "</select></th>";

// Verificación de Bloqueo de campos
// 0:Muestra; 1: Oculta
$banderaCamposEscondidos = obtenerValorConfiguracion($enlaceCon,55);
echo "<th>Tipo de Pago:</th>";
$sql1="SELECT tp.cod_tipopago, tp.nombre_tipopago
		FROM tipos_pago tp
		WHERE tp.cod_tipopago = 1
		OR tp.cod_tipopago = 4
		ORDER BY tp.cod_tipopago ASC ".(($banderaCamposEscondidos == 1) ? 'LIMIT 1' : '');
$resp1=mysqli_query($enlaceCon,$sql1);
echo "<th align='center'>
<select name='cod_tipopago' id='cod_tipopago' class='selectpicker' data-style='btn btn-info' required >";
while($dat1=mysqli_fetch_array($resp1))
{   $codigo=$dat1[0];
    $nombre=$dat1[1];
	$margenPrecio=$dat1[2];
	
	$select_cab_cod_tipopago = isset($cab_cod_tipopago) && $cab_cod_tipopago == $codigo ? 'selected' : '';
	
    echo "<option value='$codigo' $select_cab_cod_tipopago>$nombre</option>";
}
echo "</select></th>";

echo "<th colspan='1'>Días de Credito: <input type='number' class='texto' name='dias_credito' id='dias_credito' min='0' max='180' value='$cab_dias_credito' ".(( $cab_cod_tipopago == 4) ? '' : 'readonly')."></th>
<th colspan='1'>Fecha Documento Proveedor: <input type='date' class='texto' name='fecha_factura_proveedor' id='fecha_factura_proveedor' value='$cab_fecha_factura_proveedor' required></th>";

echo "<th colspan='1'>Observaciones:</th>
	<th colspan='1'><textarea class='texto' name='observaciones' size='20'>$cab_observaciones</textarea></th>";


echo "</tr>";


// ************* DETALLE ************* //
echo "</table><br>";
?>
        <div class="contenedor">
        	<div class="codigo-barras div-center">
				<input class="boton" type="button" value="Nuevo Item (+)" onclick="mas(this)" accesskey="A"/>
				<input type="text" class="form-codigo-barras" id="input_codigo_barras" placeholder="Ingrese el código de barras." autofocus autocomplete="off">
			</div>

        </div>
		
		<fieldset id="fiel" style="width:100%;border:0;" >
			<table align="center"class="text" cellSpacing="1" cellPadding="2" width="100%" border="1" id="data0" style="border:#ccc 1px solid;">
				<tr>
					<td colspan="13" align="center">
						<span style="color: red;"><b>Descuento Final Aplicado a los Productos:  </b></span><input type="text" id="descuento_adicional" name="descuento_adicional" value="0" onkeyup="changeDescuentoAdicional()">
					</td>
				</tr>
				<tr>
					<td align="center" colspan="7">
						<!--input class="boton" type="button" value="Agregar por Linea (+)" onclick="modalMasLinea(this)" accesskey="B"/-->
					</td>
				</tr>
				<tr class="titulo_tabla" align="center">
					<th width="3%" align="center">&nbsp;</th>
					<th width="26%" align="center">Producto</th>
					<th width="6%" align="center">Cantidad</th>
					<th width="6%" align="center">Precio<br>Caja</th>
					<th width="10%" align="center">Vencimiento</th>
					<th width="6%" align="center">Subtotal</th>
					<!-- Descuento Unitario -->
					<th width="6%" align="center">Desc.<br>Prod</th>
					<!-- Descuento Adicional -->
					<th width="6%" align="center">Desc.<br>Adicional</th>
					<!-- Monto Total -->
					<th width="8%" align="center">Total</th>
					<th width="10%" align="center">Precio<br>Venta<br>Calculado</th>
					<th width="10%" align="center"><span style="font-size:15px;width:80px;color:blue;"><b>Precio a<br>Actualizar</b></span></th>
					<th width="3%" align="center">-</th>
				</tr>
			</table>

			<div id="divMaterialLinea">
			</div>
			<!-- Detalle de Items -->
			<?php
			// ************* CABECERA DETALLE ************* //
				$cab_codigo_ingreso = $_GET['codigo_ingreso'];
				$sqlDetalle="SELECT ida.cod_ingreso_almacen, 
									ida.cod_material, 
									ida.cantidad_unitaria, 
									ida.fecha_vencimiento, 
									ida.descuento_unitario, 
									ida.precio_bruto,
									ida.cantidad_bonificacion,
									ida.costo_actualizado 
							FROM ingreso_detalle_almacenes ida
							WHERE ida.cod_ingreso_almacen = '$cab_codigo_ingreso'";
				$respDetalle=mysqli_query($enlaceCon,$sqlDetalle);

				$index_detalle = 0;
				$detalle_codigo	= '';
				while($dataDetalle=mysqli_fetch_array($respDetalle)){
					$index_detalle++;
					$detalle_codigo		  = $dataDetalle[0];
					$detalle_cod_material = $dataDetalle[1];
					$detalle_cantidad_unitaria 	= $dataDetalle[2];
					$detalle_fecha_vencimiento 	= $dataDetalle[3];
					$detalle_descuento_unitario = $dataDetalle[4];
					$detalle_precio_bruto 		= $dataDetalle[5];
					$detalle_cantidad_bonificacion = $dataDetalle[6];
			// ************************************ //
					$detalle_table_material 	= obtieneProductoDetalle($detalle_cod_material);
					// var_dump($detalle_table_material[1]);
					$table_material 			= $detalle_table_material[0];
					$table_cod_material  		= $detalle_table_material[1] . " - <span class='textomedianonegro'>CP:".$detalle_table_material[2]."</span>";
					$table_cantidadpresentacion = $detalle_table_material[2];
					$table_divpreciocliente		= "PrecioActual: " . number_format($detalle_table_material[3], 2);
					$table_precioclienteguardar = number_format($detalle_table_material[3], 2);
					$table_margenlinea			= $detalle_table_material[4];
					// Datos de Inputs
					$input_cantidad = $detalle_cantidad_unitaria / $table_cantidadpresentacion;
					$input_precio   = number_format(($detalle_precio_bruto * $table_cantidadpresentacion), 2);
			?>
				<div id="div<?=$index_detalle?>">
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml">
					<head>
					<link rel="STYLESHEET" type="text/css" href="stilos.css" />
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					<?php 
					require("conexionmysqli2.inc");

					$num=$index_detalle;

					$fechaActual=date("Y-m-d");
					?>

					<table border="1" align="center" cellSpacing="1" cellPadding="1" width="100%" style="border:#ccc 1px solid;" id="data<?php echo $num?>" >
					<tr bgcolor="#FFFFFF">

					<!-- row -->
					<input type="hidden" class="row-item" value="<?=$num?>">

					<!-- Buscar -->
					<td width="3%" align="center">
						<a href="javascript:buscarMaterial(form1, <?php echo $num;?>)" accesskey="B"><img src='imagenes/buscar2.png' title="Buscar Producto" width="20"></a>
					</td>

					<!-- Producto -->
					<td width="26%" align="left"><!--?php echo $num;?-->
					<input type="hidden" name="material<?php echo $num;?>" id="material<?php echo $num;?>" value="<?=$table_material?>">
					<input type="hidden" name="cantidadpresentacion<?php echo $num;?>" id="cantidadpresentacion<?php echo $num;?>" value="<?=$table_cantidadpresentacion?>">
					<div id="cod_material<?php echo $num;?>" class='textomedianorojo'><?=$table_cod_material?></div>
					</td>

					<!-- CANTIDAD -->
					<td align="center" width="6%">
						<input type="number" class="inputnumber" min="1" max="1000000" id="cantidad_unitaria<?php echo $num;?>" name="cantidad_unitaria<?php echo $num;?>" size="5"  value="<?=$input_cantidad?>" onChange='calculaPrecioCliente(0, <?php echo $num;?>);' onKeyUp='calculaPrecioCliente(0, <?php echo $num;?>);' required>    
						
						<!-- Botón para mostrar/ocultar el input de bonificación -->
						<span class="badge badge-secondary" id="badgeSpan<?php echo $num;?>" style="cursor: pointer; background-color: #d3d3d3; padding: 5px;" onclick="toggleBonificacion(<?php echo $num;?>)" title="Bonificación">
							B
						</span>
						<!-- Input de bonificación oculto por defecto -->
						<input type="number" class="inputnumber" min="0" id="bonificacion<?php echo $num;?>" name="bonificacion<?php echo $num;?>" placeholder="BonificadoUnit" value="0" style="display: none; width: 90px; background-color: #ffffcc; color: blue; font-size: 10px; font-weight: bold;" size="5" onChange='calculaPrecioCliente(0, <?php echo $num;?>);' onKeyUp='calculaPrecioCliente(0, <?php echo $num;?>);'>

					</td>

					<!-- PRECIO CAJA -->
					<td align="center" width="6%">
					<input type="number" class="inputnumber" min="0.01" max="1000000" id="precio_unitario<?php echo $num;?>" name="precio_unitario<?php echo $num;?>" size="5"  value="<?=$input_precio?>" onChange='calculaPrecioCliente(0, <?php echo $num;?>);' onKeyUp='calculaPrecioCliente(0, <?php echo $num;?>);' step="0.01" required>
					</td>

					<!-- Fecha de vencimiento -->
					<td align="center" width="10%">
					<input type="date" class="textoform" min="<?php echo $fechaActual; ?>" id="fechaVenc<?php echo $num;?>" name="fechaVenc<?php echo $num;?>" size="5" required value="<?=$detalle_fecha_vencimiento?>">
					</td>

					<!-- Subtotal -->
					<td align="center" width="6%">
					<input type="number" class="inputnumber" value="0" id="precio_old<?php echo $num;?>" name="precio_old<?php echo $num;?>" size="5" min="0" step="0.01" onKeyUp='calculaPrecioCliente(this,<?php echo $num;?>);' onChange='calculaPrecioCliente(this,<?php echo $num;?>);' required>
					</td>

					<!-- DESCUENTO PRODUCTO -->
					<td align="center" width="6%">
					%<input type="number" class="inputnumber" min="0" max="1000000" id="descuento_porcentaje<?php echo $num;?>" name="descuento_porcentaje<?php echo $num;?>" size="5"  value="<?=$detalle_descuento_unitario?>" onKeyUp='calcularDescuentoUnitario(1, <?php echo $num;?>);' onChange='calcularDescuentoUnitario(1, <?php echo $num;?>);' step="0.01" required data-tipo="1"><br>
					Bs.<input type="number" class="inputnumber" min="0" max="1000000" id="descuento_numero<?php echo $num;?>" name="descuento_numero<?php echo $num;?>" size="5"  value="0" onKeyUp='calcularDescuentoUnitario(0, <?php echo $num;?>);' onChange='calcularDescuentoUnitario(0, <?php echo $num;?>);' step="0.01" required data-tipo="0">
					</td>

					<!-- Decuento Adicional -->
					<td align="center" width="6%">
					<input type="number" class="inputnumber" value="0" id="descuento_adicional<?php echo $num;?>" name="descuento_adicional<?php echo $num;?>" size="5" min="0" step="0.01" readonly>
					</td>

					<!-- Monto TOTAL -->
					<td align="center" width="8%">
					<input type="number" class="inputnumber" value="0" id="precio<?php echo $num;?>" name="precio<?php echo $num;?>" size="5" min="0" step="0.01" readonly>
					</td>

					<td align="center" width="10%">
						<!-- Precio Venta Calculado -->
						<input type="number" class="inputnumber" value="0" id="preciocliente<?php echo $num;?>" name="preciocliente<?php echo $num;?>" size="4" min="0" step="0.01" style="height:20px;font-size:15px;width:80px;color:black;" disabled>
						<div id="divmargenOf<?php echo $num;?>" class="textopequenorojo2">-</div>
					</td>

					<td align="center" width="10%">
						<!-- PrecioCliente a Guardar -->
						<input type="number" class="inputnumber" value="<?=$table_precioclienteguardar?>" id="precioclienteguardar<?php echo $num;?>" name="precioclienteguardar<?php echo $num;?>" size="4" min="0" step="0.01" onKeyUp='calculaMargen(this,<?php echo $num;?>);' onChange='calculaMargen(this,<?php echo $num;?>);' style="height:20px;font-size:19px;width:80px;color:blue;" required>
						<!-- PrecioCliente sin modificacion -->
						<input type="hidden" class="inputnumber" value="0" id="precioclienteOf<?php echo $num;?>" name="precioclienteOf<?php echo $num;?>">
						</br>
						<div id="divpreciocliente<?php echo $num;?>" class="textopequenorojo"><?=$table_divpreciocliente?></div>
						<div id="divmargen<?php echo $num;?>" class="textopequenorojo2">-</div>
						<input type="hidden" name="margenlinea<?php echo $num;?>" id="margenlinea<?php echo $num;?>" value="<?=$table_margenlinea?>">
					</td>

					<td align="center"  width="3%" >
						<input class="boton2peque" type="button" value="-" onclick="menos(<?php echo $num;?>)" size=""/></td>
					</tr>
					</table>

					</head>
					</html>
				</div>
			<script>
				num++; 
				banderaItems0 = 0;
			</script>
			<!-- FIN LISTA DE ITEMS -->
			<?php 
				}
			?>

		</fieldset>

		
		<table align="center"class="text" cellSpacing="1" cellPadding="2" width="100%" border="0" id="data0" style="border:#ccc 1px solid;">
			<tr>
				<td align='right' width="90%"><span style="color:red;"><b>Total Compra</b></span></td>
				<td align='right' width="10%"><input type='number' name='totalCompra' id='totalCompra' value='0' size='10' step="0.01" readonly></td>
			</tr>
			<tr>
				<td align='right' width="90%"><span style="color:red;"><b>Descuento Financiero</b></span></td>
				<td align='right' width="10%"><input type='number' name='descuentoTotal' id='descuentoTotal' value='0' size='10' onKeyUp='totalesMonto();' step="0.01" required></td>
			</tr>
			<tr>
				<td align='right' width="90%"><span style="color:red;"><b>Total</b></span></td>
				<td align='right' width="10%"><input type='number' name='totalCompraSD' id='totalCompraSD' value='0' size='10' step="0.01" readonly></td>
			</tr>
		</table>

		<script>
			// Cancula totales
			for (var i = 1; i <= num; i++) {
				console.log('hola'+i)
				calculaPrecioCliente(0, i);
				calcularDescuentoUnitario(1, i);
			}
		</script>
		
<div class='divBotones'>
	<!-- <input type='button' class='boton' name='Guardar' value='Guardar'  id='btsubmit' onClick='return validar(this.form);'></center> -->
	<button type="submit" class="boton" name="Guardar" id="btsubmit">Guardar</button>
	<input type='button' class='boton2' value='Cancelar' onClick="location.href='navegador_ingresomateriales.php'">
</div>

<?php

echo "</div>";
?>



<div id="divRecuadroExt" style="background-color:#666; position:absolute; width:850px; height: 500px; top:30px; left:150px; visibility: hidden; opacity: .70; -moz-opacity: .70; filter:alpha(opacity=70); -webkit-border-radius: 20px; -moz-border-radius: 20px; z-index:2;">
</div>

<div id="divboton" style="position: absolute; top:20px; left:920px;visibility:hidden; text-align:center; z-index:3">
	<a href="javascript:Hidden();"><img src="imagenes/cerrar4.png" height="45px" width="45px"></a>
</div>

<div id="divProfileData" style="background-color:#FFF; width:800px; height:450px; position:absolute; top:50px; left:170px; -webkit-border-radius: 20px; 	-moz-border-radius: 20px; visibility: hidden; z-index:2;">
  	<div id="divProfileDetail" style="visibility:hidden; text-align:center; height:445px; overflow-y: scroll;">
		<table align='center' class="texto">
			<tr><th>Linea</th><th>Material</th><th>&nbsp;</th></tr>
			<tr>
			<td><select name='itemTipoMaterial' id="itemTipoMaterial" class="textomedianorojo" style="width:300px">
			
			<?php
			$sqlTipo="select p.cod_proveedor, p.nombre_proveedor from proveedores p order by 2;";
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
				<input type='text' name='itemNombreMaterial' id="itemNombreMaterial" class="textogranderojo" onkeypress="return pressEnter(event, this.form);">
			</td>
			<td>
				<input type='button' class='boton' value='Buscar' onClick="listaMateriales(this.form)">
			</td>
			</tr>
			<tr>
			<td colspan="3"><input type="button" class="boton2peque" onclick="javascript:masMultiple(this.form);" value="Incluir Productos Seleccionados">
			</td>
			</tr>
			
		</table>
		<div id="divListaMateriales"></div>
	
	</div>
</div>
<input type='hidden' name='prodItemActivo' value="<?=$index_detalle?>">
<input type='hidden' name='cantidad_material' value="0">


</form>

<script>
	var jsonData = [];
	function verificarCambio() {
		// return true;
		var jsonData = [];

		// Itera sobre todos los elementos <input> con la clase "row-item"
		$(".row-item").each(function(index) {
			var rowItemValue 	 = $(this).val();
			var producto 		 = $("#cod_material" + rowItemValue).html();
			
			var precioTexto  	 = $("#divpreciocliente" + rowItemValue).text();
			var partes 		 	 = precioTexto.split(":");
			var precioActual 	 = parseFloat(partes[1].trim());
			var precioCalculado  = parseFloat($("#preciocliente" + rowItemValue).val());
			var precioActualizar = parseFloat($("#precioclienteguardar" + rowItemValue).val());

			if (precioActual !== precioCalculado || precioActual !== precioActualizar) {
				var rowData = {
					"producto": producto,
					"precioActual": precioActual,
					"precioCalculado": precioCalculado,
					"precioActualizar": precioActualizar,
					"rowItemValue": rowItemValue
				};
				jsonData.push(rowData);
			}
		});
		var tableHTML;
		if(jsonData.length > 0){
			// Construye la tabla HTML
			tableHTML = '<table class="table table-striped">';
			tableHTML += "<thead><tr><th><b>Producto</b></th><th><b>Precio Actual</b></th><th><b>Precio Calculado</b></th><th><b>Precio a Actualizar</b></th></tr></thead>";
			tableHTML += '<tbody>';
			
			for (var i = 0; i < jsonData.length; i++) {
				tableHTML += '<tr>';
				tableHTML += '<td>' + jsonData[i].producto + '</td>';
				tableHTML += '<td style="font-weight: bold; color: blue; font-size: 20px;">' + jsonData[i].precioActual + '</td>';
				tableHTML += '<td style="font-weight: bold; color: blue; font-size: 20px;">' + jsonData[i].precioCalculado + '</td>';
				tableHTML += '<td style="font-weight: bold; color: red; font-size: 23px;">' + jsonData[i].precioActualizar + '</td>';
				tableHTML += '</tr>';
			}
			
			tableHTML += '</tbody>';
			tableHTML += '</table>';
		}else{
			// No hubo cambios, muestra la alerta
			tableHTML = '<div class="alert alert-success" role="alert">No hubo cambios en los precios</div>';
		}
		return tableHTML;
	}
	// Verificación de Tipo de Pago
	$('#cod_tipopago').on('change', function () {
        var selectedValue = $(this).val();
		$('#dias_credito').val(0);
        if (selectedValue === '4') {
            $('#dias_credito').prop('readonly', false);
			$('#fecha_factura_proveedor').val('<?=date('Y-m-d')?>');
            $('#fecha_factura_proveedor').prop('readonly', false);
        } else if (selectedValue === '1') {
            $('#dias_credito').prop('readonly', true);
			$('#fecha_factura_proveedor').val('');
            $('#fecha_factura_proveedor').prop('readonly', true);
        }
    });
</script>
</body>