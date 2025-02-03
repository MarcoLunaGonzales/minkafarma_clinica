<?php
set_time_limit(0);
require('conexionmysqli2.inc');

$cod_almacen_arreglar=1001;

$sqlMat="SELECT m.codigo_material,m.descripcion_material FROM material_apoyo m WHERE m.codigo_material IN (23,237,416,368,9,417,161,48,243,245,418,208,379,209,211,220,249,7,313,314,250,103,80,60,386,64,387,35,165,381,255,256,83,258,221,262,14,372,132,114,169,266,267,374,340,69,225,6,217,383,149,229,424,279,78,363,175,287,288,290,295,183,202,204,285,303,206,87,307,306,22,377,415) ORDER BY 1";
$respMat=mysqli_query($enlaceCon, $sqlMat);

while($datMat=mysqli_fetch_array($respMat)){
	

	$codMaterialArreglar=$datMat[0];
	
	echo $codMaterialArreglar."<br>";

	//igualamos las cantidades de los ingresos detalle
	$sql_det_ingreso="select id.cod_ingreso_almacen, id.cod_material, id.cantidad_unitaria from ingreso_detalle_almacenes id, 
					ingreso_almacenes i where i.cod_ingreso_almacen=id.cod_ingreso_almacen and 
					i.cod_almacen='$cod_almacen_arreglar' and id.cod_material='$codMaterialArreglar' order by cod_ingreso_almacen";
	
	//echo $sql_det_ingreso."<br>";
	
	$resp_det_ingreso=mysqli_query($enlaceCon, $sql_det_ingreso);
	while($dat_det_ingreso=mysqli_fetch_array($resp_det_ingreso))
	{	$cod_ingreso=$dat_det_ingreso[0];
		$cod_material=$dat_det_ingreso[1];
		$cant_unit=$dat_det_ingreso[2];
		$sql_actualiza_cant=mysqli_query($enlaceCon, "update ingreso_detalle_almacenes set cantidad_restante='$cant_unit'
		where cod_ingreso_almacen='$cod_ingreso' and cod_material='$cod_material'");
	}
	//borramos la tabla salida_detalle_ingreso
	$sql_salida_detalle="select sd.cod_salida_almacen, sd.cod_material, sd.cantidad_unitaria
	from salida_detalle_almacenes sd, salida_almacenes s
						where s.cod_salida_almacenes=sd.cod_salida_almacen and s.cod_almacen='$cod_almacen_arreglar' 
						and sd.cod_material='$codMaterialArreglar'";
	
	//echo $sql_salida_detalle."<br>";

	$resp_salida_detalle=mysqli_query($enlaceCon, $sql_salida_detalle);
	while($dat_salida_detalle=mysqli_fetch_array($resp_salida_detalle))
	{	$cod_salida_almacen=$dat_salida_detalle[0];
		$sql_del_salidadetalleingreso="delete from salida_detalle_ingreso where cod_salida_almacen='$cod_salida_almacen' and material='$codMaterialArreglar'";
		$resp_del_salidadetalleingreso=mysqli_query($enlaceCon, $sql_del_salidadetalleingreso);
	}
	//sacamos las salidas para generar la tabla salida_detalle_ingreso
	$sql_salida_detalle="select sd.cod_salida_almacen, sd.cod_material, sd.cantidad_unitaria
	from salida_detalle_almacenes sd, salida_almacenes s
						where s.cod_salida_almacenes=sd.cod_salida_almacen and s.cod_almacen='$cod_almacen_arreglar' 
						and s.salida_anulada=0 and sd.cod_material='$codMaterialArreglar'";
	
	//echo $sql_salida_detalle."<br>";

	$resp_salida_detalle=mysqli_query($enlaceCon, $sql_salida_detalle);
	while($dat_salida_detalle=mysqli_fetch_array($resp_salida_detalle))
	{	$cod_salida_almacen=$dat_salida_detalle[0];
		echo $cod_salida_almacen."<br>";
		$cod_material=$dat_salida_detalle[1];
		$cant_unit_salida=$dat_salida_detalle[2];
		//inicio
		$sql_detalle_ingreso="select id.cod_ingreso_almacen, id.cantidad_restante from ingreso_detalle_almacenes id, 
		ingreso_almacenes i
		where i.cod_ingreso_almacen=id.cod_ingreso_almacen and i.cod_almacen='$cod_almacen_arreglar' 
		and id.cod_material='$cod_material' and id.cantidad_restante<>0 and i.ingreso_anulado=0 order by id.cod_ingreso_almacen";
		
		//echo "SQL DET ING. : ".$sql_detalle_ingreso."<br>";

		$resp_detalle_ingreso=mysqli_query($enlaceCon, $sql_detalle_ingreso);
		$cantidad_bandera=$cant_unit_salida;
		$bandera=0;
		while($dat_detalle_ingreso=mysqli_fetch_array($resp_detalle_ingreso))
		{	$cod_ingreso_almacen=$dat_detalle_ingreso[0];
			$cantidad_restante=$dat_detalle_ingreso[1];
			if($bandera!=1)
			{
				if($cantidad_bandera>$cantidad_restante)
				{	$sql_salida_det_ingreso="insert into salida_detalle_ingreso 
									values('$cod_salida_almacen','$cod_ingreso_almacen','$cod_material','$cantidad_restante')";
					$resp_salida_det_ingreso=mysqli_query($enlaceCon, $sql_salida_det_ingreso);
					$cantidad_bandera=$cantidad_bandera-$cantidad_restante;
					$upd_cantidades="update ingreso_detalle_almacenes set cantidad_restante=0 where 
									cod_ingreso_almacen='$cod_ingreso_almacen' and cod_material='$cod_material'";
					$resp_upd_cantidades=mysqli_query($enlaceCon, $upd_cantidades);
				}
				else
				{		$sql_salida_det_ingreso="insert into salida_detalle_ingreso values('$cod_salida_almacen','$cod_ingreso_almacen','$cod_material','$cantidad_bandera')";
						$resp_salida_det_ingreso=mysqli_query($enlaceCon, $sql_salida_det_ingreso);
						$cantidad_a_actualizar=$cantidad_restante-$cantidad_bandera;
						$bandera=1;
						$upd_cantidades="update ingreso_detalle_almacenes set cantidad_restante=$cantidad_a_actualizar 
										where cod_ingreso_almacen='$cod_ingreso_almacen' and cod_material='$cod_material'";
						$resp_upd_cantidades=mysqli_query($enlaceCon, $upd_cantidades);
						$cantidad_bandera=$cantidad_bandera-$cantidad_restante;
				}
			}
		}
		
		//fin	
	}
	
}

echo "OK MATERIAL..... $sqlMat";
?>