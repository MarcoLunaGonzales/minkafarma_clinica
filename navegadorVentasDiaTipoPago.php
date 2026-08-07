<?php
ob_start();

require('conexionmysqli.php');

date_default_timezone_set('America/La_Paz');

$globalAlmacen = isset($_COOKIE['global_almacen'])
    ? (int) $_COOKIE['global_almacen']
    : 0;

$ventas = array();
$tiposPago = array();
$mensajeError = '';

if ($globalAlmacen <= 0) {
    $mensajeError = 'No se encontró el almacén activo de la sesión.';
} else {
    $sqlVentas = "SELECT
                    s.cod_salida_almacenes,
                    s.nro_correlativo,
                    s.fecha,
                    s.hora_salida,
                    s.razon_social,
                    s.nit,
                    s.monto_final,
                    s.cod_tipopago,
                    tp.nombre_tipopago,
                    s.salida_anulada,
                    CONCAT(COALESCE(f.paterno, ''), ' ', COALESCE(f.nombres, '')) AS vendedor
                  FROM salida_almacenes s
                  INNER JOIN tipos_pago tp
                          ON tp.cod_tipopago = s.cod_tipopago
                  LEFT JOIN funcionarios f
                         ON f.codigo_funcionario = s.cod_chofer
                  WHERE s.cod_almacen = ?
                    AND s.cod_tiposalida = 1001
                    AND s.cod_tipo_doc IN (1, 4)
                    AND s.fecha = CURDATE()
                  ORDER BY s.hora_salida DESC, s.nro_correlativo DESC";

    $stmtVentas = mysqli_prepare($enlaceCon, $sqlVentas);

    if ($stmtVentas) {
        mysqli_stmt_bind_param($stmtVentas, 'i', $globalAlmacen);
        mysqli_stmt_execute($stmtVentas);
        $resultadoVentas = mysqli_stmt_get_result($stmtVentas);

        while ($fila = mysqli_fetch_assoc($resultadoVentas)) {
            $ventas[] = $fila;
        }

        mysqli_stmt_close($stmtVentas);
    } else {
        $mensajeError = 'No se pudo preparar la consulta de ventas del día.';
    }
}

$sqlTiposPago = "SELECT cod_tipopago, nombre_tipopago
                 FROM tipos_pago
                 WHERE cod_tipopago IN (1, 2, 3)
                 ORDER BY cod_tipopago";
$resultadoTiposPago = mysqli_query($enlaceCon, $sqlTiposPago);

if ($resultadoTiposPago) {
    while ($fila = mysqli_fetch_assoc($resultadoTiposPago)) {
        $tiposPago[] = $fila;
    }
}

if (ob_get_length()) {
    ob_clean();
}

function escapar($valor)
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formatoMonto($monto)
{
    return number_format((float) $monto, 2, ',', '.');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas de hoy</title>
    <style>
        :root {
            --vd-primary: #0f766e;
            --vd-primary-dark: #115e59;
            --vd-primary-soft: #ccfbf1;
            --vd-bg: #f4f7f9;
            --vd-card: #ffffff;
            --vd-text: #172033;
            --vd-muted: #667085;
            --vd-border: #e4e7ec;
            --vd-danger: #b42318;
            --vd-shadow: 0 12px 30px rgba(15, 23, 42, .08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--vd-bg);
            color: var(--vd-text);
            font-family: Arial, Helvetica, sans-serif;
        }
        .vd-page { width: min(1400px, calc(100% - 32px)); margin: 28px auto; }
        .vd-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 18px; margin-bottom: 18px;
        }
        .vd-title { margin: 0 0 5px; font-size: 25px; }
        .vd-subtitle { margin: 0; color: var(--vd-muted); font-size: 14px; }
        .vd-summary {
            display: flex; align-items: center; gap: 10px; padding: 11px 15px;
            border: 1px solid #99f6e4; border-radius: 12px;
            background: var(--vd-primary-soft); color: var(--vd-primary-dark);
            font-size: 14px; font-weight: 700; white-space: nowrap;
        }
        .vd-card {
            overflow: hidden; border: 1px solid var(--vd-border);
            border-radius: 16px; background: var(--vd-card); box-shadow: var(--vd-shadow);
        }
        .vd-table-wrap { overflow-x: auto; }
        .vd-table { width: 100%; border-collapse: collapse; min-width: 960px; }
        .vd-table th {
            padding: 13px 15px; background: #f8fafc; border-bottom: 1px solid var(--vd-border);
            color: #475467; font-size: 12px; text-align: left; text-transform: uppercase;
            letter-spacing: .04em;
        }
        .vd-table td { padding: 14px 15px; border-bottom: 1px solid #eef1f4; font-size: 14px; }
        .vd-table tr:last-child td { border-bottom: 0; }
        .vd-table tr:hover td { background: #fbfefd; }
        .vd-number { font-weight: 800; color: var(--vd-primary-dark); }
        .vd-muted { display: block; margin-top: 3px; color: var(--vd-muted); font-size: 12px; }
        .vd-amount { font-weight: 800; white-space: nowrap; }
        .vd-payment {
            display: inline-flex; align-items: center; padding: 6px 9px;
            border-radius: 999px; background: #f2f4f7; font-size: 12px; font-weight: 700;
        }
        .vd-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            min-height: 38px; padding: 9px 13px; border: 0; border-radius: 9px;
            background: var(--vd-primary); color: #fff; cursor: pointer;
            font-weight: 700; transition: .18s ease;
        }
        .vd-btn:hover { background: var(--vd-primary-dark); transform: translateY(-1px); }
        .vd-btn:disabled { opacity: .6; cursor: wait; transform: none; }
        .vd-btn-secondary { background: #eef2f6; color: #344054; }
        .vd-btn-secondary:hover { background: #e4e7ec; }
        .vd-icon { width: 18px; height: 18px; fill: currentColor; flex: 0 0 auto; }
        .vd-empty, .vd-error { padding: 42px 20px; text-align: center; color: var(--vd-muted); }
        .vd-error { color: var(--vd-danger); }
        .vd-modal {
            position: fixed; inset: 0; z-index: 9999; display: none;
            align-items: center; justify-content: center; padding: 18px;
            background: rgba(15, 23, 42, .58);
        }
        .vd-modal.is-open { display: flex; }
        .vd-dialog {
            width: min(480px, 100%); overflow: hidden; border-radius: 16px;
            background: #fff; box-shadow: 0 28px 70px rgba(0, 0, 0, .25);
        }
        .vd-dialog-head { display: flex; gap: 12px; padding: 21px 22px 13px; }
        .vd-dialog-icon {
            display: grid; place-items: center; width: 42px; height: 42px;
            border-radius: 11px; background: var(--vd-primary-soft); color: var(--vd-primary-dark);
        }
        .vd-dialog-title { margin: 0; font-size: 19px; }
        .vd-dialog-text { margin: 5px 0 0; color: var(--vd-muted); font-size: 13px; }
        .vd-dialog-body { padding: 8px 22px 22px; }
        .vd-label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 800; }
        .vd-select {
            width: 100%; height: 46px; padding: 0 42px 0 13px;
            border: 1px solid #cfd6df; border-radius: 10px; background: #fff;
            color: var(--vd-text); font-size: 14px; outline: 0;
        }
        .vd-select:focus { border-color: var(--vd-primary); box-shadow: 0 0 0 3px rgba(15, 118, 110, .12); }
        .vd-dialog-actions {
            display: flex; justify-content: flex-end; gap: 9px; padding: 15px 22px;
            border-top: 1px solid var(--vd-border); background: #f8fafc;
        }
        .vd-toast {
            position: fixed; right: 22px; bottom: 22px; z-index: 10000;
            max-width: 390px; padding: 13px 16px; border-radius: 11px;
            background: #101828; color: #fff; box-shadow: var(--vd-shadow);
            opacity: 0; transform: translateY(12px); pointer-events: none; transition: .2s ease;
        }
        .vd-toast.is-visible { opacity: 1; transform: translateY(0); }
        .vd-toast.is-error { background: var(--vd-danger); }
        @media (max-width: 720px) {
            .vd-page { width: min(100% - 20px, 1400px); margin-top: 18px; }
            .vd-header { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<main class="vd-page">
    <header class="vd-header">
        <div>
            <h1 class="vd-title">Ventas de hoy</h1>
            <p class="vd-subtitle"><?php echo escapar(date('d/m/Y')); ?> · Solo se permite actualizar el tipo de pago.</p>
        </div>
        <div class="vd-summary">
            <svg class="vd-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10v2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3V2Zm2 2h6V3H9v1Zm-5 5h16V6H4v3Zm0 2v9h16v-9H4Z"/></svg>
            <?php echo count($ventas); ?> venta(s)
        </div>
    </header>

    <section class="vd-card">
        <?php if ($mensajeError !== '') { ?>
            <div class="vd-error"><?php echo escapar($mensajeError); ?></div>
        <?php } elseif (count($ventas) === 0) { ?>
            <div class="vd-empty">Todavía no existen ventas registradas en el día de hoy.</div>
        <?php } else { ?>
            <div class="vd-table-wrap">
                <table class="vd-table">
                    <thead>
                        <tr>
                            <th>N.º venta</th>
                            <th>Hora</th>
                            <th>Cliente / razón social</th>
                            <th>NIT / CI</th>
                            <th>Vendedor</th>
                            <th>Tipo de pago</th>
                            <th>Monto</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ventas as $venta) { ?>
                        <tr>
                            <td><span class="vd-number">#<?php echo escapar($venta['nro_correlativo']); ?></span></td>
                            <td><?php echo escapar(substr($venta['hora_salida'], 0, 5)); ?></td>
                            <td>
                                <?php echo escapar($venta['razon_social'] !== '' ? $venta['razon_social'] : 'Sin razón social'); ?>
                                <?php if ((int) $venta['salida_anulada'] === 1) { ?><span class="vd-muted">Venta anulada</span><?php } ?>
                            </td>
                            <td><?php echo escapar($venta['nit']); ?></td>
                            <td><?php echo escapar(trim($venta['vendedor'])); ?></td>
                            <td><span class="vd-payment" id="pago-<?php echo (int) $venta['cod_salida_almacenes']; ?>"><?php echo escapar($venta['nombre_tipopago']); ?></span></td>
                            <td><span class="vd-amount">Bs <?php echo formatoMonto($venta['monto_final']); ?></span></td>
                            <td>
                                <?php if ((int) $venta['salida_anulada'] === 0) { ?>
                                    <button type="button" class="vd-btn" onclick="abrirModalPago(<?php echo (int) $venta['cod_salida_almacenes']; ?>, <?php echo (int) $venta['cod_tipopago']; ?>, '<?php echo escapar($venta['nro_correlativo']); ?>')">
                                        <svg class="vd-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 4H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4H3V6h18v2ZM3 18v-6h18v6H3Zm2-2h6v-2H5v2Z"/></svg>
                                        Cambiar pago
                                    </button>
                                <?php } else { ?>
                                    <span class="vd-muted">No disponible</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </section>
</main>

<div class="vd-modal" id="modalPago" role="dialog" aria-modal="true" aria-labelledby="tituloModalPago">
    <div class="vd-dialog">
        <div class="vd-dialog-head">
            <div class="vd-dialog-icon">
                <svg class="vd-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 4H3a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4H3V6h18v2H3Z"/></svg>
            </div>
            <div>
                <h2 class="vd-dialog-title" id="tituloModalPago">Actualizar tipo de pago</h2>
                <p class="vd-dialog-text" id="textoVenta">Seleccione la nueva forma de pago.</p>
            </div>
        </div>
        <div class="vd-dialog-body">
            <input type="hidden" id="codigoSalida" value="0">
            <label class="vd-label" for="codigoTipoPago">Nuevo tipo de pago</label>
            <select class="vd-select" id="codigoTipoPago">
                <?php foreach ($tiposPago as $tipoPago) { ?>
                    <option value="<?php echo (int) $tipoPago['cod_tipopago']; ?>"><?php echo escapar($tipoPago['nombre_tipopago']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="vd-dialog-actions">
            <button type="button" class="vd-btn vd-btn-secondary" onclick="cerrarModalPago()">Cancelar</button>
            <button type="button" class="vd-btn" id="btnGuardarPago" onclick="guardarTipoPago()">
                <svg class="vd-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4Zm-5 16a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm3-10H5V5h10v4Z"/></svg>
                Guardar cambio
            </button>
        </div>
    </div>
</div>

<div class="vd-toast" id="toast"></div>

<script>
    var modalPago = document.getElementById('modalPago');
    var botonGuardar = document.getElementById('btnGuardarPago');

    function abrirModalPago(codigoSalida, codigoTipoPago, correlativo) {
        document.getElementById('codigoSalida').value = codigoSalida;
        document.getElementById('codigoTipoPago').value = codigoTipoPago;
        document.getElementById('textoVenta').textContent = 'Venta #' + correlativo + ' · seleccione el nuevo tipo de pago.';
        modalPago.classList.add('is-open');
    }

    function cerrarModalPago() {
        modalPago.classList.remove('is-open');
    }

    function mostrarMensaje(mensaje, esError) {
        var toast = document.getElementById('toast');
        toast.textContent = mensaje;
        toast.className = 'vd-toast is-visible' + (esError ? ' is-error' : '');
        window.setTimeout(function () { toast.className = 'vd-toast'; }, 4500);
    }

    function guardarTipoPago() {
        var codigoSalida = parseInt(document.getElementById('codigoSalida').value, 10);
        var selectPago = document.getElementById('codigoTipoPago');
        var codigoTipoPago = parseInt(selectPago.value, 10);

        if (!codigoSalida || !codigoTipoPago) {
            mostrarMensaje('Seleccione un tipo de pago válido.', true);
            return;
        }

        if (!window.confirm('¿Confirma el cambio de tipo de pago de esta venta?')) {
            return;
        }

        botonGuardar.disabled = true;
        botonGuardar.textContent = 'Actualizando...';

        fetch('ajaxActualizarTipoPagoVentaGeneral.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: 'codigo_salida=' + encodeURIComponent(codigoSalida)
                + '&codigo_tipo_pago=' + encodeURIComponent(codigoTipoPago)
        })
        .then(function (respuesta) {
            return respuesta.json().then(function (datos) {
                if (!respuesta.ok || !datos.ok) {
                    throw new Error(datos.mensaje || 'No se pudo actualizar el tipo de pago.');
                }
                return datos;
            });
        })
        .then(function (datos) {
            document.getElementById('pago-' + codigoSalida).textContent = selectPago.options[selectPago.selectedIndex].text;
            cerrarModalPago();
            mostrarMensaje(datos.mensaje, false);
        })
        .catch(function (error) {
            mostrarMensaje(error.message, true);
        })
        .then(function () {
            botonGuardar.disabled = false;
            botonGuardar.innerHTML = '<svg class="vd-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-4-4Zm-5 16a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm3-10H5V5h10v4Z"/></svg> Guardar cambio';
        });
    }

    modalPago.addEventListener('click', function (evento) {
        if (evento.target === modalPago) cerrarModalPago();
    });

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape') cerrarModalPago();
    });
</script>
</body>
</html>
