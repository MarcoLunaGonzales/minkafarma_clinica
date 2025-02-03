<?php

	require("conexionmysqlipdf.inc");

	$codProducto=$_GET["cod_producto"];

	$sql="update material_apoyo set estado=0 where codigo_material='$codProducto'";
	$resp=mysqli_query($enlaceCon,$sql);

	echo "<span style='color:red'><b>Producto Eliminado!</b></span>";
?>