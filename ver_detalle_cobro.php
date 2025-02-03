<?php
require("conexionmysqli.php");

ob_clean();

$codigo = $_GET['codigo'];

?>

<div class="row">
        <div class="col-md-12">
        <?php
                $sqlCobro = "SELECT c.nombre_cliente,
                                tp.nombre_tipopago,
                                ROUND(s.monto_final, 2) as monto_final, 
                                ROUND(s.monto_cancelado, 2) as monto_cancelado
                        FROM salida_almacenes s
                        LEFT JOIN clientes c ON c.cod_cliente = s.cod_cliente
                        LEFT JOIN tipos_pago tp On tp.cod_tipopago = s.cod_tipopago
                        WHERE s.cod_salida_almacenes = '$codigo'";
                $respCobro = mysqli_query($enlaceCon, $sqlCobro);
                $nombre_cliente  = "";
                $nombre_tipopago = "";
                $monto_final     = "";
                $monto_cancelado = "";
                if ($respCobro) {
                    $registroCobro = mysqli_fetch_assoc($respCobro);
                    if ($registroCobro) {
                            $nombre_cliente  = $registroCobro['nombre_cliente'];
                            $nombre_tipopago = $registroCobro['nombre_tipopago'];
                            $monto_final     = $registroCobro['monto_final'];
                            $monto_cancelado = $registroCobro['monto_cancelado'];
                    }
                }
        ?>
        <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-3 col-form-label"><b>Cliente:</b></label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                    <input class="form-control" type="text" disabled style="background: #A5F9EA;" id="cliente" value="<?=$nombre_cliente?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><b>Tipo Pago:</b></label>
                        <div class="col-sm-8">
                            <div class="form-group">
                                    <input class="form-control" type="text" disabled style="background: #A5F9EA;" id="tipo_pago" value="<?=$nombre_tipopago?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-3 col-form-label"><b>Monto Total:</b></label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                    <input class="form-control" type="text" disabled style="background: #A5F9EA;" id="monto_total" value="<?=$monto_final?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-4 col-form-label"><b>Monto Cancelado:</b></label>
                        <div class="col-sm-8">
                            <div class="form-group">
                                    <input class="form-control" type="text" disabled style="background: #A5F9EA;" id="monto_cancelado" value="<?=$monto_cancelado?>"/>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <br>                    
        </div>
        <div class="col-md-12">
            <h4 class="card-title text-dark font-weight-bold">Detalle de Cobro</h4>
            <table class="table table-bordered table-condensed">
                    <thead>
                        <tr style="background: #652BE9; color:#fff;">
                                <th style="font-size: 12px;" width="10%">Nro. Cobro</th>
                                <th style="font-size: 12px;" width="30%">Funcionario</th>
                                <th style="font-size: 12px; text-align: left;" width="30%">Observación</th>
                                <th style="font-size: 12px;" width="20%">Fecha</th>
                                <th style="font-size: 12px;" width="10%">Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sqlCobroDetalle = "SELECT c.fecha_cobro, 
                                                c.observaciones, 
                                                CONCAT(f.nombres, ' ', f.paterno) as funcionario,
                                                cd.monto_detalle,
                                                c.nro_cobro
                                        FROM cobros_cab c
                                        LEFT JOIN cobros_detalle cd ON cd.cod_cobro = c.cod_cobro
                                        LEFT JOIN funcionarios f On f.codigo_funcionario = c.cod_funcionario
                                        WHERE cd.cod_venta = '$codigo'";
                        $respCobroDetalle = mysqli_query($enlaceCon, $sqlCobroDetalle);
                        $fecha_cobro    = "";
                        $observaciones  = "";
                        $funcionario    = "";
                        $monto_detalle  = "";
                        $nro_cobro      = "";
                        $suma_total     = 0;
                        while($data = mysqli_fetch_array($respCobroDetalle)){
                            $fecha_cobro    = $data['fecha_cobro'];
                            $observaciones  = $data['observaciones'];
                            $funcionario    = $data['funcionario'];
                            $monto_detalle  = $data['monto_detalle'];
                            $nro_cobro      = $data['nro_cobro'];

                            $suma_total += $monto_detalle;
                    ?>
                        <tr>
                            <td><?= $nro_cobro ?></td>
                            <td style="text-align: left !important;"><?= $funcionario ?></td>
                            <td style="text-align: left !important;"><?= $observaciones ?></td>
                            <td><?= $fecha_cobro ?></td>
                            <td style="text-align: right;"><?= number_format($monto_detalle, 2) ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                    <tfoot>
                    <td colspan="4" style="text-align: right;"><b>Total</b></td>
                    <td><b><?= number_format($suma_total, 2) ?></b></td>
                    </tfoot>
            </table>  
        </div>
</div>