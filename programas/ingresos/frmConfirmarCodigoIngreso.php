<?php

$estilosVenta=1;
require("../../conexionmysqli.inc");
require("../../funciones.php");
require("../../funcion_nombres.php");


$codigo=$_GET["codigo"];
//
//echo "ddd:$codigo<br>";
//
$sqlFecha="select DAY(i.fecha), MONTH(i.fecha), YEAR(i.fecha), HOUR(i.hora_ingreso), MINUTE(i.hora_ingreso) 
from ingreso_almacenes i where i.cod_ingreso_almacen=$codigo";
$respFecha=mysqli_query($enlaceCon,$sqlFecha);

$dia=mysqli_result($respFecha,0,0);
$mes=mysqli_result($respFecha,0,1);
$ano=mysqli_result($respFecha,0,2);
$hh=mysqli_result($respFecha,0,3);
$mm=mysqli_result($respFecha,0,4);


//generamos el codigo de confirmacion
$codigoGenerado=$codigo+$dia+$mes+$ano+$hh+$mm;
//

$banderaCorreo=0;
if($banderaCorreo==2){
	$codigoIngreso=$codigo;
	$codigoGeneradoX=$codigoGenerado;
	include("../../sendEmailAprobAnulacionIngreso.php");
}

?>
<center>
    <div id='pnlfrmcodigoconfirmacion'>
        <br>
        <table class="texto" border="1" cellspacing="0" >
            <tr><td colspan="2">Introduzca el codigo de confirmacion</td></tr>
            <tr><td>Codigo:</td><td><input type="text" id="idtxtcodigo" value="<?php echo "$codigoGenerado";?>" readonly ></td></tr>
            <tr><td>Clave:</td><td><input type="text" id="idtxtclave" value="" ></td></tr>
        </table>
        <br>
    </div>
</center>
<?php

?>
