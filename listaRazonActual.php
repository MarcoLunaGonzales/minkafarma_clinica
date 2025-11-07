<?php
$estilosVenta=1;
require("conexionmysqli2.inc");
$rs=$_GET['rs'];

$sql="select DISTINCT s.nit, s.razon_social from salida_almacenes s 
	where s.razon_social like '%$rs%' and s.nit!='' and s.razon_social!='' order by s.fecha desc limit 0,5";

//	echo $sql;
$resp=mysqli_query($enlaceCon,$sql);
$index=0;
while($dat=mysqli_fetch_array($resp)){
	$rsocial=$dat[1];
	$nit=$dat[0];
	?>
<tr>
		<td><?=$rsocial?></td>
		<td><?=$nit?></td>
		<td><a href="#" onclick="asignarNit('<?=$nit?>');return false;" class="btn btn-rose btn-sm btn-fab"><i class="material-icons">check</i></a></td>
	</tr>
<?php
	$index++;
}

if($index==0){
   ?><tr><td colspan="3">NINGÚN NIT ENCONTRADO</td></tr><?php
}
echo "#####".$index."#####".$nit;

