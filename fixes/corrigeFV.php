<?php
set_time_limit(0);
require('../conexionmysqli2.inc');

 error_reporting(E_ALL);
 ini_set('display_errors', '1');

//igualamos las cantidades de los ingresos detalle
$sqlDet="SELECT i.cod_ingreso_almacen, i.cod_material, i.lote, i.fecha_vencimiento, ii.fecha, m.descripcion_material
	from ingreso_detalle_almacenes i 
	INNER JOIN ingreso_almacenes ii ON ii.cod_ingreso_almacen=i.cod_ingreso_almacen
	INNER JOIN material_apoyo m ON m.codigo_material=i.cod_material
	where i.fecha_vencimiento in ('1969-12-30','0000-00-00') and ii.ingreso_anulado=0 ";
$respDet=mysqli_query($enlaceCon, $sqlDet);
while($datDet=mysqli_fetch_array($respDet))
{	$codIngreso=$datDet[0];
	$codProducto=$datDet[1];
	$codLote=$datDet[2];
	$FV=$datDet[3];

	$fechaIngreso=$datDet[4];

	$nombreProducto=$datDet[5];

	echo $codIngreso."\t\t\t\t".$codProducto."\t\t\t".$codLote."\t\t\t".$FV."  ".$nombreProducto."<br>";

	$sqlLotes="SELECT id.lote, id.fecha_vencimiento from ingreso_almacenes i, ingreso_detalle_almacenes id where 
		i.cod_ingreso_almacen=id.cod_ingreso_almacen and i.ingreso_anulado=0 and 
		id.cod_material='$codProducto' and i.fecha>='$fechaIngreso' order by i.cod_ingreso_almacen desc";
	//echo $sqlLotes."<br>";
	$respLotes=mysqli_query($enlaceCon, $sqlLotes);
	$bandera=0;
	while( ($datLotes=mysqli_fetch_array($respLotes)) && $bandera==0 ){
		$loteCorregir=$datLotes[0];
		$FVCorregir=$datLotes[1];

		if( ($FVCorregir!='1969-12-30') && ($FVCorregir!='0000-00-00') ){
			echo $FVCorregir."<br>";
			$bandera=1;
		}
	}

	$sqlUPD="UPDATE ingreso_detalle_almacenes set fecha_vencimiento='$FVCorregir' where cod_ingreso_almacen='$codIngreso' and cod_material='$codProducto'";
	$sqlUPD=mysqli_query($enlaceCon, $sqlUPD);

	echo "<br><br>";
}


?>