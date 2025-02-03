<?php

require("conexionmysqli.php");
require('funciones.php');
require('function_formatofecha.php');
require("estilos_almacenes.inc");

?>
<html>
    <head>
        <title>Busqueda</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link href="lib/externos/jquery/jquery-ui/completo/jquery-ui-1.8.9.custom.css" rel="stylesheet" type="text/css"/>
        <link href="lib/css/paneles.css" rel="stylesheet" type="text/css"/>
        <script type='text/javascript' language='javascript'>

function nuevoAjax()
{   var xmlhttp=false;
    try {
            xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
    try {
        xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
    } catch (E) {
        xmlhttp = false;
    }
    }
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}       
    
function ShowBuscar(){
    document.getElementById('divRecuadroExt').style.visibility='visible';
    document.getElementById('divProfileData').style.visibility='visible';
    document.getElementById('divProfileDetail').style.visibility='visible';
}

function HiddenBuscar(){
    document.getElementById('divRecuadroExt').style.visibility='hidden';
    document.getElementById('divProfileData').style.visibility='hidden';
    document.getElementById('divProfileDetail').style.visibility='hidden';
}
    
function ShowFacturar(codVenta,numCorrelativo){
    document.getElementById("cod_venta").value=codVenta;
    document.getElementById("nro_correlativo").value=numCorrelativo;
    
    document.getElementById('divRecuadroExt2').style.visibility='visible';
    document.getElementById('divProfileData2').style.visibility='visible';
    document.getElementById('divProfileDetail2').style.visibility='visible';
}

function HiddenFacturar(){
    document.getElementById('divRecuadroExt2').style.visibility='hidden';
    document.getElementById('divProfileData2').style.visibility='hidden';
    document.getElementById('divProfileDetail2').style.visibility='hidden';
}

function funOk(codReg,funOkConfirm)
{   $.get("programas/salidas/frmConfirmarCodigoSalida.php","codigo="+codReg, function(inf1) {
        dlgAC("#pnldlgAC","Codigo de confirmacion",inf1,function(){
            var cad1=$("input#idtxtcodigo").val();
            var cad2=$("input#idtxtclave").val();
            if(cad1!="" && cad2!="") {
                dlgEsp.setVisible(true);
                $.get("programas/salidas/validacionCodigoConfirmar.php","codigo="+cad1+"&clave="+cad2, function(inf2) {
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

function ajaxBuscarVentas(f){
    var fechaIniBusqueda, fechaFinBusqueda, nroCorrelativoBusqueda, verBusqueda, global_almacen, clienteBusqueda,vendedorBusqueda,tipoVentaBusqueda,clienteBusqueda, nitBusqueda, razonsocialBusqueda;
    fechaIniBusqueda=document.getElementById("fechaIniBusqueda").value;
    fechaFinBusqueda=document.getElementById("fechaFinBusqueda").value;
    nroCorrelativoBusqueda=document.getElementById("nroCorrelativoBusqueda").value;
    global_almacen=document.getElementById("global_almacen").value;
    vendedorBusqueda=document.getElementById("vendedorBusqueda").value;
    tipoVentaBusqueda=document.getElementById("tipoVentaBusqueda").value;
    clienteBusqueda=document.getElementById("clienteBusqueda").value;

    nitBusqueda=document.getElementById("nitBusqueda").value;
    razonsocialBusqueda=document.getElementById("razonsocialBusqueda").value;

    location.href="navegadorVentas.php?fechaIniBusqueda="+fechaIniBusqueda+"&fechaFinBusqueda="+fechaFinBusqueda+"&nroCorrelativoBusqueda="+nroCorrelativoBusqueda+"&vendedorBusqueda="+vendedorBusqueda+"&tipoPagoBusqueda="+tipoVentaBusqueda+"&clienteBusqueda="+clienteBusqueda+"&nitBusqueda="+nitBusqueda+"&razonsocialBusqueda="+razonsocialBusqueda;
}
/*function convertirNR(codFactura){
    if(confirm('Esta seguro de Anular la Factura y Convertir en NR.')){
        location.href='convertirNRAnularFac.php?codigo_registro='+codFactura;
    }else{
        return(false);
    }
}*/
function enviar_nav()
{   location.href='registrar_salidaventas.php';
}
function editar_salida(f)
{   var i;
    var j=0;
    var j_cod_registro, estado_preparado;
    var fecha_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                fecha_registro=f.elements[i-2].value;
                estado_preparado=f.elements[i-1].value;
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
        {   if(f.fecha_sistema.value==fecha_registro)
            {
                {   
                        location.href='editarVentas.php?codigo_registro='+j_cod_registro;
                }
            }
            else
            {   funOk(j_cod_registro,function(){
                        location.href='editarVentas.php?codigo_registro='+j_cod_registro;
                    });
            }
        }
    }
}
function anular_salida(f)
{   var i;
    var j=0;
    var j_cod_registro, estado_preparado;
    var fecha_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                fecha_registro=f.elements[i-2].value;
                estado_preparado=f.elements[i-1].value;
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
        {   funOk(j_cod_registro,function() {
                        location.href='anular_venta.php?codigo_registro='+j_cod_registro;
            });
        }
    }
}

function anular_salida_siat(f)
{   var i;
    var j=0;
    var j_cod_registro, estado_preparado;
    var fecha_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                fecha_registro=f.elements[i-2].value;
                estado_preparado=f.elements[i-1].value;
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
        {   
            // funOk(j_cod_registro,function() {
            //             location.href='anular_venta_siat.php?codigo_registro='+j_cod_registro;
            // });

            funVerifi(j_cod_registro);


        }
    }
}
function funVerifi(codReg){   
    // var cod_sucursal=$("#cod_sucursal").val();

var parametros={"codigo":codReg};
 $.ajax({
        type: "GET",
        dataType: 'html',
        url: "programas/salidas/frmConfirmarCodigoSalida_siat.php",
        data: parametros,
        success:  function (resp) { 
            $("#datos_anular").html(resp);
            $("#codigo_salida").val(codReg);
            $("#contrasena_admin").val("");
            $("#modalAnularFactura").modal("show");           
      }
 }); 
}

function confirmarCodigo(){ 
    document.getElementById('boton_anular').style.visibility='hidden';
   // var cod_sucursal=document.getElementById("cod_sucursal").value;  
   // var cod_personal=document.getElementById("cod_personal").value;  
   
  var cad1=$("input#idtxtcodigo").val();
  var cad2=$("input#idtxtclave").val(); 
  var per=$("#rpt_personal").val(); 

  // var rpt_tipoanulacion=$("#rpt_tipoanulacion").val(); 
  // var glosa_anulacion=$("input#glosa_anulacion").val(); 

  var enviar_correo=$("input#enviar_correo").val();
  var correo_destino=$("input#correo_destino").val();

  var parametros={"codigo":cad1,"clave":cad2,"per":per};
  $.ajax({
        type: "GET",
        dataType: 'html',
        url: "programas/salidas/validacionCodigoConfirmar_siat.php",
        data: parametros,
        success:  function (resp) { 
            if(resp==1) {
                location.href='anular_venta_siat.php?codigo_registro='+$("#codigo_salida").val()+'&id_caja='+per+'&enviar_correo='+enviar_correo+'&correo_destino='+correo_destino;
            }else{
               Swal.fire("Error!","El codigo que ingreso es incorrecto","error");
               $("#modalAnularFactura").modal("hide");    
            }
      }
 }); 
}

function cambiarCancelado(f)
{   var i;
    var j=0;
    var j_cod_registro, estado_preparado;
    var fecha_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                fecha_registro=f.elements[i-2].value;
                estado_preparado=f.elements[i-1].value;
                j=j+1;
            }
        }
    }
    if(j>1)
    {   alert('Debe seleccionar solamente un registro.');
    }
    else
    {   if(j==0)
        {   alert('Debe seleccionar un registro.');
        }
        else
        {      
            funOk(j_cod_registro,function() {
                location.href='cambiarEstadoCancelado.php?codigo_registro='+j_cod_registro+'';
            });            
        }
    }
}

function cambiarNoEntregado(f)
{   var i;
    var j=0;
    var j_cod_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                j=j+1;
            }
        }
    }
    if(j>1)
    {   alert('Debe seleccionar solamente una Salida.');
    }
    else
    {   if(j==0)
        {   alert('Debe seleccionar una Salida.');
        }
        else
        {   location.href='cambiarEstadoNoEntregado.php?codigo_registro='+j_cod_registro+'';
        }
    }
}
function cambiarNoCancelado(f)
{   var i;
    var j=0;
    var j_cod_registro;
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   j_cod_registro=f.elements[i].value;
                j=j+1;
            }
        }
    }
    if(j>1)
    {   alert('Debe seleccionar solamente una Salida.');
    }
    else
    {   if(j==0)
        {   alert('Debe seleccionar una Salida.');
        }
        else
        {   location.href='cambiarEstadoNoCancelado.php?codigo_registro='+j_cod_registro+'';
        }
    }
}

function imprimirNotas(f)
{   var i;
    var j=0;
    datos=new Array();
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   datos[j]=f.elements[i].value;
                j=j+1;
            }
        }
    }
    if(j==0)
    {   alert('Debe seleccionar al menos una salida para imprimir la Nota.');
    }
    else
    {   window.open('navegador_detallesalidamaterialResumen.php?codigo_salida='+datos+'','','scrollbars=yes,status=no,toolbar=no,directories=no,menubar=no,resizable=yes,width=1000,height=800');
    }
}
function preparar_despacho(f)
{   var i;
    var j=0;
    datos=new Array();
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   datos[j]=f.elements[i].value;
                j=j+1;
            }
        }
    }
    if(j==0)
    {   alert('Debe seleccionar al menos una salida para proceder a su preparado.');
    }
    else
    {   location.href='preparar_despacho.php?datos='+datos+'&tipo_material=1&grupo_salida=2';
    }
}
function enviar_datosdespacho(f)
{   var i;
    var j=0;
    datos=new Array();
    for(i=0;i<=f.length-1;i++)
    {   if(f.elements[i].type=='checkbox')
        {   if(f.elements[i].checked==true)
            {   datos[j]=f.elements[i].value;
                j=j+1;
            }
        }
    }
    if(j==0)
    {   alert('Debe seleccionar al menos una salida para proceder al registro del despacho.');
    }
    else
    {   location.href='registrar_datosdespacho.php?datos='+datos+'&tipo_material=1&grupo_salida=2';
    }
}
function llamar_preparado(f, estado_preparado, codigo_salida)
{   window.open('navegador_detallesalidamateriales.php?codigo_salida='+codigo_salida,'popup','');
}
        </script>
    </head>
    <body>
<?php


$nroCorrelativoBusqueda="";
$fechaIniBusqueda="";
$fechaFinBusqueda="";
$vendedorBusqueda="";
$clienteBusqueda="";
$tipoPagoBusqueda="";
$fecha_sistema="";
$estado_preparado="";
$nitBusqueda="";
$razonSocialBusqueda="";
$view=1;

// Configuración para la Asignación de Medico
$configAsignacionMedico = obtenerValorConfiguracion($enlaceCon, 56);

if(isset($_GET["nroCorrelativoBusqueda"])){
    $nroCorrelativoBusqueda = $_GET["nroCorrelativoBusqueda"];    
}
if(isset($_GET["fechaIniBusqueda"])){
    $fechaIniBusqueda = $_GET["fechaIniBusqueda"];
}
if(isset($_GET["fechaFinBusqueda"])){
    $fechaFinBusqueda = $_GET["fechaFinBusqueda"];
}
if(isset($_GET["vendedorBusqueda"])){
    $vendedorBusqueda = $_GET["vendedorBusqueda"];
}
if(isset($_GET["tipoPagoBusqueda"])){
    $tipoPagoBusqueda = $_GET["tipoPagoBusqueda"];
}
if(isset($_GET["clienteBusqueda"])){
    $clienteBusqueda = $_GET["clienteBusqueda"];
}
if(isset($_GET["nitBusqueda"])){
    $nitBusqueda = $_GET["nitBusqueda"];
}
if(isset($_GET["razonsocialBusqueda"])){
    $razonSocialBusqueda = $_GET["razonsocialBusqueda"];
}
$global_admin_cargo=$_COOKIE["global_admin_cargo"];

echo "<form method='post' action=''>";
echo "<input type='hidden' name='fecha_sistema' value='$fecha_sistema'>";
//$global_admin_cargo
echo "<h1>Listado de Ventas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='navegadorVentas2.php'><img src='imagenes/go2.png' width='15'></a></h1>";

// echo "<table class='texto' cellspacing='0' width='90%'>
// <tr><th>Leyenda:</th>
// <th>Ventas Registradas</th><td bgcolor='#f9e79f' width='5%'></td>
// <th>Ventas Entregadas</th><td bgcolor='#1abc9c' width='5%'></td>
// <th>Ventas Anuladas</th><td bgcolor='#e74c3c' width='5%'></td>
// <td bgcolor='' width='10%'>&nbsp;</td></tr></table>";

//
echo "<div class='divBotones'>
        <input type='button' value='Registrar' name='adicionar' class='boton' onclick='enviar_nav()'>
        <input type='button' value='Buscar' class='boton-azul' onclick='ShowBuscar()'></td>      
        <!--input type='button' value='Anular' class='boton2' onclick='anular_salida(this.form)'-->
        <input type='button' value='Anular Con SIAT' class='boton2' onclick='anular_salida_siat(this.form)'>
    </div>";

echo "<div id='divCuerpo'><center><table class='texto'>";
echo "<tr><th>&nbsp;</th><th>Nro. Doc</th><th>Fecha/hora<br>Venta</th><th>Usuario</th><th>TipoPago</th><th>Cliente<br>Razon Social</th><th>NIT/CI</th><th>Monto</th><th>Observaciones</th>";

// Asignación de Medico
if($configAsignacionMedico){
    echo "<th>Asignar Médico</th>";
}

echo "<th>Imprimir Factura</th><th>Documento SIAT</th>";


// Configuración para Formulario Psicotrópico
$config_psicotropico = obtenerValorConfiguracion($enlaceCon, 61) ?? 2;
if($config_psicotropico == 1){
    echo "<th title='Psicotrópico'>PSIC</th>";
}

echo "</tr>";
    
echo "<input type='hidden' name='global_almacen' value='$global_almacen' id='global_almacen'>";

$consulta = "SELECT s.cod_salida_almacenes, s.fecha, s.hora_salida, ts.nombre_tiposalida, 
    (select a.nombre_almacen from almacenes a where a.`cod_almacen`=s.almacen_destino), s.observaciones, 
    s.estado_salida, s.nro_correlativo, s.salida_anulada, s.almacen_destino, 
    (select c.nombre_cliente from clientes c where c.cod_cliente = s.cod_cliente), s.cod_tipo_doc, razon_social, nit,
    (select t.nombre_tipopago from tipos_pago t where t.cod_tipopago=s.cod_tipopago)as tipopago,siat_estado_facturacion,
    (select concat(f.paterno, ' ', f.nombres) from funcionarios f where f.codigo_funcionario=s.cod_chofer)as vendedor,
    s.monto_final,
    (SELECT rs.cod_medico FROM recetas_salidas rs WHERE rs.cod_salida_almacen = s.cod_salida_almacenes LIMIT 1) as cod_medico,
    s.cod_tipopago,
    s.monto_cancelado,
    COALESCE(
        (SELECT 1
         FROM salida_detalle_almacenes sda
         LEFT JOIN material_apoyo m ON m.codigo_material = sda.cod_material
         WHERE m.producto_controlado = 1
         AND sda.cod_salida_almacen = s.cod_salida_almacenes LIMIT 0,1), 
        0
    ) as psicotropico,
    s.nro_receta,
    s.nombre_paciente,
    s.nombre_medico,
    (SELECT std.descripcion from siat_sincronizarparametricatipodocumentoidentidad std where std.codigoClasificador=s.siat_codigotipodocumentoidentidad limit 0,1)as tipodocid
    FROM salida_almacenes s, tipos_salida ts 
    WHERE s.cod_tiposalida = ts.cod_tiposalida AND s.cod_almacen = '$global_almacen' and s.cod_tiposalida=1001 
    and s.cod_tipo_doc in (1,4)";

if($nroCorrelativoBusqueda!="")
{   $consulta = $consulta."AND s.nro_correlativo='$nroCorrelativoBusqueda' ";
}
if($vendedorBusqueda!="")
{   $consulta = $consulta."AND s.cod_chofer='$vendedorBusqueda' ";
}
if($clienteBusqueda!="")
{   $consulta = $consulta."AND s.cod_cliente='$clienteBusqueda' ";
}
if($nitBusqueda!="")
{   $consulta = $consulta."AND s.nit='$nitBusqueda' ";
}
if($razonSocialBusqueda!="")
{   $consulta = $consulta."AND s.razon_social like '%$razonSocialBusqueda%' ";
}
if($tipoPagoBusqueda!="")
{   $consulta = $consulta."AND s.cod_tipopago='$tipoPagoBusqueda' ";
}
if($fechaIniBusqueda!="" && $fechaFinBusqueda!="")
{   $consulta = $consulta."AND '$fechaIniBusqueda'<=s.fecha AND s.fecha<='$fechaFinBusqueda' ";
}   
$consulta = $consulta."ORDER BY s.fecha desc, s.hora_salida desc, s.nro_correlativo desc limit 0, 1000 ";

//echo $consulta;
//
$resp = mysqli_query($enlaceCon,$consulta);
    
    
// MODIFICACIÓN DEL FORMATO DEL PDF
$formato_hoja = obtenerValorConfiguracion($enlaceCon, 58);
// CONFIGURACIÓN DE CONTROL DE PAGO (REGISTRO DE PAGO 1: CREDITO, 2: TODOS)
$conf_controlPago = obtenerValorConfiguracion($enlaceCon, 59);
while ($dat = mysqli_fetch_array($resp)) {
    $codigo = $dat[0];
    $fecha_salida = $dat[1];
    $fecha_salida_mostrar = "$fecha_salida[8]$fecha_salida[9]-$fecha_salida[5]$fecha_salida[6]-$fecha_salida[0]$fecha_salida[1]$fecha_salida[2]$fecha_salida[3]";
    $hora_salida = $dat[2];
    $nombre_tiposalida = $dat[3];
    $nombre_almacen = $dat[4];
    $obs_salida = $dat[5];
    $estado_almacen = $dat[6];
    $nro_correlativo = $dat[7];
    $salida_anulada = $dat[8];
    $cod_almacen_destino = $dat[9];
    $nombreCliente=$dat[10];
    $codTipoDoc=$dat[11];
    $nombreTipoDoc=nombreTipoDoc($enlaceCon,$codTipoDoc);
    $razonSocial=$dat[12];
    $razonSocial=strtoupper($razonSocial);
    $nitCli=$dat[13];
    $tipoPago=$dat[14];
    $nombreVendedor=$dat[16];
    $montoVenta=$dat[17];
    $montoVentaFormat=formatonumeroDec($montoVenta);
    $codMedico = $dat[18];
    $codTipoPago = $dat[19];
    $montoCancelado = $dat[20];

    $verf_psicotropico = $dat['psicotropico'];
    $nro_receta        = $dat['nro_receta'];
    $nombre_paciente   = $dat['nombre_paciente'];
    $nombre_medico     = $dat['nombre_medico'];

    $tipoDocIdentidad = $dat['tipodocid'];
    $tipoDocIdentidadArray = explode(' ',$tipoDocIdentidad);
    $tipoDocIdentidadAbrev=$tipoDocIdentidadArray[0];
    
    echo "<input type='hidden' name='fecha_salida$nro_correlativo' value='$fecha_salida_mostrar'>";

    $urlDetalle="dFacturaElectronica.php";
    $siat_estado_facturacion=$dat['siat_estado_facturacion'];
    // if($codTipoDoc==4){
    //     $nro_correlativo="<i class=\"text-danger\">M-$nro_correlativo</i>";
    //     if($siat_estado_facturacion!=1){
    //          //$urlDetalle="dFactura.php";
    //     }
    // }else{
    //     $nro_correlativo="F-$nro_correlativo";
    // }
    $datosAnulacion="";
    $stikea="";
    $stikec="";
    if($salida_anulada==1){
        $stikea="<strike class='text-danger'>";        
        $stikec=" (ANULADO)</strike>";
        // $datosAnulacion="title='<small><b class=\"text-primary\">$nro_correlativo ANULADA<br>Caja:</b> ".nombreVisitador($dat['cod_chofer_anulacion'])."<br><b class=\"text-primary\">F:</b> ".date("d/m/Y H:i",strtotime($dat['fecha_anulacion']))."</small>' data-toggle='tooltip'";
        $chk="";
    }
    
    $sqlEstadoColor="select color from estados_salida where cod_estado='$estado_almacen'";
    $respEstadoColor=mysqli_query($enlaceCon,$sqlEstadoColor);
    $numFilasEstado=mysqli_num_rows($respEstadoColor);
    if($numFilasEstado>0){
        $datEstadoColor = mysqli_fetch_array($respEstadoColor);
        $color_fondo = $datEstadoColor[0];
        //$color_fondo=mysql_result($respEstadoColor,0,0);
        
    }else{
        $color_fondo="#ffffff";
    }
    $chk = "";

    $estadoPago = 1; // Inicialmente establece el estado de pago como pagado (1)
    // Verifica las condiciones de pago según la configuración
    if ($conf_controlPago == 1 && $codTipoPago == 4 && $montoCancelado < $montoVenta) {
        // Si la configuración es de crédito y el tipo de pago es 4 (crédito) y el monto cancelado es menor al monto de venta
        $estadoPago = 0; // Cambia el estado de pago a pendiente (0)
        $chk = "<input type='checkbox' name='codigo' value='$codigo'>";
    } elseif ($conf_controlPago == 2 && $montoCancelado < $montoVenta) {
        // Si la configuración es para todos los pagos y el monto cancelado es menor al monto de venta
        $estadoPago = 0; // Cambia el estado de pago a pendiente (0)
        $chk = "<input type='checkbox' name='codigo' value='$codigo'>";
    } elseif ($conf_controlPago ==3 ) {
        $color_fondo="GreenYellow";
        //SIN CONTROL DE PAGOS
        $chk = "<input type='checkbox' name='codigo' value='$codigo'>";
    }
    else{
        $color_fondo="GreenYellow";
        //se debe ponre en vacio solo temporal habilitado
        $chk = "";
        //$chk = "<input type='checkbox' name='codigo' value='$codigo'>";
    }


    // Determina el icono basado en el estado de pago
    $icono = ($estadoPago) ? '<i class="material-icons medium text-success" title="Pagado en su totalidad">check_circle</i>' : '<i class="material-icons medium text-warning" title="Pendiente de pago">hourglass_empty</i>';

    
    echo "<input type='hidden' name='estado_preparado' value='$estado_preparado'>";
    //echo "<tr><td><input type='checkbox' name='codigo' value='$codigo'></td><td align='center'>$fecha_salida_mostrar</td><td>$nombre_tiposalida</td><td>$nombre_ciudad</td><td>$nombre_almacen</td><td>$nombre_funcionario</td><td>&nbsp;$obs_salida</td><td>$txt_detalle</td></tr>";
    echo "<tr>";
    echo "<td align='center'>&nbsp;$chk</td>";
    echo "<td align='center' class='verCobros' data-cod_venta='$codigo' style='cursor:pointer;'>$icono $stikea$nombreTipoDoc-$nro_correlativo $stikec</td>";
    echo "<td align='center'>$stikea$fecha_salida_mostrar $hora_salida$stikec</td>";
    echo "<td>$stikea $nombreVendedor $stikec</td>";
    echo "<td>$stikea $tipoPago $stikec</td>
    <td>$stikea &nbsp;<small><span style='color:blue'>$nombreCliente</span></small> <br> $razonSocial $stikec</td>
    <td>$stikea&nbsp; $tipoDocIdentidadAbrev-$nitCli $stikec</td>
    <td>$stikea&nbsp;$montoVentaFormat $stikec</td>
    <td>$stikea &nbsp;$obs_salida $stikec</td>";

    $nombreMedico="";
    if($configAsignacionMedico){
        // Asignación de Médico
        if($codMedico>0){
            $sqlMedico="SELECT CONCAT(m.apellidos,' ', m.nombres) as medico from recetas_salidas r, medicos m 
                where m.codigo=r.cod_medico and r.cod_salida_almacen=$codigo";
            $respMedico=mysqli_query($enlaceCon, $sqlMedico);
            if($dat=mysqli_fetch_array($respMedico)){
                $nombreMedico=$dat[0];
            }
        }
        echo "<td align='center'>
                <button type='button' class='btn btn-sm btn-warning btn-fab' onclick=\"asignarMedico('$codigo', '$codMedico')\">
                    <i class='material-icons'>medical_services</i>
                </button><br>
                <small>$nombreMedico</small>
            </td>";
    }

    $url_notaremision = "navegador_detallesalidamuestras.php?codigo_salida=$codigo";    
    
    $urlConversionFactura="convertNRToFactura.php?codVenta=$codigo";    
    
    $NRparaMostrar=$nombreTipoDoc."-".$nro_correlativo;
    $fechaParaMostrar=$fecha_salida_mostrar;
    
    /*echo "<td bgcolor='$color_fondo'><a href='javascript:llamar_preparado(this.form, $estado_preparado, $codigo)'>
        <img src='imagenes/icon_detail.png' width='30' border='0' title='Detalle'></a></td>";
    */

    
    /*switch ($siat_estado_facturacion) {
        default:$color_fondo="#12A4DF";break;      

        case 1:$color_fondo="#99E80A";break;
        case 2:$color_fondo="#FF2E09";break;
        case 3:$color_fondo="#12A4DF";break;  

    }
    */

    if($formato_hoja == 1){
        echo "<td bgcolor='$color_fondo'><a href='formatoFacturaOnLine.php?codVenta=$codigo' target='_BLANK'><img src='imagenes/factura1.jpg' width='30' border='0' title='Factura Formato Pequeño'></a></td>";
    }else if($formato_hoja == 2){
        echo "<td bgcolor='$color_fondo'><a href='formatoFacturaOnLineHoja.php?codVenta=$codigo' target='_BLANK'><img src='imagenes/factura1.jpg' width='30' border='0' title='Factura Normal'></a></td>";
    }else if ($formato_hoja==3) {
        echo "<td bgcolor='$color_fondo' align='center'>
            <a href='formatoFacturaNotaFiscal.php?codVenta=$codigo' target='_BLANK'>
            <img src='imagenes/factura.png' width='30' border='0' title='Nota Fiscal'></a>
            <a href='formatoFactura.php?codVenta=$codigo' target='_BLANK'><img src='imagenes/factura1.jpg' width='30' border='0' title='Factura Formato Pequeño'></a>
            </td>";
    }
    
    $codigoVentaCambio=0;
    $sqlCambio="select c.cod_cambio from salida_almacenes c where c.cod_cambio=$codigo";
    $respCambio=mysqli_query($enlaceCon,$sqlCambio);
    
    if($global_admin_cargo==1){
        while($datCambio=mysqli_fetch_array($respCambio)){
            $codigoVentaCambio=$datCambio[0];        
        }
        echo "<td  bgcolor='$color_fondo'>
            <a href='$urlDetalle?codigo_salida=$codigo' target='_BLANK' title='DOCUMENTO FACTURA'  class='text-dark'>
                <i class='material-icons'>description</i>
            </a>";
        echo "</td>";
    }else{
        echo "<td bgcolor='$color_fondo'>-";
        echo "</td>";
    }

    // Psicotrópico 
    if($verf_psicotropico == 1 && $config_psicotropico == 1){
        echo "<td>
                <a href='#' class='abrirPricotropico' data-cod_salida_almacenes='$codigo' data-nro_receta='$nro_receta' data-nombre_paciente='$nombre_paciente' data-nombre_medico='$nombre_medico' data-nro_documento='$NRparaMostrar'>
                    <img src='imagenes/editarPsicotropico.png' width='30' border='0' title='Editar Formulario Psicotrópico'>
                </a>
            </td>";
    }

    echo "</tr>";
}
echo "</table></center><br>";
echo "</div>";

echo "<div class='divBotones'>
        <input type='button' value='Registrar' name='adicionar' class='boton' onclick='enviar_nav()'>
        <input type='button' value='Buscar' class='boton-azul' onclick='ShowBuscar()'></td>      
        <!--input type='button' value='Anular' class='boton2' onclick='anular_salida(this.form)'-->
        <input type='button' value='Anular Con SIAT' class='boton2' onclick='anular_salida_siat(this.form)'>
    </div>";
    
echo "</form>";

?>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalAnularFactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
        <div class="card-header card-header-danger card-header-icon">
          <div class="card-icon">
            <i class="material-icons">delete</i>
          </div>
          <h4 class="card-title text-danger font-weight-bold">Anulación de Facturas</h4>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true" style="position:absolute;top:0px;right:0;">
            <i class="material-icons">close</i>
          </button>
        </div>
        <input type="hidden" name="codigo_salida" id="codigo_salida" value="0">
        <div class="card-body" id="datos_anular">
           
        </div>
        <div class="card-footer" >
           <button id="boton_anular" name="boton_anular" class="btn btn-default" onclick="confirmarCodigo()">ANULAR</button>
        </div>
    </div>  
    </div>
</div>
<!--    end small modal -->



<div id="divRecuadroExt" style="background-color:#666; position:absolute; width:800px; height: 600px; top:30px; left:150px; visibility: hidden; opacity: .70; -moz-opacity: .70; filter:alpha(opacity=70); -webkit-border-radius: 20px; -moz-border-radius: 20px; z-index:2;">
</div>
<div id="divProfileData" style="background-color:#FFF; width:750px; height:550px; position:absolute; top:50px; left:170px; -webkit-border-radius: 20px;     -moz-border-radius: 20px; visibility: hidden; z-index:2;">
    <div id="divProfileDetail" style="visibility:hidden; text-align:center">
        <h2 align='center' class='texto'>Buscar Ventas</h2>
        <table align='center' class='texto'>
            <tr>
                <td>Fecha Ini(dd/mm/aaaa)</td>
                <td>
                <input type='date' name='fechaIniBusqueda' id="fechaIniBusqueda" class='texto'>
                </td>
            </tr>
            <tr>
                <td>Fecha Fin(dd/mm/aaaa)</td>
                <td>
                <input type='date' name='fechaFinBusqueda' id="fechaFinBusqueda" class='texto'>
                </td>
            </tr>
            <tr>
                <td>Cliente:</td>
                <td>
                    <select name="clienteBusqueda" class="selectpicker" data-style="btn btn-success" data-live-search="true" id="clienteBusqueda">
                        <option value="">Todos</option>
                    <?php
                        $sqlClientes="SELECT cod_cliente, nombre_cliente from clientes order by 2";
                        $respClientes=mysqli_query($enlaceCon,$sqlClientes);
                        while($datClientes=mysqli_fetch_array($respClientes)){
                            $codCliBusqueda=$datClientes[0];
                            $nombreCliBusqueda=$datClientes[1];
                    ?>
                            <option value="<?php echo $codCliBusqueda;?>"><?php echo $nombreCliBusqueda;?></option>
                    <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>NIT</td>
                <td>
                <input type='text' name='nitBusqueda' id="nitBusqueda" class='texto'>
                </td>
            </tr>                       
            <tr>
                <td>Razon Social</td>
                <td>
                <input type='text' name='razonsocialBusqueda' id="razonsocialBusqueda" class='texto'>
                </td>
            </tr>                       
            <tr>
                <td>Nro. de Documento</td>
                <td>
                <input type='text' name='nroCorrelativoBusqueda' id="nroCorrelativoBusqueda" class='texto'>
                </td>
            </tr>           
            <tr>
                <td>Vendedor:</td>
                <td>
                    <select name="vendedorBusqueda" class="selectpicker" data-style="btn btn-success" id="vendedorBusqueda">
                        <option value="">Todos</option>
                    <?php
                        $sqlClientes="SELECT DISTINCT c.codigo_funcionario,CONCAT(c.paterno,' ',c.materno,' ',c.nombres) as personal from salida_almacenes s join funcionarios c on c.codigo_funcionario=s.cod_chofer order by 2;";
                        $respClientes=mysqli_query($enlaceCon,$sqlClientes);
                        while($datClientes=mysqli_fetch_array($respClientes)){
                            $codCliBusqueda=$datClientes[0];
                            $nombreCliBusqueda=$datClientes[1];
                    ?>
                            <option value="<?php echo $codCliBusqueda;?>"><?php echo $nombreCliBusqueda;?></option>
                    <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>           
            <tr>
                <td>Tipo Pago:</td>
                <td>
                    <select name="tipoVentaBusqueda" class="selectpicker" data-style="btn btn-success" id="tipoVentaBusqueda">
                        <option value="">Todos</option>
                    <?php
                        $sqlClientes="select c.cod_tipopago, c.nombre_tipopago from tipos_pago c order by 2";
                        $respClientes=mysqli_query($enlaceCon,$sqlClientes);
                        while($datClientes=mysqli_fetch_array($respClientes)){
                            $codCliBusqueda=$datClientes[0];
                            $nombreCliBusqueda=$datClientes[1];
                    ?>
                            <option value="<?php echo $codCliBusqueda;?>"><?php echo $nombreCliBusqueda;?></option>
                    <?php
                        }
                    ?>
                    </select>
                </td>
            </tr>
        </table>    
        <center>
            <input type='button' class="boton-azul" value='Buscar' onClick="ajaxBuscarVentas(this.form)">
            <input type='button' class="boton2" value='Cancelar' onClick="HiddenBuscar()">
            
        </center>
    </div>
</div>



<div id="divRecuadroExt2" style="background-color:#666; position:absolute; width:800px; height: 350px; top:30px; left:150px; visibility: hidden; opacity: .70; -moz-opacity: .70; filter:alpha(opacity=70); -webkit-border-radius: 20px; -moz-border-radius: 20px; z-index:2;">
</div>
<div id="divProfileData2" style="background-color:#FFF; width:750px; height:300px; position:absolute; top:50px; left:170px; -webkit-border-radius: 20px;    -moz-border-radius: 20px; visibility: hidden; z-index:2;">
    <div id="divProfileDetail2" style="visibility:hidden; text-align:center">
        <h2 align='center' class='texto'>Convertir a Factura</h2>
        <form name="form1" id="form1" action="convertNRToFactura.php" method="POST">
        <table align='center' class='texto'>
            <tr>
                <input type="hidden" name="cod_venta" id="cod_venta" value="0">
                <td>Nro.</td>
                <td>
                <input type='text' name='nro_correlativo' id="nro_correlativo" class='texto' disabled>
                </td>
            </tr>
            <tr>
                <td>Razon Social</td>
                <td>
                <input type='text' name='razon_social_convertir' id="razon_social_convertir" class='texto' required>
                </td>
            </tr>
            <tr>
                <td>NIT</td>
                <td>
                <input type='number' name='nit_convertir' id="nit_convertir" class='texto' required>
                </td>
            </tr>
        </table>    
        <center>
            <input type='submit' value='Convertir' class='boton' >
            <input type='button' value='Cancelar' class='boton2' onClick="HiddenFacturar()">
            
        </center>
        </form>
    </div>
</div>





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

        <!-- Modal Asignación de Medico -->
        <div class="modal fade modal-primary" id="modalRecetaVenta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content card">
                    <div class="card-header card-header-primary card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">medical_services</i>
                        </div>
                        <h4 class="card-title text-dark font-weight-bold">Datos del Médico</h4>
                        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true" style="position:absolute;top:0px;right:0;">
                            <i class="material-icons">close</i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Codigo de "Salida Ventas" -->
                            <input type="hidden" id="cod_salida_almacen">
                            <!-- Codigo de "Medico" Seleccionado -->
                            <input type="hidden" id="cod_medico">
                            <div class="col-sm-12">    
                                <div class="row">
                                    <label class="col-sm-3 col-form-label">Nombres y Apellidos</label>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input class="form-control" type="text" style="background: #A5F9EA;" id="buscar_app_doctor" value="" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                        </div>
                                    </div>
                                    <a href="#" class='btn btn-success btn-sm btn-fab float-right' onclick='buscarMedicoTest()'><i class='material-icons'>search</i></a>
                                </div>
                                <br>
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr style="background: #652BE9;color:#fff;">
                                            <th width="60%">Nombre</th>
                                            <th>Especialidad</th>
                                            <th>-</th>
                                        </tr>
                                    </thead>
                                    <tbody id="datos_medicos">                   	  	
                                    </tbody>
                                </table>                      
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
            require("navegadorVentas_modalpsicotropico.php");
        ?>


        <!-- Modal Detalle de Pagos -->
        <div class="modal fade modal-primary" id="modalDetalleCobro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content card">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">payment</i>
                        </div>
                        <h4 class="card-title text-dark font-weight-bold">Cobranza</h4>
                        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true" style="position:absolute;top:0px;right:0;">
                            <i class="material-icons">close</i>
                        </button>
                    </div>
                    <div class="card-body" id="htmlDetalleCobro">
                    </div>
                </div>
            </div>
        </div>

        <script>
            /**
             * Obtiene lista de Medicos
             */
            function asignarMedico(cod_salida_almacen, cod_medico){
                $('#cod_salida_almacen').val(cod_salida_almacen);
                $('#cod_medico').val(cod_medico);

                var orden  = 'codigo';
                var codigo = cod_medico;
                var parametros={order_by:orden,cod_medico:cod_medico};

                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "ajaxListaMedicos.php",
                    data: parametros,
                    success:  function (resp) {
                        $("#datos_medicos").html(resp);      
                        $('#modalRecetaVenta').modal('show');           	   
                    }
                });	
            }
            /**
             * Buscar Medico
             */
            function buscarMedicoTest(){
                var codigo=$("#cod_medico").val();
                var nom=$("#buscar_nom_doctor").val();
                var app=$("#buscar_app_doctor").val();
                var espe=$("#especialidad_doctor").val();
                var parametros={order_by:"2",cod_medico:codigo,nom_medico:nom,app_medico:app,espe:espe};
                $.ajax({
                    type: "GET",
                    dataType: 'html',
                    url: "ajaxListaMedicos.php",
                    data: parametros,
                    success:  function (resp) {
                        // console.log(resp)
                        $("#datos_medicos").html(resp);                 	   
                    }
                });	
            }
            /**
             * Selecciona Médico
             */
            function asignarMedicoVenta(cod_medico_select){
                var cod_salida_almacen = $('#cod_salida_almacen').val();
                var nombre_medico = $('body #medico_lista'+cod_medico_select).html();
                    Swal.fire({
                    title: '¿Está seguro?',
                    text: 'Se asignara el médico '+ nombre_medico + ' a la venta seleccionada.',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No',
                    allowOutsideClick: false  // Evita que se cierre al hacer clic fuera del cuadro de diálogo
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "edit_medico_venta.php",
                            data: { 
                                cod_medico: cod_medico_select,
                                cod_salida_almacen: cod_salida_almacen,
                            },
                            success: function(response) {
                                let resp = JSON.parse(response);
                                Swal.fire({
                                    type: 'success',
                                    title: 'Mensaje',
                                    text: resp.message,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    $('#modalRecetaVenta').modal('toggle');
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    }
                });
            }

            // Obtiene Detalle de Cobro
            $('.verCobros').on('click', function(){
                let codigo = $(this).data('cod_venta');
                $.ajax({
                    type: "GET",
                    url: "ver_detalle_cobro.php",
                    data: {
                        codigo: codigo 
                    },
                    success: function(cuerpoHTML) {
                        $('#htmlDetalleCobro').html(cuerpoHTML);
                        $('#modalDetalleCobro').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        </script>
    </body>
</html>


