<?php
$estilosVenta=1;
require("conexionmysqli2.inc");
$nitCliente=$_GET['nitCliente'];

$sql="SELECT f.cod_cliente, 
			CONCAT(f.nombre_cliente) nombre,
			'' as tipo,
			CASE WHEN f.nit_cliente = '$nitCliente' THEN 1 ELSE 0 END as selected,
			f.nit_cliente
	FROM clientes f 
	WHERE f.cod_cliente != 146
	ORDER BY f.cod_cliente DESC";
$resp=mysqli_query($enlaceCon,$sql);

//,(SELECT siat_codigotipodocumentoidentidad FROM salida_almacenes where cod_cliente=f.cod_cliente and siat_codigotipodocumentoidentidad=5 and salida_anulada=0 order by fecha desc limit 1) as tipo

$cod_cliente=146;// varios
$htmlCliente="";
$index=0;
$tipo=1;
$htmlCliente .= "<option value=''>NO REGISTRADO</option>";
while($dat=mysqli_fetch_array($resp)){
	$cod_cliente = $dat[0];
	$nombre_item = $dat[1];
	$tipo		 = $dat[2];	
	$selected 	 = $dat[3] == '1' ? 'selected' : '';
	$nit_cliente = $dat[4];	
	$index++;
	$htmlCliente.="<option value='$cod_cliente' $selected  data-nitcliente='$nit_cliente'>$nombre_item [$cod_cliente]</option>";
}
echo $cod_cliente."####".$htmlCliente."####".$tipo;