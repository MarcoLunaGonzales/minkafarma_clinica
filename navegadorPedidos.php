<?php

require("conexionmysqli.php");
require('funciones.php');
require("estilos_almacenes.inc");

// Cargo 1:Admin, 0:Nomal
$global_admin_cargo = $_COOKIE["global_admin_cargo"];
$global_usuario     = $_COOKIE["global_usuario"];

?>
<html>
    <head>
        <title>Lista Pedidos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="lib/externos/jquery/jquery-ui/completo/jquery-ui-1.8.9.custom.css" rel="stylesheet" type="text/css"/>
        <link href="lib/css/paneles.css" rel="stylesheet" type="text/css"/>
        <!-- CSS de DataTables -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
        <!-- DataTables -->
        <script src="assets/js/plugins/jquery.dataTables.min.js"></script>
        <!-- Extensión de idioma para DataTables en español -->
        <script src="https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"></script>
    
    </head>
    <body>
        <div class="wrapper">
            <div class="">
                <div class="content" style="margin-top: 0px;">
                    <div class="content-fluid">
                        <div class="card">
                            <!-- Titulo -->
                            <div class="card-header card-header-primary card-header-icon">
                                <center>
                                    <h4 class="card-title"><b>LISTADO DE PEDIDOS</b></h4>
                                </center>
                                <h4 class="mb-0">
                                    <a href="registrar_salidapedidos.php" class="btn btn-sm btn-success">
                                        <i class="material-icons">add</i> Nuevo Pedido
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" id="btnFiltro">
                                        <i class="material-icons">filter_list</i> Filtro
                                    </button>
                                </h4>
                            </div>
                            <!-- Contenido -->
                            <div class="card-body pt-2">
                                <div class="table-responsive">
                                    <!-- INICIO -->
                                    <table class="table table-hover" id="miTabla" style="font-size: 12px;">
                                    <!-- <table class="table table-hover" id="" style="font-size: 12px;"> -->
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="3%" style="font-size: 11px;">Nro.</th>
                                                <th width="10%" style="font-size: 11px;">Cliente</th>
                                                <th width="10%" style="font-size: 11px;">Fecha Registro</th>
                                                <th width="10%" style="font-size: 11px;">Vendedor</th>
                                                <th width="5%" style="font-size: 11px;">Tipo Pago</th>
                                                <th width="17%" style="font-size: 11px;">Razón Social</th>
                                                <th width="10%" style="font-size: 11px;">NIT</th>
                                                <th width="5%" style="font-size: 11px;">Monto</th>
                                                <th width="5%" style="font-size: 11px;">Obs.</th>
                                                <th width="10%" style="font-size: 11px;" class="text-center">-</th>
                                                <th width="8%" style="font-size: 11px;" class="text-center">Estado</th>
                                                <th width="7%" style="font-size: 11px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $consulta = "SELECT 
                                                            CONCAT(c.nombre_cliente) as cliente,
                                                            p.codigo,
                                                            a.nombre_almacen,
                                                            td.nombre as tipo_doc,
                                                            DATE_FORMAT(p.fecha,'%d-%m-%Y %H:%i:%s') as fecha,
                                                            p.observaciones,
                                                            p.estado,
                                                            p.numero,
                                                            p.pedido_anulado,
                                                            c.nombre_cliente,
                                                            p.monto_total,
                                                            p.descuento,
                                                            p.monto_final,
                                                            p.razon_social,
                                                            p.nit,
                                                            CONCAT(f.nombres, ' ', f.paterno, ' ', f.materno) as vendedor,
                                                            tp.nombre_tipopago as tipopago,
                                                            ptd.descripcion as documento_identidad,
                                                            p.siat_complemento,
                                                            p.created_by,
                                                            p.created_at,
                                                            p.cod_tipopreciogeneral,
                                                            p.fecha_a_facturar,
                                                            IFNULL((SELECT 1 FROM salida_almacenes s WHERE s.cod_pedido = p.codigo order by s.cod_salida_almacenes desc limit 0,1), 0) AS proceso_salida
                                                        FROM pedidos p
                                                        LEFT JOIN almacenes a ON a.cod_almacen = p.cod_almacen
                                                        LEFT JOIN tipos_docs td ON td.codigo = p.cod_tipo_doc
                                                        LEFT JOIN clientes c ON c.cod_cliente = p.cod_cliente
                                                        LEFT JOIN funcionarios f ON f.codigo_funcionario = p.cod_funcionario
                                                        LEFT JOIN tipos_pago tp ON tp.cod_tipopago = p.cod_tipopago
                                                        LEFT JOIN siat_sincronizarparametricatipodocumentoidentidad ptd ON ptd.codigo = p.siat_codigotipodocumentoidentidad
                                                        WHERE p.cod_almacen = '$global_almacen' ";
                                                // FILTRO
                                                $fil_cod_cliente  = $_GET['fil_cod_cliente'] ?? '';
                                                if(!empty($fil_cod_cliente)){
                                                    $consulta .= " AND p.cod_cliente = '$fil_cod_cliente' ";
                                                }
                                                $fil_nro_pedido   = $_GET['fil_nro_pedido'] ?? '';
                                                if(!empty($fil_nro_pedido)){
                                                    $consulta .= " AND p.numero = '$fil_nro_pedido' ";
                                                }
                                                $fil_fecha_inicio = $_GET['fil_fecha_inicio'] ?? '';
                                                $fil_fecha_fin    = $_GET['fil_fecha_fin'] ?? '';
                                                $fil_estado    = $_GET['fil_estado'] ?? '';

                                                if(!empty($fil_fecha_inicio) && !empty($fil_fecha_fin)){
                                                    $consulta .= " AND DATE(p.fecha) BETWEEN '$fil_fecha_inicio' AND '$fil_fecha_fin' ";
                                                }

                                                if(empty($fil_estado)){
                                                    //FORZAMOS QUE SE VEAN TODOS LOS ESTADOS DEL PEDIDO
                                                    $fil_estado=-1;
                                                }                                
                                                // VENDEDOR
                                                if($global_admin_cargo == 0){
                                                    $consulta .= " AND p.cod_funcionario = '$global_usuario' ";
                                                }

                                                $consulta .= " ORDER BY p.fecha DESC LIMIT 0, 500";

                                                //echo $consulta;


                                                $resp = mysqli_query($enlaceCon, $consulta);

                                                if(mysqli_num_rows($resp) > 0) {
                                                    $resultados = mysqli_fetch_all($resp, MYSQLI_ASSOC);

                                                    foreach($resultados as $dat) {
                                                    /********************************************************/
                                                    /*ESTA PARTE ES PARA VERIFICACION DE LA VENTA Y SU PAGO*/
                                                    /********************************************************/
                                                    $codigoPedido=$dat['codigo'];
                                                    $sqlVentas="SELECT s.cod_salida_almacenes, t.abreviatura, s.nro_correlativo, s.monto_final, s.monto_cancelado, 
                                                        s.cod_tipopago, s.salida_anulada
                                                        from salida_almacenes s, tipos_docs t where s.cod_tipo_doc=t.codigo and s.cod_pedido='$codigoPedido'";
                                                    $respVentas=mysqli_query($enlaceCon, $sqlVentas);
                                                    $txtVentas="";

                                                    $conf_controlPago = obtenerValorConfiguracion($enlaceCon, 59);

                                                    $ventaAnulada=0;
                                                    while($datVentas=mysqli_fetch_array($respVentas)){  
                                                        $montoVenta=$datVentas[3];
                                                        $montoCancelado=$datVentas[4];
                                                        $codTipoPago=$datVentas[5];
                                                        $ventaAnulada=$datVentas[6];


                                                        $estadoPago = 1; // Inicialmente establece el estado de pago como pagado (1)
                                                        // Verifica las condiciones de pago según la configuración
                                                        if ($conf_controlPago == 1 && $codTipoPago == 4 && $montoCancelado < $montoVenta) {
                                                            // Si la configuración es de crédito y el tipo de pago es 4 (crédito) y el monto cancelado es menor al monto de venta
                                                            $estadoPago = 0; // Cambia el estado de pago a pendiente (0)
                                                        } elseif ($conf_controlPago == 2 && $montoCancelado < $montoVenta) {
                                                            // Si la configuración es para todos los pagos y el monto cancelado es menor al monto de venta
                                                            $estadoPago = 0; // Cambia el estado de pago a pendiente (0)
                                                        }
                                                        // Determina el icono basado en el estado de pago
                                                        $icono = ($estadoPago) ? '<i class="material-icons medium text-success" title="Pagado en su totalidad">check_circle</i>' : '<i class="material-icons medium text-warning" title="Pendiente de pago">hourglass_empty</i>';
                                                        if($ventaAnulada){
                                                            $txtVentas.="<span style='color:red; text-decoration: line-through;'>".$datVentas[1]."-".$datVentas[2].$icono."</span>";
                                                        }else{
                                                            $txtVentas.=$datVentas[1]."-".$datVentas[2].$icono."<br>";
                                                        }

                                                    }
                                                    /********************************************************/
                                                    /********************************************************/
                                                    /********************************************************/
                                                    $estilosExtra="";
                                                    if($dat['estado'] == 2){
                                                        $estilosExtra='style="background-color: #F8BBD0; text-decoration: line-through;"';
                                                    }else if($dat['proceso_salida'] == 1){
                                                        $estilosExtra='style="background-color: #eaf7ea;"';
                                                    }
                                                
                                                        if( $fil_estado==-1 || ($fil_estado==2 && ($dat['estado']==2)) || ($fil_estado==1 && ($dat['proceso_salida']!=1 && $dat['estado']!=2)) || ($fil_estado==0 && ($dat['proceso_salida']==1)) ){
                                                        ?>
                                                        <tr <?=$estilosExtra;?> >    
                                                            <td class="pt-2 pb-2">
                                                                <a href="navegador_detallepedido.php?cod_pedido=<?=$dat['codigo']?>" target="_blank">
                                                                    <?=$dat['numero']?>
                                                                </a>
                                                            </td>
                                                            <td class="pt-2 pb-2"><?=$dat['cliente']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['fecha']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['vendedor']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['tipopago']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['razon_social']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['nit']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['monto_final']?></td>
                                                            <td class="pt-2 pb-2"><?=$dat['observaciones']?> <?=$dat['estado']." ".$ventaAnulada." ".$fil_estado;?></td>
                                                            <td class="pt-2 pb-2" style="text-align: right !important;"><?=$txtVentas?></td>
                                                            <td class="pt-2 pb-2" style="text-align: center !important;">
                                                                <?php
                                                                    if($dat['estado'] == 2){
                                                                ?>
                                                                <span class="badge badge-danger"><i class="material-icons small">cancel</i> Anulado</span>
                                                                <?php
                                                                    }else if($dat['proceso_salida'] == 1){
                                                                ?>
                                                                <span class="badge badge-success"><i class="material-icons small">check_circle</i> Procesado</span>
                                                                <?php
                                                                    }else{
                                                                ?>
                                                                <span class="badge badge-warning"><i class="material-icons small">warning</i> Sin procesar</span>
                                                                <?php
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td class="pt-2 pb-2">
                                                                <?php
                                                                //if($global_admin_cargo==1){
                                                                    if( ($dat['proceso_salida'] == 0 || $ventaAnulada==1) && $dat['estado'] == 1){
                                                                ?>
                                                                
                                                                    <?php
                                                                        if($global_admin_cargo == 1){
                                                                    ?>
                                                                    <a href="registrar_salidaventas_pedido.php?cod_pedido=<?=$dat['codigo']?>" class="btn btn-sm btn-info pt-4 pb-1 pl-2 pr-2" title="Generar Venta">
                                                                        <i class="material-icons">description</i>
                                                                    </a>
                                                                    <?php
                                                                    }
                                                                    ?>

                                                                    <?php
                                                                        if($dat['estado'] == 1){
                                                                    ?>
                                                                    <button class="btn btn-sm btn-danger pt-4 pb-1 pl-2 pr-2 anular-pedido" data-cod-pedido="<?=$dat['codigo']?>" title="Anular Pedido">
                                                                        <i class="material-icons">cancel</i>
                                                                    </button>
                                                                    <?php
                                                                        }
                                                                    ?>
                                                                <?php
                                                                    }
                                                                //}
                                                                ?>
                                                            </td>
                                                        </tr>
                                                <?php
                                                        }   
                                                    }
                                                }
                                                ?>
                                        </tbody>
                                    </table>
                                    <!-- FIN -->
                                </div>
                            </div>
                        </div>

            
                        <div class="modal" id="filtroModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalTituloCambio"><b>Filtro de Pedidos</b></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="navegadorPedidos.php" method="GET">
                                            <div class="row pb-2">
                                                <label class="col-sm-3 elegant-label">Cliente:</label>
                                                <div class="col-sm-9">
                                                    <select name='fil_cod_cliente' id='fil_cod_cliente' class='selectpicker form-control' data-style='btn btn-rose' data-live-search="true">
                                                        <option value=''>TODOS</option>
                                                        <?php
                                                            $sql3="SELECT c.cod_cliente, c.nombre_cliente
                                                                    FROM clientes c
                                                                    ORDER BY c.nombre_cliente ASC";
                                                            $resp3=mysqli_query($enlaceCon,$sql3);
                                                            while($dat3=mysqli_fetch_array($resp3)){
                                                                $select = $dat3['cod_cliente'] == $fil_cod_cliente ? 'selected' : '';
                                                        ?>
                                                                <option value="<?=$dat3['cod_cliente']?>" <?=$select?>><?=$dat3['nombre_cliente']?></option>
                                                        <?php		
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row pb-2">
                                                <label class="col-sm-3 elegant-label">N° de Pedido:</label>
                                                <div class="col-sm-9">
                                                    <input class="elegant-input" type="text" placeholder="Ingrese el nro de pedido" name="fil_nro_pedido" id="fil_nro_pedido" value="<?=$fil_nro_pedido?>"/>
                                                </div>
                                            </div>
                                            <div class="row pb-2">
                                                <label class="col-sm-3 elegant-label">Fecha Inicio:</label>
                                                <div class="col-sm-9">
                                                    <input class="elegant-input" type="date" name="fil_fecha_inicio" id="fil_fecha_inicio" value="<?=date("Y-m-01")?>"/>
                                                </div>
                                            </div>
                                            <div class="row pb-2">
                                                <label class="col-sm-3 elegant-label">Fecha Fin:</label>
                                                <div class="col-sm-9">
                                                    <input class="elegant-input" type="date" name="fil_fecha_fin" id="fil_fecha_fin" value="<?=date("Y-m-d")?>"/>
                                                </div>
                                            </div>

                                            <div class="row pb-2">
                                                <label class="col-sm-3 elegant-label">Estado:</label>
                                                <div class="col-sm-9">
                                                    <select name='fil_estado' id='fil_estado' class='selectpicker form-control' data-style='btn btn-success' data-live-search="true">
                                                    <option value='-1'>Ver Todo</option>
                                                    <option value='1'>Sin Procesar</option>
                                                    <option value='0'>Procesado</option>
                                                    <option value='2'>Anulado</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="modal-footer p-0 pt-2">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <style>
            /**
            * ESTILO DE SELECT
            **/
            .bootstrap-select .dropdown-toggle .filter-option {
                text-align: left;
            }
            .bootstrap-select .dropdown-menu {
                max-height: 300px;
                overflow-y: auto;
            }
            .bootstrap-select .dropdown-menu.open {
                display: block;
            }
            /**
            * ESTILO DE FORMULARIO
            **/
            .elegant-label {
                font-family: 'Poppins', sans-serif;
                font-weight: bold;
                color: #6c757d;
                display: flex;
                align-items: center;
                margin-bottom: 0;
            }

            .elegant-label span.text-danger {
                margin-right: 5px;
            }
            .elegant-input {
                width: 100%;
                border: 2px solid #ced4da;
                border-radius: 5px;
                padding: 5px 5px;
                transition: all 0.3s ease;
            }

            .elegant-input:focus {
                border-color: #80bdff;
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            }

            .elegant-input::placeholder {
                color: #999;
                opacity: 1;
            }
        </style>
        
        <script>
            $(document).ready(function() {
                $('#miTabla').DataTable({
                    searching: true,
                    paging: true,
                    ordering: false,
                    lengthMenu: [ 100, 150, 250, 300, 350 ],
                    language: {
                        "decimal": "",
                        "emptyTable": "No hay datos disponibles en la tabla",
                        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                        "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                        "infoFiltered": "(filtrado de _MAX_ registros totales)",
                        "infoPostFix": "",
                        "thousands": ",",
                        "lengthMenu": "Mostrar _MENU_ registros",
                        "loadingRecords": "Cargando...",
                        "processing": "Procesando...",
                        "search": "Buscar:",
                        "zeroRecords": "No se encontraron registros coincidentes",
                        "paginate": {
                            "first": "Primero",
                            "last": "Último",
                            "next": "Siguiente",
                            "previous": "Anterior"
                        },
                        "aria": {
                            "sortAscending": ": activar para ordenar la columna ascendente",
                            "sortDescending": ": activar para ordenar la columna descendente"
                        }
                    },
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            var input = $('<input type="text" class="form-control" placeholder="Buscar...">')
                                .appendTo($(column.header()))
                                .on('keyup', function () {
                                    column.search($(this).val()).draw();
                                });
                        });
                    }
                });
                // Anula Pedido
                $('body').on('click', '.anular-pedido', function(e){
                    e.preventDefault();
                    var codPedido = $(this).data('cod-pedido');
                    Swal.fire({
                        title: '¿Está seguro de anular este pedido?',
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, anular',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.value) {
                            location.href = 'guardarPedidoAnulado.php?cod_pedido=' + codPedido;
                        }
                    });
                });
                // Abrir modal Filtro
                $('body').on('click', '#btnFiltro', function(){
                    $('#filtroModal').modal('show');
                });
            });
        </script>
    </body>
</html>


