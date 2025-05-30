<?php

require('../conexionmysqli.php');
require('../function_formatofecha.php');
require('../home_almacen.php');
require('../funciones.php');

$globalSucursal=$_COOKIE['global_agencia'];


?>


<html>
    <head>
        <title>Busqueda</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="../lib/externos/jquery/jquery-ui/completo/jquery-ui-1.8.9.custom.css" rel="stylesheet" type="text/css"/>
        <link href="../lib/css/paneles.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-1.4.4.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.core.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.widget.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.button.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.mouse.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.draggable.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.position.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.resizable.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.dialog.min.js"></script>
        <script type="text/javascript" src="../lib/externos/jquery/jquery-ui/minimo/jquery.ui.datepicker.min.js"></script>
        <script type="text/javascript" src="../lib/js/xlibPrototipo-v0.1.js"></script>
        <link href="../stilos.css" rel='stylesheet' type='text/css'>
        <script type='text/javascript' language='javascript'>


function funOk(codReg,funOkConfirm)
{   $.get("../programas/ingresos/frmConfirmarCodigoIngreso.php","codigo="+codReg, function(inf1) {
        dlgAC("#pnldlgAC","Codigo de confirmacion",inf1,function(){
            var cad1=$("input#idtxtcodigo").val();
            var cad2=$("input#idtxtclave").val();
            if(cad1!="" && cad2!="") {
                dlgEsp.setVisible(true);
                $.get("../programas/ingresos/validacionCodigoConfirmar.php","codigo="+cad1+"&clave="+cad2, function(inf2) {
                    inf2=xtrim(inf2);
                    dlgEsp.setVisible(false);
                    if(inf2=="" || inf2=="OK") {
                        /**/funOkConfirm();/**/
                    } else {
                        dlgA("#pnldlgA2","Informe","<div class='pnlalertar'>El codigo ingresado es incorrecto.</div>",function(){},function(){});
                    }
                });
            } else {
                dlgA("#pnldlgA3","Informe","<div class='pnlalertar'>Introduzca el codigo de confirmacion.</div>",function(){},function(){});
            }
        },function(){});
    });
}
function enviar_nav()
{   location.href='registrarCobranza.php';
}

function anular_pago(f)
{   
	var i;
    var j=0;
    var j_cod_registro;
    var fecha_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                fecha_registro=f.elements[i-1].value;
                j=j+1;
            }
        }
    }
    if(j>1)
    {   alert('Debe seleccionar solamente un registro para anularlo.');
    }
    else
    {   if(j==0)
        {   alert('Debe seleccionar un registro para anularlo.');
        }
        else
        {   funOk(j_cod_registro,function(){
                    location.href='anularPago.php?codigo_registro='+j_cod_registro;
                });
        }
    }
}
        </script>
    </head>
    <body>
<form method='post' name='form1' action=''>
<?php


echo "<h1>Registro de Cobranzas</h1>";
echo "<table border='1' cellspacing='0' class='textomini'><tr><th>Leyenda:</th><th>Cobranza Anulada</th><td bgcolor='#ff8080' width='10%'></td>
<th>Cobranza Normal</th><td bgcolor='' width='10%'>&nbsp;</td></tr></table><br>";

    echo "<div class='divBotones'>
	<input type='button' value='Registrar Cobro' name='adicionar' class='boton' onclick='enviar_nav()'></td>
	<td><input type='button' value='Anular Cobro' name='adicionar' class='boton2' onclick='anular_pago(this.form)'></div>";

	echo "<br><center><table class='texto'>";

	echo "<tr><th>&nbsp;</th><th>Nro. Cobro</th><th>Funcionario</th>
		<th>Fecha</th><th>Monto</th><th>Observaciones</th>
        <th colspan='3'>Imprimir</th></tr>";
	
	$consulta = "SELECT c.`cod_cobro`,
       c.`fecha_cobro`,
       c.`observaciones`,
       c.`monto_cobro`,
       (select concat(f.paterno,' ',f.nombres) from funcionarios f where f.codigo_funcionario=c.cod_funcionario), 
        c.nro_cobro, 
        (select g.nombre_gestion from gestiones g where g.cod_gestion=c.cod_gestion), 
        c.cod_estado, 
        (select sum(monto_detalle) from cobros_detalle cd where cd.cod_cobro=c.cod_cobro)
from `cobros_cab` c where c.cod_ciudad='$globalSucursal'
order by c.fecha_cobro desc, c.`cod_cobro` desc limit 0, 100";
		
	$resp = mysqli_query($enlaceCon, $consulta);

	while ($dat = mysqli_fetch_array($resp)) {
		$codPago = $dat[0];
		$fechaPago= $dat[1];
        $observaciones=$dat[2];


        $montoPago = $dat[8];
		
		$montoPago=redondear2($montoPago);
		
		$nombreProveedor= $dat[4];
		$nroCobranza=$dat[5];
		$nombreGestion=$dat[6];
		$codEstado=$dat[7];
        $estilo_texto = "";
        $color_fondo = "";
		if ($codEstado == 2) {
			$color_fondo = "#ff8080";
            $estilo_texto = "text-decoration:line-through; color:red";
			$chkbox = "";
		}else {
			$color_fondo = "";
            $estilo_texto = "";
			$chkbox = "<input type='checkbox' name='codigo' value='$codPago'>";
		}
		
		echo "<tr style='$estilo_texto'>
		<td align='center'>$chkbox</td>
		<td align='center'>$nroCobranza</td>
		<td align='center'>$nombreProveedor</td>
		<td align='center'>$fechaPago</td>
		<td align='center'>$montoPago</td>
		<td>&nbsp;$observaciones</td>
        <td bgcolor='$color_fondo'><a href='notaCobranzaPequena.php?codCobro=$codPago' target='_blank'>
            <img src='../imagenes/printer.png' title='Formato Termico' width='50' heigth='30'></a></td>
		<td bgcolor='$color_fondo'><a href='notaCobranza.php?codCobro=$codPago' target='_blank'>
            <img src='../imagenes/icon_detail.png' title='Formato Pequeño' width='30' heigth='30'></a></td>
		<td bgcolor='$color_fondo'><a href='notaCobranzaCarta.php?codCobro=$codPago' target='_blank'>
            <img src='../imagenes/detalle2.png' title='Formato Carta' width='30'></a>
        </td>
		</tr>";
	}
	echo "</table></center><br>";
    echo "<div class='divBotones'><input type='button' value='Registrar Cobro' name='adicionar' class='boton' onclick='enviar_nav()'></td>
		<td><input type='button' value='Anular Cobro' name='adicionar' class='boton2' onclick='anular_pago(this.form)'></div>";

		echo "</form>";

?>
        <script type='text/javascript' language='javascript'>
        </script>
        <div id="pnldlgfrm"></div>
        <div id="pnldlgSN"></div>
        <div id="pnldlgAC"></div>
        <div id="pnldlgA1"></div>
        <div id="pnldlgA2"></div>
        <div id="pnldlgA3"></div>
        <div id="pnldlgArespSvr"></div>
        <div id="pnldlggeneral"></div>
        <div id="pnldlgenespera"></div>
    </body>
</html>
