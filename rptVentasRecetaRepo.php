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


$fecha_ini=$_POST['fecha_ini'];
$fecha_fin=$_POST['fecha_fin'];

$rpt_ver=$_POST['rpt_ver'];

$fecha_iniconsulta=$fecha_ini;
$fecha_finconsulta=$fecha_fin;


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

<h1>Recetas Registradas</h1>
<h2>De: <?=$fecha_ini?> A: <?=$fecha_fin?></h2>
<h2>Fecha Reporte: <?=$fecha_reporte?></h2>

<?php

/*REPORTE DETALLADO POR FECHAS*/
if($rpt_ver==0 || $rpt_ver==1 || $rpt_ver==2 ){
    $sql="SELECT s.cod_salida_almacenes, 
                s.fecha, rs.cod_medico, 
                CONCAT(med.apellidos,' ',med.nombres) as medico, 
                (select e.abreviatura from especialidades e where e.codigo=med.cod_especialidad) as especialidad, 
                med.cod_especialidad, 
                m.codigo_material, 
                m.descripcion_material, 
                sd.cantidad_unitaria AS cantidadventa,
                (sd.monto_unitario)-(sd.descuento_unitario) AS montoVenta, 
                (((sd.monto_unitario-sd.descuento_unitario)/s.monto_total)*s.descuento) AS descuentocabecera
            FROM salida_almacenes s, recetas_salidas rs, salida_detalle_almacenes sd, medicos med, material_apoyo m
            WHERE s.cod_salida_almacenes = sd.cod_salida_almacen 
            AND rs.cod_salida_almacen = s.cod_salida_almacenes 
            AND s.salida_anulada = 0 
            AND rs.cod_medico > 0 
            AND sd.estado_receta = 1 
            AND rs.cod_medico = med.codigo 
            AND sd.cod_material = m.codigo_material 
            AND sd.estado_receta=1
            AND s.fecha BETWEEN '$fecha_ini' AND '$fecha_fin'";
            
    if($rpt_ver==0){
        $sql.=" ORDER BY s.fecha ASC";        
    }elseif ($rpt_ver==1 || $rpt_ver==2 ) {
        $sql.=" ORDER BY medico ASC";
    }else{
        $sql.="";
    }

    //echo $sql;
    $resp=mysqli_query($enlaceCon,$sql);
    ?>
    <center><table align='center' class='texto' width='70%' id='ventasLinea'>
      <thead>
    <tr>
      <th width="5%">&nbsp;</th>
      <th>Fecha</th>
      <th>Cod.Médico</th>
      <th>Médico</th>
      <th>Especialidad</th>
      <th>Cod.Producto</th>
      <th>Material</th>
      <th>Cantidad</th>
      <th>Monto Bs.</th>
    </tr>
    </thead>
    <tbody>
    <?php
        $index=1;
        $sumaCantidadMedico=0;
        $sumaVentasMedico=0;
        $codMedicoPivote=0;
        if($data=mysqli_fetch_array($resp)){
            $codMedicoPivote=$data['cod_medico'];
            $nombreMedicoPivote=$data['medico'];
            $especialidadPivote=$data['especialidad'];
        }

        $resp=mysqli_query($enlaceCon,$sql);
        while($data=mysqli_fetch_array($resp)){
            $cantidadVenta=$data['cantidadventa'];
            $montoVenta=$data['montoVenta']-$data['descuentocabecera'];
            $codMedico=$data['cod_medico'];
            $nombreMedico=$data['medico'];
            $especialidad=$data['especialidad'];

           if($codMedico!=$codMedicoPivote && ($rpt_ver==1 || $rpt_ver==2)){
    ?>
        <tr>
            <td>-</td>
            <td>-</td>
            <td><b><span class="textomedianonegro"><?=$codMedicoPivote?></span><b></td>
            <td><b><span class="textomedianorojo"><?=$nombreMedicoPivote?></span><b></td>
            <td><b><span class="textomedianonegro"><?=$especialidadPivote?></span><b></td>
            <td>-</td>
            <td>-</td>
            <td align="right"><b><span class="textomedianonegro"><?=number_format($sumaCantidadMedico,2,'.',',')?></span><b></td>
            <td align="right"><b><span class="textomedianonegro"><?=number_format($sumaVentasMedico,2,'.',',')?></span><b></td>
        </tr>
    <?php            
                $sumaCantidadMedico=0;
                $sumaVentasMedico=0;
                $codMedicoPivote=$codMedico;
                $nombreMedicoPivote=$nombreMedico;
                $especialidadPivote=$especialidad;
            }

            $sumaCantidadMedico+=$cantidadVenta;
            $sumaVentasMedico+=$montoVenta;

            if( $rpt_ver==0 || $rpt_ver==1 ){
    ?>
            <tr>
                <td><?=$index?></td>
                <td><?=$data['fecha']?></td>
                <td><?=$codMedico?></td>
                <td><?=$nombreMedico?></td>
                <td><?=$especialidad?></td>
                <td><?=$data['codigo_material']?></td>
                <td><?=$data['descripcion_material']?></td>
                <td align="right"><?=number_format($cantidadVenta,2,'.',',')?></td>
                <td align="right"><?=number_format($montoVenta,2,'.',',')?></td>
            </tr>
    <?php
            }
        
            $index++;
        }
        if(($rpt_ver==1 || $rpt_ver==2)){ 
    ?>
        <tr>
            <td>-</td>
            <td>-</td>
            <td><b><span class="textomedianonegro"><?=$codMedicoPivote?></span><b></td>
            <td><b><span class="textomedianorojo"><?=$nombreMedicoPivote?></span><b></td>
            <td><b><span class="textomedianonegro"><?=$especialidadPivote?></span><b></td>
            <td>-</td>
            <td>-</td>
            <td align="right"><b><span class="textomedianonegro"><?=number_format($sumaCantidadMedico,2,'.',',')?></span><b></td>
            <td align="right"><b><span class="textomedianonegro"><?=number_format($sumaVentasMedico,2,'.',',')?></span><b></td>
        </tr>
    <?php
        }
    ?>
    </tbody>
    </table></center></br>
<?php
}
?>
</body></html>
