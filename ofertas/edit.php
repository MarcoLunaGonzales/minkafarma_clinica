<style>
	input[type=radio]
	{
	  /* Double-sized Checkboxes */
	  -ms-transform: scale(2); /* IE */
	  -moz-transform: scale(2); /* FF */
	  -webkit-transform: scale(2); /* Safari and Chrome */
	  -o-transform: scale(2); /* Opera */
	  transform: scale(2);
	  padding: 10px;
	}

	/* Might want to wrap a span around your checkbox text */
	.radiotext
	{
	  /* Checkbox text */
	  font-size: 110%;
	  display: inline;
	}

</style>

<?php

require("../conexionmysqli.inc");
require("../estilos2.inc");
require("configModule.php");

$sql="select nombre, abreviatura,desde,hasta, oferta_stock_limitado from $table where codigo=$codigo_registro";
$resp=mysqli_query($enlaceCon,$sql);
$dat=mysqli_fetch_array($resp);

$nombre=$dat[0];
$abreviatura=$dat[1];
$desde=strftime('%Y-%m-%d',strtotime($dat[2]));
$hasta=strftime('%Y-%m-%d',strtotime($dat[3]));
$desde_hora=strftime('%H:%M',strtotime($dat[2]));
$hasta_hora=strftime('%H:%M',strtotime($dat[3]));

$stockLimitado=$dat[4];

echo "<form action='$urlSaveEdit' method='post'>";

echo "<h1>Editar $moduleNameSingular</h1>";

echo "<center>
<table class='table table-sm'>";

echo "<tr><th align='left' class='bg-info text-white'>Nombre</th>";
echo "<input type='hidden' name='codigo' value='$codigo_registro'>";
echo "<td align='left' colspan='3'><input type='text' class='form-control' name='nombre' value='$nombre' size='30' onKeyUp='javascript:this.value=this.value.toUpperCase();' required></td></tr>";

echo "<tr><th align='left' class='bg-info text-white'>Descuento BASE %</th>";
echo "<td align='left' colspan='3'><input type='number' class='form-control' name='abreviatura' value='$abreviatura' size='20' required></td></tr>";

echo "<tr><th align='left' class='bg-info text-white'>Oferta con STOCK LIMITADO</th>";
if($stockLimitado==1){
	echo "<td align='left' colspan='3'>
			<input type='radio' name='stock_limitado' value='0'><span class='radiotext'>&nbsp;&nbsp; NO &nbsp;&nbsp;</span>
			<input type='radio' name='stock_limitado' value='1' checked><span class='radiotext'>&nbsp;&nbsp; SI &nbsp;&nbsp;</span>
	</td>";	
}else{
	echo "<td align='left' colspan='3'>
			<input type='radio' name='stock_limitado' value='0' checked><span class='radiotext'>&nbsp;&nbsp; NO &nbsp;&nbsp;</span>
			<input type='radio' name='stock_limitado' value='1'><span class='radiotext'>&nbsp;&nbsp; SI &nbsp;&nbsp;</span>
	</td>";
}
echo "</tr>";


echo "<tr><td align='left' class='bg-info text-white'>Desde</td>";
echo "<td align='left'>
	<INPUT  type='date' class='form-control' value='$desde' id='fecha_ini' size='10' name='fecha_ini'><INPUT  type='time' class='form-control' value='$desde_hora' id='hora_ini' size='10' name='hora_ini'>
</td><td align='left' class='bg-info text-white'>Hasta</td><td align='left' colspan='3'>
	<INPUT  type='date' class='form-control' value='$hasta' id='fecha_fin' size='10' name='fecha_fin'><INPUT  type='time' class='form-control' value='$hasta_hora' id='hora_fin' size='10' name='hora_fin'>
</td>";
echo "</tr>";
echo "</table>";

echo "<div class='divBotones'>
<input type='submit' class='btn btn-primary' value='Guardar'>
<input type='button' class='btn btn-danger' value='Cancelar' onClick='location.href=\"$urlList2\"'>
</div>";

echo "</form>";
?>