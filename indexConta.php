<html>
<head>
<meta charset="utf-8" />
<title>MinkaSoftware</title> 
    <link rel="shortcut icon" href="imagenes/icon_farma.ico" type="image/x-icon">
<link type="text/css" rel="stylesheet" href="menuLibs/css/demo.css" />
<script type="text/javascript" src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
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
	    font-size: 12spx !important;
	    color: #ffffFF !important;
	    background-color: #88898A !important;
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
include("datosUsuario.php");
?>
<div id="page">
	<div class="header">
		<a href="#menu"><span></span></a>
		TuFarma - <?=$nombreEmpresa;?>
		<div style="position:absolute; width:95%; height:50px; text-align:right; top:0px; font-size: 15px; font-weight: bold; color: #ffff00;">
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
			<li><span>Datos Generales</span>
				<ul>
					<li><a href="programas/proveedores/inicioProveedores.php" target="contenedorPrincipal">Proveedores & Distribuidores</a></li>
					<li><a href="navegador_material.php" target="contenedorPrincipal">Productos</a></li>
					<li><a href="navegador_ajustarpreciostock.php" target="contenedorPrincipal">Ajustar Precio/Stock **</a></li>
				</ul>	
			</li>

			<li><span>Ingresos</span>
				<ul>
					<li><a href="navegador_ingresomateriales.php" target="contenedorPrincipal">Ingreso de Materiales</a></li>
					<!--li><a href="navegadorLiquidacionIngresos.php" target="contenedorPrincipal">Liquidacion de Ingresos</a></li-->
					<li><a href="navegador_ingresotransito.php" target="contenedorPrincipal">Ingreso de Productos en Transito</a></li>
				</ul>	
			</li>
			<li><span>Salidas</span>
				<ul>
					<li><a href="navegador_salidamateriales.php" target="contenedorPrincipal">Listado de Traspasos</a></li>
					<li><a href="navegadorVentas.php" target="contenedorPrincipal">Listado de Ventas</a></li>
					<li><a href="control_inventario/list.php" target="contenedorPrincipal">Control de Inventario</a></li>
					<!--li><a href="registrar_salidaventas_manuales.php" target="_blank">Factura Manual de Contigencia</a></li-->
				</ul>	
			</li>
			<li><span>Marcados de Personal</span>
				<ul>
					<li><a href="registrar_marcado.php" target="contenedorPrincipal">Registro de Marcados</a></li>
					<li><a href="rptOpMarcados.php" target="contenedorPrincipal">Reporte de Marcados</a></li>
				</ul>	
			</li>
			<li><span>SIAT</span>
				<ul>
					<li><a href="siat_folder/siat_facturacion_offline/facturas_sincafc_list.php" target="contenedorPrincipal">Facturas Off-line</a></li>
					<li><a href="siat_folder/siat_facturacion_offline/facturas_cafc_list.php" target="contenedorPrincipal">Facturas Off-line CAFC</a></li>
					<li><a href="siat_folder/siat_sincronizacion/index.php" target="contenedorPrincipal">Sincronización</a></li>
					<li><a href="siat_folder/siat_puntos_venta/index.php" target="contenedorPrincipal">Puntos Venta</a></li>
					<li><a href="siat_folder/siat_cuis_cufd/index.php" target="contenedorPrincipal">Generación CUIS y CUFD</a></li>
					
				</ul>	
			</li>	
			<li><a href="registrar_salidaventas.php" target="_blank">Vender / Facturar **</a></li>
			<li><a href="rptOpArqueoDiario.php?variableAdmin=1" target="contenedorPrincipal">Cierre de Caja</a></li>
			<li><a href="cambiarSucursalSesion.php" target="contenedorPrincipal">Cambiar Almacen</a></li>


			<!-- <li><a href="rptOpProcesarCostos.php" target="contenedorPrincipal">Procesar Costos</a></li> -->
			<li><span>Costos</span>
				<ul>
					<li><a href="rptOpProcesarCostos.php" target="contenedorPrincipal">Procesar Costos</a></li>
					<li><a href="rpt_op_inv_ingresos_costo.php" target="contenedorPrincipal">Ingresos con Costo 0</a></li>
					<li><a href="rpt_op_inv_salidas_costo.php" target="contenedorPrincipal">Salidas con Costo 0</a></li>
				</ul>	
			</li>	

			<li><span>Reportes</span>
				<ul>
					<li><span>Movimiento de Almacen</span>
						<ul>
							<li><a href="rpt_op_inv_kardex.php" target="contenedorPrincipal">Kardex de Movimiento</a></li>
							<li><a href="rpt_op_inv_existencias.php" target="contenedorPrincipal">Existencias</a></li>
							<li><a href="rpt_op_inv_ingresos.php" target="contenedorPrincipal">Ingresos</a></li>
							<li><a href="rpt_op_inv_salidas.php" target="contenedorPrincipal">Salidas</a></li>
							<li><a href="rptPrecios.php" target="contenedorPrincipal">Precios</a></li>
							<li><a href="rptProductosVencer.php" target="contenedorPrincipal">Productos proximos a Vencer</a></li>
							<li><a href="rptOpProductosAReponer.php" target="contenedorPrincipal">Productos a reponer</a></li>							<li><a href="rptOpVencimientoMinimo.php" target="contenedorPrincipal">Reporte de Vencimientos y Mínimos</a></li>							<!--li><a href="rptOCPagar.php" target="contenedorPrincipal">OC por Pagar</a></li-->
						</ul>
					</li>	
					<li><span>Ventas</span>
						<ul>
							<li><a href="rptOpVentasDocumento.php" target="contenedorPrincipal">Ventas x Documento</a></li>
							<li><a href="rptOpVentasxItem.php" target="contenedorPrincipal">Ventas x Item</a></li>
							<li><a href="rptOpVentasGeneral.php" target="contenedorPrincipal">Ventas x Documento e Item</a></li>
							<li><a href="rptOpVentasxPersonaDetalle.php" target="contenedorPrincipal">Ventas x Vendedor</a></li>
							<li><a href="rptOpVentasSucursal.php" target="contenedorPrincipal">Ventas x Sucursal</a></li>
							<!--li><a href="rptOpKardexCliente.php" target="contenedorPrincipal">Kardex x Cliente</a></li-->
						</ul>	
					</li>
					<li><span>Costos</span>
						<ul>
							<li><a href="rptOpKardexCostos.php" target="contenedorPrincipal">Kardex Valorado</a></li>
							<li><a href="rptOpExistenciasCostos.php" target="contenedorPrincipal">Existencias</a></li>	
							<li><a href="rptOpUtilidadesDocItem.php" target="contenedorPrincipal">Costo Ventas x Documento e Item</a></li>							
						</ul>
					</li>
					<li><span>Reportes Contables</span>
						<ul>
							<li><a href="rptOpLibroVentas.php" target="contenedorPrincipal">Libro de Ventas</a></li>
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