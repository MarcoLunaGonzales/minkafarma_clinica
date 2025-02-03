<html>
    <head>
        <title>Busqueda</title>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-1.4.4.min.js"></script>
        <link href="../stilos.css" rel='stylesheet' type='text/css'>

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
function ajaxCargarDeudas(){
	var contenedor;
	contenedor = document.getElementById('divDetalle');

	var codCliente = document.getElementById('cliente').value;

	ajax=nuevoAjax();

	ajax.open("GET", "ajaxCargarDeudas.php?codCliente="+codCliente,true);

	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			contenedor.innerHTML = ajax.responseText;
		}else{
			contenedor.innerHTML = "Cargando...";
		}
		$('.selectpicker').selectpicker('refresh');
	}
	ajax.send(null)
}

function validar(f)
{   
	var codCliente=document.getElementById("cliente").value;
	var banderaMontos=0;

	var monto;
	var nroDoc;
	if(codCliente==0){
		alert("Debe seleccionar un Cliente");
		return false;
	}else{
		/******** Validacion Algun Monto con Datos ********/
		var inputs = $('form input[name^="montoPago"]');
		inputs.each(function() {
		  	var value = $(this).val();
		  	if(value>0){
					banderaMontos=1;
		  	}
		});
		if(banderaMontos==0){
			alert("Debe existir algun monto valido para guardar la cobranza.");
			return(false);
		}
		/******** Fin validacion Cantidades ********/
	}	
	return true;
}

function solonumeros(e)
{
	var key;
	if(window.event) {// IE
		key = e.keyCode;
	}else if(e.which) // Netscape/Firefox/Opera
	{
		key = e.which;
	}
	if (key < 46 || key > 57) 
	{
	  return false;
	}
	return true;
}



	</script>
<?php

require("../conexionmysqli.inc");

$globalSucursal=$_COOKIE['global_agencia'];

?>
</head>
<body>
	<div id="appVue" hidden>
		<form action='guardarCobranza.php' method='post' name='form1' onsubmit="return validar(this)">
		<h3 align="center">Registrar Pagos</h3>

		<table border='0' class='texto' cellspacing='0' align='center' width='80%' style='border:#ccc 1px solid;'>
		<tr><th>Funcionario</th><th>Fecha Pago</th><th>Observaciones</th></tr>
		<?php
		$sql1="SELECT f.codigo_funcionario, CONCAT(f.nombres, ' ', f.paterno) as nombre_funcionario
			FROM funcionarios f
			WHERE f.estado = 1
			ORDER BY f.codigo_funcionario DESC";
		$resp1=mysqli_query($enlaceCon, $sql1);
		?>
		<tr>
		<td align='center'>
		<select name='cod_funcionario' id='cod_funcionario' class='selectpicker' data-style="btn btn-success" data-live-search="true" required>
			<option value="0">Seleccione una opcion</option>
			<?php
			while($dat1=mysqli_fetch_array($resp1))
			{   $codigo=$dat1[0];
				$nombre=$dat1[1];
			?>
				<option value="<?php echo $codigo; ?>"><?php echo $nombre; ?></option>
			<?php	
			}
			$fecha=date("d/m/Y");
			?>
		</select>
		</td>
		<td>
		<input type='text' class='texto' value='<?php echo $fecha; ?>' id='fecha' size='10' name='fecha'>
		<img id='imagenFecha' src='../imagenes/fecha.bmp'>
		</td>
		<td>
		<input type='text' class='texto' value="" id='observaciones' size='40' name='observaciones'>
		</td>


		</tr>
		</table>

		</br>

		<div>
			<div class="text-center">
				<button type="button" class="btn btn-success" @click="abrirModalPagoCliente">
					<i class="material-icons">add</i> Agregar Pago
				</button>
			</div>
			<div class="container-fluid p-4">
				<div class="table-responsive">
					<!-- <table border='0' class='texto' cellspacing='0' align='center' width='90%' style='border:#ccc 1px solid;'> -->
					<table class="table table-bordered table-striped text-center" align="center">
						<thead class="thead-light">
							<tr>
								<th style="font-size:13px; font-weight: bold;">Cliente</th>
								<th style="font-size:13px; font-weight: bold;">Tipo Doc</th>
								<th style="font-size:13px; font-weight: bold;">Nro.</th>
								<th style="font-size:13px; font-weight: bold;">Fecha</th>
								<th style="font-size:13px; font-weight: bold;">Monto</th>
								<th style="font-size:13px; font-weight: bold;">A Cuenta</th>
								<th style="font-size:13px; font-weight: bold;">Saldo</th>
								<th style="font-size:13px; font-weight: bold;">Tipo de Pago</th>
								<th style="font-size:13px; font-weight: bold;">Monto a Pagar</th>
								<th style="font-size:13px; font-weight: bold;">Nro. Doc. Pago</th>
								<th style="font-size:13px; font-weight: bold;">Detalle</th>
								<th style="font-size:13px; font-weight: bold;">Acciones</th>
							</tr>
						</thead>
						<tbody id="divDetalle">
							<tr v-for="(item, index) in lista_pagos" :key="index">
								<input type='hidden' :value='item.cod_salida_almacenes' :name="'codCobro' + (index + 1)" :id="'codCobro' + (index + 1)">
								<input type='hidden' :value='item.cod_cliente' :name="'codCliente' + (index + 1)" :id="'codCliente' + (index + 1)">
								<td>{{ item.nombre_cliente }}</td>
								<td>{{ item.tipo_doc }}</td>
								<td>{{ item.nro_correlativo }}</td>
								<td>{{ item.fecha }}</td>
								<td>{{ item.monto_final }}</td>
								<td>{{ item.monto_cancelado }}</td>
								<td>{{ item.saldo_cliente }}</td>
								<td>
									<select :name="'tipoPago' + (index + 1)" class='form-control'>
										<option v-for="(pago, pagoIndex) in lista_tipos_pago" :value="pago.cod_tipopago" :key="pagoIndex">{{ pago.nombre_tipopago }}</option>
									</select>
								</td>
								<td>
									<input type='number' class='texto' :name="'montoPago' + (index + 1)" :id="'montoPago' + (index + 1)" value="0" size='10' :max='item.monto_final - item.monto_cancelado' step='any' v-model="item.monto_pago">
								</td>
								<td>
									<input type='text' class='texto' :name="'nroDoc' + (index + 1)" :id="'nroDoc' + (index + 1)" size='10' value='0'>
								</td>
								<td>
									<input type='text' class='texto' :name="'observaciones' + (index + 1)" :id="'observaciones' + (index + 1)" size='15'>
								</td>
								<td>
									<button type="button" class="btn btn-sm btn-danger pt-4" @click="quitarPago(index)">
										<i class="material-icons">delete</i>
									</button>
								</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="8"></td>
								<td><b>Total:</b></td>
								<td>{{ calcularTotal }}</td>
								<td colspan="2"></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<input type='hidden' name='nroFilas' id='nroFilas' v-model='lista_pagos.length'>

		<center><input type='submit' class='boton' value='Guardar Cobranza' id='btsubmit' name='btsubmit' ></center>

		</form>

		<!-- Modal para seleccionar items -->
		<div class="modal fade" id="modalPagoCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-xl" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title"><b>AGREGAR PAGO</b></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="row align-items-center">
							<div class="col-md-2 text-end">
								<label for="exampleFormControlSelect1" class="font-weight-bold" style="color:black;">Seleccionar cliente:</label>
							</div>
							<div class="col-md-10">
								<select v-model="cod_cliente_select" @change="listaSaldoCliente" name='cliente' id='cliente' class='selectpicker' data-style="btn btn-primary" data-live-search="true" data-width="100%">
									<option value="0">Seleccione una opcion</option>
								<?php
									$sql1  = "SELECT cod_cliente, concat(nombre_cliente) 
											FROM clientes where cod_area_empresa='$globalSucursal'
											ORDER BY 2";
									$resp1 = mysqli_query($enlaceCon, $sql1);
									while($dat1=mysqli_fetch_array($resp1)){
										$codigo = $dat1[0];
										$nombre = $dat1[1];
								?>
									<option value="<?php echo $codigo; ?>"><?php echo $nombre; ?></option>
								<?php	
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div v-if="lista_estado">
								<table class="table table-striped">
									<thead class="thead-light">
										<tr>
											<th class="font-weight-bold" width="10%">Tipo Doc</th>
											<th class="font-weight-bold" width="10%">Nro.</th>
											<th class="font-weight-bold" width="30%">Fecha</th>
											<th class="font-weight-bold" width="10%">Monto</th>
											<th class="font-weight-bold" width="10%">A Cuenta</th>
											<th class="font-weight-bold" width="10%">Saldo</th>
											<th class="font-weight-bold" width="10%">Acciones</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(item, index) in lista_saldo_cliente">
											<td>{{ item.tipo_doc }}</td>
											<td>{{ item.nro_correlativo }}</td>
											<td>{{ item.fecha }}</td>
											<td>{{ item.monto_final }}</td>
											<td>{{ item.monto_cancelado }}</td>
											<td>{{ item.saldo_cliente }}</td>
											<td>
												<button type="button" class="btn btn-sm btn-success pt-4" @click="adicionaListaPago(index)">
													<i class="material-icons">add</i>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div v-else>
								<span class="alert alert-danger">Los montos de cobros no son correctos. Por favor, contacte al Administrador.</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script src="https://cdn.jsdelivr.net/npm/vue@2.7.16/dist/vue.js"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script>
		// Inicialización de VUE
		var app = new Vue({
			el: '#appVue',
			data: {
				numFilas: 0,
				// Lista Pagos
				lista_tipos_pago: [],
				lista_pagos: [], 		//ListaPagos
				total_monto: 0,
				// Modal 
				cod_cliente_select: 0,
				lista_saldo_cliente: [],
				mensaje_saldo_cliente: '',
				lista_estado: true,
			},
			mounted() {
				this.listaTiposPagos();
			},
			computed: {
				/**
				 * Calcula el total de monto que esta pagando
				 */
				calcularTotal() {
					let total = 0;
					this.lista_pagos.forEach(item => {
						let monto_pago = parseFloat(item.monto_pago) || 0;
						total += monto_pago;
					});
					return total.toFixed(2);
				},
			},
			methods: {
				/**
				 * Obtiene lista de saldo cliente
				 */
				listaTiposPagos() {
					let me = this;
					axios.get('ajaxTiposPagoVue.php', {
						params: {}
					})
					.then(function(response) {
						me.lista_tipos_pago = response.data.lista;
						// console.log(me.lista_tipos_pago);
					})
					.catch(function(error) {
						console.error(error);
					});
				},
				/**
				 * Abrir modal para agregar pago de Cliente
				 */
				abrirModalPagoCliente(){
					$('#modalPagoCliente').modal('show');
				},
				/**
				 * Quitar de lista de Pago
				 */
				quitarPago(index){
					let me = this;
					// Eliminar el ítem seleccionado del array data
					const item = me.lista_pagos.splice(index, 1)[0];
				},
				/***************************************************
				 * Obtiene lista de saldo cliente
				 */
				listaSaldoCliente() {
					let me = this;
					// let codigos_salida = me.lista_pagos.length > 0 ? me.lista_pagos.map(pago => pago.cod_salida_almacenes).join(',') : '';
					axios.get('ajaxCargarDeudasVue.php', {
						params: {
							cod_cliente: me.cod_cliente_select,
						}
					})
					.then(function(response) {
						me.lista_saldo_cliente = response.data.lista_pagos;
						me.lista_estado 	   = response.data.status;
						me.mensaje_saldo_cliente = response.data.message;
						// Filtrar la lista de saldo del cliente para eliminar los elementos con codigos_salida_almacenes encontrados en me.lista_pagos
						if (me.lista_saldo_cliente.length > 0) {
							let codigos_salida = me.lista_pagos.length > 0 ? me.lista_pagos.map(pago => pago.cod_salida_almacenes) : [];
							me.lista_saldo_cliente = me.lista_saldo_cliente.filter(saldo => !codigos_salida.includes(saldo.cod_salida_almacenes));
						}
					})
					.catch(function(error) {
						console.error(error);
					});
				},
				/**
				 * Cargar item de PAGOS de Cliente seleccionado
				 */
				adicionaListaPago(index) {
					let me = this;
					// Eliminar el ítem seleccionado del array data
					const item = me.lista_saldo_cliente.splice(index, 1)[0];
					// Agregar el ítem al array lista_saldo_cliente
					me.lista_pagos.push(item);
					$('#modalPagoCliente').modal('toggle');
				},
			}
		});
	</script>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			document.getElementById("appVue").removeAttribute("hidden");
		});
	</script>
</body>