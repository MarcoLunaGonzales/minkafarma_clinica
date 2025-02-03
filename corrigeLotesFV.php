<?php
set_time_limit(0);
require('conexionmysqli2.inc');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

//igualamos las cantidades de los ingresos detalle
$sqlDet="SELECT sd.cod_salida_almacen, sd.cod_material, sd.cantidad_unitaria, sd.lote, sd.fecha_vencimiento, 	sd.cod_ingreso_almacen from salida_almacenes s, salida_detalle_almacenes sd
	where s.cod_salida_almacenes=sd.cod_salida_almacen and s.salida_anulada=0 and 
	s.cod_tiposalida=1001 and s.fecha>='2024-07-01' and sd.lote=0 ORDER BY sd.cod_material";
$respDet=mysqli_query($enlaceCon, $sqlDet);
while($datDet=mysqli_fetch_array($respDet))
{	$codSalida=$datDet[0];
	$codMaterial=$datDet[1];
	$cantUnit=$datDet[2];
	$lote=$datDet[3];
	$FV=$datDet[4];

	echo $codSalida." ".$codMaterial." ".$cantUnit." ".$lote." ".$FV."<br>";

	$sqlLotes="SELECT id.lote, id.fecha_vencimiento from ingreso_almacenes i, ingreso_detalle_almacenes id where 
		i.cod_ingreso_almacen=id.cod_ingreso_almacen and i.ingreso_anulado=0 and 
		id.cod_material=1 order by i.cod_ingreso_almacen desc";
	$respLotes=mysqli_query($enlaceCon, $sqlLotes);
	while($datLotes=mysqli_fetch_array($respLotes)){
		$loteCorregir=$datLotes[0];
		$FVCorregir=$datLotes[1];
	}

	$sqlUPD="UPDATE salida_detalle_almacenes set lote='$loteCorregir', fecha_vencimiento='$FVCorregir' where cod_salida_almacen='$codSalida' and cod_material='$codMaterial'";
	$sqlUPD=mysqli_query($enlaceCon, $sqlUPD);
}

?>