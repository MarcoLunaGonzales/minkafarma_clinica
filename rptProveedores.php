<html>
<head>
  <meta charset="utf-8" />
  <link rel="STYLESHEET" type="text/css" href="stilos.css" />
</head>
<body>
<?php
set_time_limit(0);

require("conexionmysqli2.inc");
require("estilos_almacenes.inc");

require('funcion_nombres.php');
require('funciones.php');


$rptProveedores=$_POST['rpt_proveedores'];
$rptProveedores = implode(",", $rptProveedores); 


// $rpt_territorio=$_GET['codTipoTerritorio'];
// $almacenes=obtenerAlmacenesDeCiudadString($rpt_territorio);
$fecha_reporte=date("d/m/Y");

// $nombre_territorio=obtenerNombreSucursalAgrupado($rpt_territorio);
// $nombre_territorio=str_replace(",",", ", $nombre_territorio);
?><style type="text/css"> 
        thead tr th { 
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #ffffff;
        }
    
        .table-responsive { 
            height:200px;
            overflow:scroll;
        }
    </style>

<h1>Proveedores</h1>
<h2>Fecha Reporte: <?=$fecha_reporte?></h2>

<?php

$sql="SELECT p.cod_proveedor, p.nombre_proveedor, p.direccion, p.telefono1, p.telefono2, p.contacto, p.cod_tipoproveedor, tp.nombre_tipoventa,
    p.politica_devolucion
    FROM proveedores AS p 
    LEFT JOIN tipos_proveedor tp ON tp.cod_tipoventa = p.cod_tipoproveedor
    WHERE p.cod_proveedor in ($rptProveedores) ORDER BY p.nombre_proveedor ASC ";
$resp=mysqli_query($enlaceCon, $sql);
echo "<br><table align='center' class='texto' width='70%'>
<tr>
    <th>-</th>
    <th>Codigo</th>
    <th>Nombre</th>
    <th>Direccion</th>
    <th>Politica de Devolución</th>
    <th>Contacto</th>
    <th>Tipo Proveedor</th>
</tr>";

$cont=0;
while($reg=mysqli_fetch_array($resp)){
    $cont++;
    $codProv = $reg["cod_proveedor"];
    $nomProv = $reg["nombre_proveedor"];
    $direccion = $reg["direccion"];
    $telefono1 = $reg["telefono1"];
    $telefono2 = $reg["telefono2"];
    $contacto  = $reg["contacto"];
    $tipoProveedor = $reg["cod_tipoproveedor"];
    $nombreTipoventa = $reg["nombre_tipoventa"];
    $politicaDevolucion = $reg["politica_devolucion"];

    echo "<tr>";
    echo "<td>
        $cont</td>
        <td>$codProv</td>
        <td>$nomProv</td>
        <td>$direccion</td>
        <td><span class='textomedianorojo'>$politicaDevolucion</span></td>
        <td>$contacto</td>
        <td>$nombreTipoventa</td>
        <td>$txtLineas</td>
    </tr>";

}
echo "</table></br>";

?>
