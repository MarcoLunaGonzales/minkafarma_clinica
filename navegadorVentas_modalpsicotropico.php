<?php

require "conexionmysqli.inc";

?>

<script>
	// Abrir Modal
	$('body').on('click', '.abrirPricotropico', function(){
		// Datos recuperados
		let cod_salida_almacenes = $(this).data('cod_salida_almacenes');
		//let nro_receta 		= $(this).data('nro_receta');
		let nombre_paciente = $(this).data('nombre_paciente');
		let nombre_medico 	= $(this).data('nombre_medico');

		let nro_documento 	= $(this).data('nro_documento');
		
		$('#cod_salida_almacenes').val(cod_salida_almacenes);
		//$('#nro_receta').val(nro_receta);
		$('#nombre_paciente').val(nombre_paciente);
		$('#nombre_medico').val(nombre_medico);

		$('#div_nro_doc').html(nro_documento);

		$('#modalPsicotropico').modal('show');
	});
	// Evento al hacer clic en el botón "Continuar" del modal PSICOTRÓPICOS
	$('body').on('click', '#btnContinuar', function() {
		var cod_salida_almacenes = $('#cod_salida_almacenes').val().trim();
		//var nroReceta 	   = $('#nro_receta').val().trim();
		var nombrePaciente = $('#nombre_paciente').val().trim();
		var nombreMedico   = $('#nombre_medico').val().trim();

		if (nombrePaciente == '' || nombreMedico == '') {
			alert('Por favor, complete todos los campos marcados con *');
			return false;
		}
		return true;
	});

    // Evento al hacer clic en el botón "Actualizar" del modal PSICOTRÓPICOS
    $('body').on('click', '#btnActualizar', function() {
		var cod_salida_almacenes = $('#cod_salida_almacenes').val().trim();
        //var nroReceta 			 = $('#nro_receta').val().trim();
        var nombrePaciente 		 = $('#nombre_paciente').val().trim();
        var nombreMedico 		 = $('#nombre_medico').val().trim();

        if (nombrePaciente === '' || nombreMedico === '') {
            Swal.fire({
                type: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor, complete todos los campos marcados con *',
            });
            return false;
        }

        $.ajax({
            url: 'actualizar_psicotropico.php',
            type: 'POST',
            data: {
				cod_salida_almacenes: cod_salida_almacenes,
                nombre_paciente: nombrePaciente,
                nombre_medico: nombreMedico
            },
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    type: response.success ? 'success' : 'error',
                    title: response.success ? 'Actualización exitosa' : 'Error',
                    text: response.message,
                }).then((resp) => {
                    if (resp.value) {
                        $('#modalPsicotropico').modal('hide');
						location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr);
                Swal.fire({
                    type: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al actualizar los datos. Intente nuevamente.',
                });
            }
        });

        return true;
    });
</script>
		
<style>
	/* Estilo para el input */
	.form-control-elegant {
		border: 1px solid #ccc;
		border-radius: 4px;
		padding: 8px 12px;
		font-size: 14px;
		transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
		width: 100%;
	}

	/* Estilo para el input cuando está enfocado */
	.form-control-elegant:focus {
		border-color: #007bff;
		outline: 0;
		box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
	}
</style>
<!-- Registro para movimiento Psicotrópico -->
<div class="modal fade modal-primary" id="modalPsicotropico" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content card">
			<div class="card-header card-header-primary card-header-icon">
				<div class="card-icon">
					<i class="material-icons">settings</i>
				</div>
				<h4 class="card-title text-primary font-weight-bold">Editar Registro Producto Controlado</h4>
				<h4 class="card-title text-center text-danger font-weight-bold"><div id='div_nro_doc'></div></h4>
				<button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true" style="position:absolute;top:0px;right:0;">
					<i class="material-icons">close</i>
				</button>
			</div>
			<div class="card-body">
				<div class="row p-3">
					<input type="hidden" id="cod_salida_almacenes" value="">
					<div class="col-md-12">
						<!-- Input para Nombre del Paciente -->
						<div class="row pb-2">
							<label class="col-sm-3 col-form-label" style="color: black; font-weight: bold;">
								<span class="text-danger">*</span>  Nombre del Paciente
							</label>
							<div class="col-sm-9">
								<div class="form-group bmd-form-group">
									<input type="text" class="form-control-elegant" id="nombre_paciente" name="nombre_paciente" placeholder="Ingrese Nombre del Paciente">
								</div>
							</div>
						</div>
						<!-- Input para Nombre del Médico -->
						<div class="row">
							<label class="col-sm-3 col-form-label" style="color: black; font-weight: bold;">
								<span class="text-danger">*</span>  Nombre del Médico
							</label>
							<div class="col-sm-9">
								<div class="form-group bmd-form-group">
									<input type="text" class="form-control-elegant" id="nombre_medico" name="nombre_medico" placeholder="Ingrese Nombre del Médico">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="button" class="btn btn-primary" id="btnActualizar">Actualizar</button>
			</div>
		</div>  
	</div>
</div>
