<?php

if( !function_exists('ceiling') )
{
    function ceiling($number, $significance = 1)
    {
        return ( is_numeric($number) && is_numeric($significance) ) ? (ceil($number/$significance)*$significance) : false;
    }
}

require('function_formatofecha.php');
require('funcion_nombres.php');
require('funciones.php');


require "conexionmysqli.inc";
require("estilos_almacenes.inc");


 error_reporting(E_ALL);
 ini_set('display_errors', '1');


$rpt_territorio=$_GET['rpt_territorio'];
$fecha_ini=$_GET['fecha_ini'];
$fecha_fin=$_GET['fecha_fin'];
$hora_ini=$_GET['hora_ini'];
$hora_fin=$_GET['hora_fin'];

$globalLogo=$_COOKIE["global_logo"];

ob_start();
echo "<html><head><link href='stilos.css' rel='stylesheet' type='text/css'></head></body>";
echo "<center><table border=0 class='linea' width='100%'><tr><td align='left'>
<img src='imagenes/$globalLogo' width='180' 
                        style='
                            position: fixed; 
                            top: 10px; 
                            left: 40px; 
                            z-index: 1000; '>
						</td>
<th></th></tr></table></center>";
//desde esta parte viene el reporte en si
$fecha_iniconsulta=$fecha_ini;
$fecha_iniconsultahora=$fecha_iniconsulta." ".$hora_ini.":00";
$fecha_finconsultahora=$fecha_fin." ".$hora_fin.":59";
$fecha_reporte=date("d/m/Y");
$montoCajaChica=0;
echo "<center><h3 style='margin: 0px;'><b>Reporte Arqueo Diario de Caja</b></h3>
	<h3 style='margin: 0px;'><b>Fecha Arqueo: ".strftime('%d/%m/%Y',strtotime($fecha_ini))." ".$hora_ini."</b> &nbsp;&nbsp;&nbsp; <b>Fecha Reporte: ".$fecha_reporte." ".$hora_fin."</b></h3></center>";

	
$sql="SELECT s.`fecha`,  
	(select c.nombre_cliente from clientes c where c.`cod_cliente`=s.cod_cliente) as cliente, 
	s.`razon_social`, s.`observaciones`, 
	(select t.`abreviatura` from `tipos_docs` t where t.`codigo`=s.cod_tipo_doc),
	s.`nro_correlativo`, s.`monto_final`, s.cod_tipopago, (select tp.nombre_tipopago from tipos_pago tp where tp.cod_tipopago=s.cod_tipopago), 
	s.hora_salida,s.cod_chofer,s.cod_salida_almacenes,s.salida_anulada,s.monto_cancelado_usd,s.tipo_cambio,
	COALESCE(TRIM(CONCAT(f.nombres, ' ', f.paterno, ' ', f.materno)), '-') AS personal_clinica_caja
	from `salida_almacenes` s 
	LEFT JOIN funcionarios f ON f.codigo_funcionario = s.cod_funcionario_caja
	where s.`cod_tiposalida`=1001 and
	s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a where a.`cod_ciudad`='$rpt_territorio')
	and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fecha_iniconsultahora' and '$fecha_finconsultahora' and s.cod_tipopago=1 ";
/*and s.salida_anulada=0*/

$sqlTarjetas="SELECT s.`fecha`,  
	(select c.nombre_cliente from clientes c where c.`cod_cliente`=s.cod_cliente) as cliente, 
	s.`razon_social`, s.`observaciones`, 
	(select t.`abreviatura` from `tipos_docs` t where t.`codigo`=s.cod_tipo_doc),
	s.`nro_correlativo`, s.`monto_final`, s.cod_tipopago, (select tp.nombre_tipopago from tipos_pago tp where tp.cod_tipopago=s.cod_tipopago), 
	s.hora_salida,s.cod_chofer,s.cod_salida_almacenes,s.monto_cancelado_usd,s.tipo_cambio,
 	0 as nombre_banco, (SELECT nro_tarjeta FROM tarjetas_salidas where cod_salida_almacen=s.cod_salida_almacenes limit 1)numero_tarjeta,
	COALESCE(TRIM(CONCAT(f.nombres, ' ', f.paterno, ' ', f.materno)), '-') AS personal_clinica_caja
	from `salida_almacenes` s 
	LEFT JOIN funcionarios f ON f.codigo_funcionario = s.cod_funcionario_caja
	where s.`cod_tiposalida`=1001 and s.salida_anulada=0 and
	s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a where a.`cod_ciudad`='$rpt_territorio')
	and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fecha_iniconsultahora' and '$fecha_finconsultahora' and s.cod_tipopago!=1 ";

	$sql.=" and s.cod_tipo_doc in (1,2,3,4)";
	$sqlTarjetas.=" and s.cod_tipo_doc in (1,2,3,4)";

$sql.=" order by s.fecha, s.hora_salida";
$sqlTarjetas.=" order by s.fecha, s.hora_salida";

//echo $sqlTarjetas;
$resp=mysqli_query($enlaceCon,$sql);
$respTarjeta=mysqli_query($enlaceCon,$sqlTarjetas);

?>
<input type="hidden" name='rpt_territorio' id='rpt_territorio' value="<?=$rpt_territorio?>">
<input type="hidden" name='fecha_ini' id='fecha_ini' value="<?=$fecha_ini?>">
<input type="hidden" name='hora_ini' id='hora_ini' value="<?=$hora_ini?>">
<input type="hidden" name='fecha_fin' id='fecha_fin' value="<?=$fecha_fin?>">
<input type="hidden" name='hora_fin' id='hora_fin' value="<?=$hora_fin?>">
<?php
echo "<br><table align='center' class='textomediano' width='100%'>
<tr><th colspan='8'>Detalle de Ventas (EFECTIVO)</th></tr>
<tr>
<th>Fecha</th>
<th>Cajero(a)</th>
<th>Cliente</th>
<th>Razon Social</th>
<th>TipoPago</th>
<th>Documento</th>
<th style='background-color: #4a5cb0; color: white;'>Cajero Clinica</th>
<th>Monto [Bs]</th>
</tr>";

$totalVenta=0;
$totalEfectivo=0;
$totalEfectivoUsd=0;
$totalEfectivoBs=0;
$totalEfectivoF=0;
$totalTarjetaF=0;
$totalTarjeta=0;

while($datos=mysqli_fetch_array($resp)){
    $codigoSalida=$datos['cod_salida_almacenes'];	
	$fechaVenta=$datos[0];
	$nombreCliente=$datos[1];
	$razonSocial=$datos[2];
	$obsVenta=$datos[3];
	$datosDoc=$datos[4]."-".$datos[5];
	$montoVenta=$datos[6];
	//$montoVenta=number_format($montoVenta,1,'.','');
	$totalVenta=$totalVenta+$montoVenta;	
	
	$codTipoPago=$datos[7];
	$nombreTipoPago=$datos[8];
	$horaVenta=$datos[9];
	$personalCliente=nombreVisitador($enlaceCon, $datos['cod_chofer']);
	$montoDolares=$datos['monto_cancelado_usd'];
	$tipoCambio=$datos['tipo_cambio'];	
	if($codTipoPago==1 && $datos['salida_anulada']==0){
		$totalEfectivoBs+=($montoDolares*$tipoCambio);
	    $totalEfectivo+=$montoVenta;
	    $totalEfectivoUsd+=$montoDolares;		
	}else{
		if($datos['salida_anulada']==0){
			//$montoVenta=number_format($montoVenta,1,'.','');
			$totalTarjeta+=$montoVenta;		
		}
	}
	$personal_clinica_caja=$datos['personal_clinica_caja'];
	$montoVentaFormat=number_format($montoVenta,2,".",",");
	$totalEfectivoF=number_format($totalEfectivo,2,".",",");
	$totalEfectivoFUSD=number_format($totalEfectivoUsd,2,".",",");
	$totalEfectivoFBS=number_format($totalEfectivoBs,2,".",",");
	$totalTarjetaF=number_format($totalTarjeta,2,".",",");
	
	if($datos['salida_anulada']==0){
	  	echo "<tr>
		<td>$fechaVenta $horaVenta</td>
		<td>$personalCliente</td>
		<td>$nombreCliente</td>
		<td>$razonSocial</td>
		<td>$nombreTipoPago</td>
		<td>$datosDoc</td>
		<td>$personal_clinica_caja</td>
		<td align='right'>$montoVentaFormat</td>
		</tr>";
	}else{
		echo "<tr style='color:red'>
		<td><strike>$fechaVenta $horaVenta</strike></td>
		<td><strike>$personalCliente</strike></td>
		<td><strike>$nombreCliente</strike></td>
		<td><strike>$razonSocial</strike></td>
		<td><strike>$nombreTipoPago</strike></td>
		<td><strike>$datosDoc</strike></td>
		<td>$personal_clinica_caja</td>
		<td align='right'>$montoVentaFormat</td>
		</tr>";
	} 
	
}

$totalVentaFormat=number_format($totalVenta,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<th>Total Efectivo:</th>
	<th align='right'>$totalEfectivoF</th>
</tr>";
echo "</table></br>";

//VENTAS TARJETA


echo "<br><table align='center' class='textomediano' width='100%'>
<tr><th colspan='10'>Detalle de Ventas Otros Tipos de Pago (Tarjeta D/C- Transferencia)</th></tr>
<tr>
<th>Fecha</th>
<th>Cajero(a)</th>
<th>Cliente</th>
<th>Razon Social</th>
<th>TipoPago</th>
<th>Documento</th>
<th>Banco</th>
<th>Tarjeta</th>
<th style='background-color: #4a5cb0; color: white;'>Cajero Clinica</th>
<th>Monto [Bs]</th>
</tr>";

$totalTarjeta=0;
while($datos=mysqli_fetch_array($respTarjeta)){
    $codigoSalida=$datos['cod_salida_almacenes'];	
	$fechaVenta=$datos[0];
	$nombreCliente=$datos[1];
	$razonSocial=$datos[2];
	$obsVenta=$datos[3];
	$datosDoc=$datos[4]."-".$datos[5];
	$montoVenta=$datos[6];
	//$montoVenta=number_format($montoVenta,1,'.','');
	$totalVenta=$totalVenta+$montoVenta;
	$codTipoPago=$datos[7];
	$nombreTipoPago=$datos[8];
	$horaVenta=$datos[9];
	$bancoNombre=$datos['nombre_banco'];
	$tarjetaNumero=$datos['numero_tarjeta'];
	$personal_clinica_caja=$datos['personal_clinica_caja'];
	$personalCliente=nombreVisitador($enlaceCon, $datos['cod_chofer']);
		
	if($codTipoPago==1){
		$totalEfectivo+=$montoVenta;
	}else{
		//$montoVenta=number_format($montoVenta,1,'.','');
		$totalTarjeta+=$montoVenta;
	}

	if($bancoNombre==""){
		$bancoNombre="OTRO";
	}
	$montoVentaFormat=number_format($montoVenta,2,".",",");
	$totalEfectivoF=number_format($totalEfectivo,2,".",",");
	$totalTarjetaF=number_format($totalTarjeta,2,".",",");
	
	echo "<tr>
	<td>$fechaVenta $horaVenta</td>
	<td>$personalCliente</td>
	<td>$nombreCliente</td>
	<td>$razonSocial</td>
	<td>$nombreTipoPago</td>
	<td>$datosDoc</td>
	<td>$bancoNombre</td>
	<td align='right'>$tarjetaNumero</td>
	<td>$personal_clinica_caja</td>
	<td align='right'>$montoVentaFormat</td>
	</tr>";
}

$totalVentaFormat=number_format($totalVenta,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<th>Total Otros Tipos de Pago</th>
	<th align='right'>$totalTarjetaF</th>
</tr>";

echo "</table></br>";



// Cobranza
$sqlCobranza="SELECT 
					c.cod_cobro,
					DATE_FORMAT(c.fecha_cobro,'%d-%m-%Y') as fecha, 
					CONCAT(cli.nombre_cliente, ' ', cli.paterno) as cliente, 
					CONCAT(t.abreviatura,'-',sa.nro_correlativo) as nota,
					tp.nombre_tipopago as tipoPago,
					cd.monto_detalle as montoPago,
					cd.cod_tipopago
			FROM cobros_cab c
			LEFT JOIN cobros_detalle cd ON cd.cod_cobro = c.cod_cobro
			LEFT JOIN salida_almacenes sa ON cd.cod_venta = sa.cod_salida_almacenes
			LEFT JOIN tipos_pago tp ON tp.cod_tipopago = cd.cod_tipopago
			LEFT JOIN tipos_docs t ON t.codigo = sa.cod_tipo_doc
			LEFT JOIN clientes cli ON cli.cod_cliente = cd.cod_cliente
			WHERE c.cod_estado = 1
			AND c.fecha_cobro BETWEEN '$fecha_ini' and '$fecha_fin'
			HAVING montoPago is not null";
// echo $sqlCobranza;
$respCobranza = mysqli_query($enlaceCon,$sqlCobranza);
echo "<br><table align='center' class='textomediano' width='100%'>
		<tr>
			<th colspan='5'>Cobranzas de Ventas al Credito</th></tr>
			<tr>
				<th>Fecha</th>
				<th>Cliente</th>
				<th>Nota</th>
				<th>TipoPago</th>
				<th>Monto [Bs]</th>
			</tr>";

$total_cobranza = 0;
while($datos=mysqli_fetch_array($respCobranza)){
    $cobroCodigo	= $datos['cod_cobro'];	
	$cobroFecha		= $datos['fecha'];
	$cobroCliente	= $datos['cliente'];
	$cobroNota		= $datos['nota'];
	$cobroTipoPago	= $datos['tipoPago'];
	$cobroMonto		= $datos['montoPago'];
	$cobrocodTipopago = $datos['cod_tipopago'];
	
	echo "<tr>
	<td>$cobroFecha</td>
	<td>$cobroCliente</td>
	<td>$cobroNota</td>
	<td>$cobroTipoPago</td>
	<td align='right'>$cobroMonto</td>
	</tr>";
	$total_cobranza = $total_cobranza + $cobroMonto;

	if($cobrocodTipopago==1){
		$totalEfectivo  += $cobroMonto;
		$totalEfectivoF += $cobroMonto;
	}else{
		$totalTarjeta  +=$cobroMonto;
		$totalTarjetaF += $cobroMonto;
	}
}

$totalCobranzaF=number_format($total_cobranza,2,".",",");
echo "<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<th align='right'>$totalCobranzaF</th>
</tr>";
echo "</table></br>";
// Fin Cobranza





$totalGastos=0;

$saldoCajaChica=$montoCajaChica+$totalTarjeta-$totalGastos;
$saldoCajaChicaF=number_format($saldoCajaChica,2,".",",");

$saldoCajaChica2=$montoCajaChica+$totalEfectivo-$totalGastos;
$saldoCajaChica2F=number_format($saldoCajaChica2,2,".",",");


$totalIngresos=($totalEfectivo+$totalTarjeta);
$totalIngresosFormat=number_format($totalIngresos,2,".",",");
echo "<br><table align='center' class='textomediano' width='100%'>";

$totalVentaFormat=number_format($totalVenta,2,".",",");
echo "<tr style='font-size:15px;'>
	<th>Total Efectivo(Ventas) + Cobranzas Efectivo:</th>
	<th align='right'>$totalEfectivoF</th>
</tr>";
echo "<tr style='font-size:15px;'>
	<th>Total Otros Tipos de Pago </th>
	<th align='right'>$totalTarjetaF</th>
</tr>";
echo "<tr style='font-size:25px;'>
	<th>Total Ingresos:</th>
	<th align='right'>$totalIngresosFormat</th>
</tr>";
echo "</table></br></body>";
echo "</html>";

$html = ob_get_clean();

echo $html;

?>

<style>
    .floating-buttons {
        position: fixed;
        bottom: 20px;
        left: 20px;
        display: flex;
        flex-direction: row;
        gap: 10px;
        align-items: center;
    }

    .floating-buttons button {
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>

<div class="floating-buttons">
    <button type="button" class="btn btn-success" id="btnCierreCaja" onclick="btnCierreCaja()">Cerrar Caja</button>
    <button type="button" class="btn btn-danger" onclick="window.close();">Cancelar</button>
</div>


<script>
	function btnCierreCaja(){
        Swal.fire({
            title: "¿Está seguro?",
            text: "¿Desea realizar el cierre de caja?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#4caf50",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, cerrar caja",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.value) {

                let fechaInicio = $("#fecha_ini").val();
                let horaInicio  = $("#hora_ini").val();
                let fechaFin 	= $("#fecha_fin").val();
                let horaFin 	= $("#hora_fin").val();

                if (!fechaInicio || !horaInicio || !fechaFin || !horaFin) {
                    Swal.fire("Error", "Debe seleccionar fechas y horas válidas", "error");
                    return;
                }

                let fechaHoraInicio = fechaInicio + " " + horaInicio;
                let fechaHoraFin = fechaFin + " " + horaFin;
				
				Swal.fire({
					title: "Procesando...",
					text: "Por favor, espere mientras se realiza el cierre de caja.",
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});
                $.ajax({
                    url: "./ajax_cierreCajaPersonal.php",
                    type: "POST",
                    data: {
                        fecha_inicio: fechaHoraInicio,
                        fecha_fin: fechaHoraFin
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {
							Swal.fire({
								title: "Éxito",
								text: "Cierre de caja realizado correctamente",
								type: "success",
								allowOutsideClick: false
							}).then(() => {
								location.reload();
							});
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Hubo un problema con la solicitud", "error");
                    }
                });
            }
        });
	}
</script>