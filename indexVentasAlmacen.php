<html>
<head>
<meta charset="utf-8" />
<title>MinkaSoftware</title> 
    <link rel="shortcut icon" href="imagenes/icon_farma.ico" type="image/x-icon">
<link type="text/css" rel="stylesheet" href="menuLibs/css/demo.css" />
<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
<style>  
	.boton-rojo{
	    text-decoration: none !important;
	    font-weight: 0 !important;
	    font-size: 12px !important;
	    color: #ffffff !important;
	    background-color: #E73024 !important;
	    border-radius: 3px !important;
	    border: 2px solid #E73024 !important;
	}	
	.boton-rojo:hover{
    	color: #000000 !important;
    	background-color: #ffffff !important;
  	}
   .boton-plomo{
	    text-decoration: none !important;
	    font-weight: 0 !important;
	    font-size: 12px !important;
	    color: white !important;
	    background-color: red !important;
	    border-radius: 3px !important;
	    border: 2px solid #88898A !important;
	}
	.boton-plomo:hover{
    	color: #000000 !important;
    	background-color: #ffffff !important;
  	}
</style>
     <link rel="stylesheet" href="dist/css/demo.css" />
     <link rel="stylesheet" href="dist/mmenu.css" />
	 <link rel="stylesheet" href="dist/demo.css" />
</head>
<body>
<?php
//require_once 'conexionmysqlipdf.inc';
require_once 'datosUsuario.php';
//require_once 'funciones.php'; 
?>

<div id="page">
	<div class="header">
		<a href="#menu"><span></span></a>
		TuFarma - <?=$nombreEmpresa;?>
		<div style="position:absolute; width:95%; height:50px; text-align:right; top:0px; font-size: 15px; font-weight: bold; color: #ffff00;">
			<button onclick="window.contenedorPrincipal.location.href='manuales_sistema/navegadorManuales.php'" style="position:relative;z-index:99999;right:0px;" class="boton-plomo" title="Manuales TuFarma" formtarget="contenedorPrincipal">
				<i class="material-icons" style="font-size: 20px">chrome_reader_mode</i>
			</button>
			[<?=$fechaSistemaSesion;?> <?=$horaSistemaSesion;?>]
			<button onclick="location.href='salir.php'" style="position:relative;z-index:99999;right:0px;" class="boton-rojo" title="Salir">
				<i class="material-icons" style="font-size: 16px">logout</i>
			</button>			
		<div>
		<div style="position:absolute; width:95%; height:50px; text-align:left; top:0px; font-size: 15px; font-weight: bold; color: #ffff00;">
			[<?=$nombreUsuarioSesion;?>]    [<?=$nombreAlmacenSesion;?>]
			<button onclick="window.contenedorPrincipal.location.href='cambiarSucursalSesion.php'" style="position:relative;z-index:99999;right:0px;" class="boton-rojo" title="Cambiar Sucursal" formtarget="contenedorPrincipal">
				<i class="material-icons" style="font-size: 16px">swap_horiz</i>
			</button>
			<button onclick="window.contenedorPrincipal.location.href='editPerfil.php'" style="position:relative;z-index:99999;right:0px;" class="boton-rojo" title="Cambiar Clave de Acceso" formtarget="contenedorPrincipal">
				<i class="material-icons" style="font-size: 16px">person</i>
			</button>	
		<div>
	</div>
	
	
	<div class="content">
		<iframe src="inicio_almacenes.php" name="contenedorPrincipal" id="mainFrame" border="1"></iframe>
	</div>
	
	
	<nav id="menu">

		<div id="panel-menu">

		<ul>
			<li><span>Salidas</span>
				<ul>
					<li><a href="navegador_salidamateriales.php" target="contenedorPrincipal">Listado de Salidas</a></li>
					<li><a href="<?=$urlNavVentas;?>" target='_blank'>Listado de Ventas</a></li>
				</ul>	
			</li>
			<li><a href="navegadorPedidos.php" target='_blank'>Pedidos **</a></li>
			<li><a href="registrar_salidaventas.php" target="_blank">Vender / Facturar **</a></li>
			<li><span>Cobranzas</span>
					<ul>
						<li><a href="cobranzas/navegadorCobranzas.php" target="contenedorPrincipal">Listado de Cobranzas</a></li>
						<li><a href="cobranzas/rptOpCobranzas.php" target="contenedorPrincipal">Reporte de Cobros</a></li>
						<li><a href="cobranzas/rptOpCuentasCobrar.php" target="contenedorPrincipal">Reporte Cuentas x Cobrar</a></li>
					</ul>	
			</li>
			<li><a href="control_inventario/list.php" target="contenedorPrincipal">Control de Inventario</a></li>
			<li><span>Reportes</span>
				<ul>
					<li><span>Movimiento de Almacen</span>
						<ul>
							<li><a href="rpt_op_inv_kardex.php" target="contenedorPrincipal">Kardex de Movimiento</a></li>
							<li><a href="rpt_op_inv_existencias.php" target="contenedorPrincipal">Existencias</a></li>
							<li><a href="rptOpProductosAReponer.php" target="contenedorPrincipal">Productos a reponer</a></li>
							<li><a href="rptProductosVencer.php" target="contenedorPrincipal">Productos proximos a Vencer</a></li>
							<li><a href="rptOpVencimientoMinimo.php" target="contenedorPrincipal">Vencimientos y Mínimos</a></li>
						</ul>
					</li>	
					<li><span>Ventas</span>
						<ul>
							<li><a href="rptOpVentasPorClientes.php" target="contenedorPrincipal">Ventas x Cliente</a></li>
							<li><a href="rptOpVentasDocumento.php" target="contenedorPrincipal">Ventas x Documento</a></li>
							<li><a href="rptOpVentasxItem.php" target="contenedorPrincipal">Ventas x Item</a></li>
						</ul>	
					</li>
				</ul>
			</li>	
		</ul>
		
		</div>		
	</nav>
</div>

<script src="dist/mmenu.polyfills.js"></script>
<script src="dist/mmenu.js"></script>
<script src="dist/demo.js"></script>

	</body>
</html>