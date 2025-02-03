<?php
	require("conexionmysqli.inc");
	require("estilos_almacenes.inc");
	require("funciones.php");
	require("funcion_nombres.php");
?>
<!--link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
<script src="assets/js/core/jquery.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script-->

<script>

    $(document).ready(function() {
        $('#myTable').DataTable({
            "paging":   false,
            "info":     false,
            "order": [[4, 'asc']],
            fixedHeader: {
              header: true,
              footer: true
            }
        } );
    } );
	
</script>
<style>
	.scroll-container {
		width: 100%;
		overflow-x: scroll; /* Siempre muestra el scroll horizontal */
		max-height: 85vh;
		position: relative;
	}
	table {
		width: 80%;
		border-collapse: collapse;
	}
	th, td {
		padding: 8px 12px;
		border: 1px solid #ddd;
		text-align: left;
	}
	th {
		background-color: #f2f2f2;
		position: sticky;
		top: 0;
		z-index: 2;
		box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
	}
	/* TEXTO PRODUCTO */
	.textomedianorojo2 {
		font-family: Verdana;
		font-size: 14pt;
		color: #e20000;
	}	
</style>



<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	$fechaActual=date("Y-m-d");

	$globalSucursal=$_COOKIE['global_agencia'];
	$globalAlmacen=$_COOKIE['global_almacen'];
	$nombreAlmacen=nombreAlmacen($enlaceCon, $globalAlmacen);
	

	$numeroMesesControlVencimiento = obtenerValorConfiguracion($enlaceCon, 28);

	$nroMeses=3;
	$fechaIni=date("Y-m-d");
	$fechaFin=date('Y-m-d',strtotime($fechaIni.'+'.$nroMeses.' month'));
		
	echo "<h1>Reporte de Productos Proximos a Vencer<br>Almacen: $nombreAlmacen</h1>";
	
	$sql="select m.descripcion_material, DATE_FORMAT(id.fecha_vencimiento, '%d/%m/%Y'), id.cantidad_restante, id.fecha_vencimiento, 
	(select pl.nombre_linea_proveedor from proveedores_lineas pl where pl.cod_linea_proveedor=m.cod_linea_proveedor) as linea, 
	(select p.nombre_proveedor from proveedores_lineas pl, proveedores p where p.cod_proveedor=pl.cod_proveedor and pl.cod_linea_proveedor=m.cod_linea_proveedor) as proveedor,
	m.codigo_material
	from material_apoyo m, ingreso_detalle_almacenes id, ingreso_almacenes i
		where i.cod_ingreso_almacen=id.cod_ingreso_almacen and id.cod_material=m.codigo_material and 
		i.ingreso_anulado=0 and id.fecha_vencimiento<='$fechaFin' and id.cantidad_restante>0 and i.cod_almacen='$globalAlmacen'
		order by linea, m.descripcion_material";
		
	$resp=mysqli_query($enlaceCon, $sql);
	
	echo "<center class='scroll-container'>
	<table class='texto' id='myTable'>";
	echo "<thead>
	<tr>
	<th>#</th>
	<th>Distribuidor</th>
	<th>Linea</th>
	<th>Material</th>
	<th>Fecha</th>
	<th>Cantidad</th>
	</tr>
	</thead>";

	echo "<tbody>";
	$indice=1;
	while($dat=mysqli_fetch_array($resp))
	{
		$nombreMaterial=$dat[0];
		$fechaVencimiento=$dat[1];
		$cantidadUnitaria=$dat[2];
		$fechaVencimientoSF=$dat[3];
		$lineaProveedor=$dat[4];
		$distribuidor=$dat[5];
		$codigoMaterial=$dat[6];
		
		$fechaVencimiento=obtenerFechaVencimiento2($enlaceCon, $globalAlmacen, $codigoMaterial);
		//echo $fechaVencimientoSF." ".$fechaActual;

		if($fechaVencimiento!=""){
			//list($mes, $anio) = explode("/", $fechaVencimiento);
			list($anio, $mes, $dia) = explode("-", $fechaVencimiento);
			$hoy = date('m/Y');
			list($mesHoy, $anioHoy) = explode("/", $hoy);
			$mesesDiferencia = (($anio - $anioHoy) * 12) + ($mes - $mesHoy);

			$controlVencimientoArray 	   = json_decode($numeroMesesControlVencimiento, true);
			usort($controlVencimientoArray, function($a, $b) {
				return $a['meses'] <=> $b['meses'];
			});
			$colorFV = '';
			foreach ($controlVencimientoArray as $item) {
				if ($mesesDiferencia <= $item['meses']) {
					$colorFV = $item['color'];
					break;
				} else {
					$colorFV = 'white';
				}
			}				
		}


		/*if($fechaVencimientoSF!="0000-00-00" && $fechaVencimientoSF!="1969-12-30"){
			if($fechaVencimientoSF<=$fechaActual){
				echo "<td align='center'><div class='textogranderojo'>$indice</div></td>";
				echo "<td align='left'><div class='textogranderojo'>$distribuidor</div></td>";
				echo "<td align='left'><div class='textogranderojo'>$lineaProveedor</div></td>";
				echo "<td align='left'><div class='textogranderojo'>$nombreMaterial</div></td>";
				echo "<td align='center'><div class='textogranderojo'>$fechaVencimiento</div></td>";
				echo "<td align='center'><div class='textogranderojo'>$cantidadUnitaria</div></td>";
			}else{
				echo "<td align='center'>$indice</td>";
				echo "<td align='left'>$distribuidor</td>";
				echo "<td align='left'>$lineaProveedor</td>";
				echo "<td align='left'>$nombreMaterial</td>";
				echo "<td align='center'>$fechaVencimiento</td>";
				echo "<td align='center'>$cantidadUnitaria</td>";
			}			
		}*/

		echo "<tr style='background-color: $colorFV; text-align: center;'><td align='center'>$indice</td>";
		echo "<td align='left'>$distribuidor</td>";
		echo "<td align='left'>$lineaProveedor</td>";
		echo "<td align='left'><b>$nombreMaterial<b></td>";
		echo "<td align='center'><b>$fechaVencimiento</b></td>";
		echo "<td align='center'>$cantidadUnitaria</td>";

		
		echo "</tr>";
		
		$indice++;
	}
	echo "</tbody></table></center>";	
?>
