
<?php

    date_default_timezone_set('America/La_Paz');

    include "conexionmysqli2_fpdf.php";
    require_once('funciones.php');
    require('funcion_nombres.php');
    require('NumeroALetras.php');
    include('phpqrcode/qrlib.php'); 
    require_once 'assets/libraries/fpdf/fpdf.php';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


    if(isset($_GET["codVenta"])){
        $codigoVenta=$_GET["codVenta"];
    }else{
        $codigoVenta=$codigoVenta;
    }

    $cod_ciudad=$_COOKIE["global_agencia"];

    $sql="SELECT a.cod_ciudad from salida_almacenes s join almacenes a on s.cod_almacen=a.cod_almacen where s.cod_salida_almacenes='$codigoVenta'";
    // echo $sql;
    $respq=mysqli_query($enlaceCon,$sql);
    $row = mysqli_fetch_array($respq);
    $cod_ciudad = $row[0];
    // echo "cod_ciudad".$cod_ciudad;

    //OBTENEMOS EL LOGO Y EL NOMBRE DEL SISTEMA
    $logoEnvioEmail=obtenerValorConfiguracion($enlaceCon,13);
    $nombreSistemaEmail=obtenerValorConfiguracion($enlaceCon,12);

    $sqlConf="select id, valor from configuracion_facturas where id=1 and cod_ciudad='$cod_ciudad'";
    // echo $sqlConf;
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $nombreTxt = $row['valor'];

    $sqlConf="select id, valor from configuracion_facturas where id=10 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $nombreTxt2 = $row[1];

    $sqlConf="select id, valor from configuracion_facturas where id=2 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $sucursalTxt = $row[1];

    $sqlConf="select id, valor from configuracion_facturas where id=3 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $direccionTxt = $row[1];

    $sqlConf="select id, valor from configuracion_facturas where id=4 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $telefonoTxt = $row[1];

    $sqlConf="select id, valor from configuracion_facturas where id=5 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $ciudadTxt = $row[1];

    $sqlConf="select id, valor from configuracion_facturas where id=6 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $txt1 = $row[1];

    $sqlConf="select id, valor from siat_leyendas where id=1";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $txt2 = $row[1];


    $sqlConf="select id, valor from configuracion_facturas where id=9 and cod_ciudad='$cod_ciudad'";
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $row = mysqli_fetch_array($respConf);
    $nitTxt = $row[1];


    $sqlDatosFactura="SELECT '' as nro_autorizacion, 
                            '' as codigo_control, 
                            f.nit, 
                            f.razon_social, 
                            DATE_FORMAT(f.siat_fechaemision, '%d/%m/%Y') as siat_fechaemision, 
                            f.fecha 
                    FROM salida_almacenes f
                    WHERE f.cod_salida_almacenes=$codigoVenta
                    LIMIT 1";
    // echo $sqlDatosFactura;
    $respDatosFactura=mysqli_query($enlaceCon,$sqlDatosFactura);
    $rowDatosFactura = mysqli_fetch_array($respDatosFactura);
    
    
    $nroAutorizacion    = $rowDatosFactura['nro_autorizacion'];
    $nitCliente         = $rowDatosFactura['nit'];
    $razonSocialCliente = $rowDatosFactura['razon_social'];
    $razonSocialCliente = strtoupper($razonSocialCliente);
    $fechaFactura       = $rowDatosFactura['siat_fechaemision'];


    $cod_funcionario=$_COOKIE["global_usuario"];
    //datos documento
    $sqlDatosVenta="SELECT DISTINCT DATE_FORMAT(s.fecha, '%d/%m/%Y'), t.`nombre`, 'cliente', s.`nro_correlativo`, s.descuento, s.hora_salida,s.monto_total,s.monto_final,s.monto_efectivo,s.monto_cambio,s.cod_chofer,s.cod_tipopago,s.cod_tipo_doc,s.fecha,(SELECT cod_ciudad from almacenes where cod_almacen=s.cod_almacen)as cod_ciudad,s.cod_cliente,s.siat_cuf,s.siat_complemento,(SELECT nombre_tipopago from tipos_pago where cod_tipopago=s.cod_tipopago) as nombre_pago,s.siat_fechaemision,s.siat_codigotipoemision,s.siat_codigoPuntoVenta,(SELECT descripcionLeyenda from siat_sincronizarlistaleyendasfactura where codigo=s.siat_cod_leyenda) as leyenda,(SELECT siat_unidadProducto from ciudades where cod_ciudad in (select cod_ciudad from almacenes where cod_almacen=s.cod_almacen)) as unidad_medida, UPPER(s.razon_social) as siat_usuario,
    (SELECT CONCAT(f.nombres, ' ', f.paterno) FROM funcionarios f WHERE f.codigo_funcionario = s.cod_chofer) as funcionario
            from `salida_almacenes` s, `tipos_docs` t, `clientes` c
            where s.`cod_salida_almacenes`='$codigoVenta' and s.cod_tipo_doc=t.codigo";
    // echo $sqlDatosVenta;
    $respDatosVenta=mysqli_query($enlaceCon,$sqlDatosVenta);
    $siat_complemento="";
    $usuarioCaja="";
    while($datDatosVenta=mysqli_fetch_array($respDatosVenta)){
        $nombreVendedor = $datDatosVenta['funcionario'];
        $cuf=$datDatosVenta['siat_cuf'];
        $fechaVenta=$datDatosVenta[0];
        $nombreTipoDoc=$datDatosVenta[1];
        $nombreCliente=$datDatosVenta[2];
        $nroDocVenta=$datDatosVenta[3];
        $descuentoVenta=$datDatosVenta[4];
        $descuentoVenta=redondear2($descuentoVenta);
        $horaFactura=$datDatosVenta[5];
        $montoTotal2=$datDatosVenta['monto_total'];
        $montoFinal2=$datDatosVenta['monto_final'];
        $montoEfectivo2=$datDatosVenta['monto_efectivo'];
        $montoCambio2=$datDatosVenta['monto_cambio'];
        $montoTotal2=redondear2($montoTotal2);
        $montoFinal2=redondear2($montoFinal2);

        $montoEfectivo2=redondear2($montoEfectivo2);
        $montoCambio2=redondear2($montoCambio2);

        $descuentoCabecera=$datDatosVenta['descuento'];
        $cod_funcionario=$datDatosVenta['cod_chofer'];
        $tipoPago=$datDatosVenta['cod_tipopago'];
        $tipoDoc=$datDatosVenta['nombre'];
        $codTipoDoc=$datDatosVenta['cod_tipo_doc'];

        $fecha_salida=$datDatosVenta['fecha'];
        $hora_salida=$datDatosVenta['hora_salida'];
        $cod_ciudad_salida=$datDatosVenta['cod_ciudad'];
        $cod_cliente=$datDatosVenta['cod_cliente'];

        $siat_complemento=$datDatosVenta['siat_complemento'];

        $siat_codigotipoemision=$datDatosVenta['siat_codigotipoemision'];
        $siat_codigopuntoventa=$datDatosVenta['siat_codigoPuntoVenta'];

        $unidad_medida=str_replace('<br>', ' ', $datDatosVenta['unidad_medida']);

        $usuarioCaja=$datDatosVenta['siat_usuario'];

        $nombrePago=$datDatosVenta['nombre_pago'];
        $txt3=$datDatosVenta['leyenda'];
        $fechaFactura=date("d/m/Y",strtotime($datDatosVenta['fecha']));
        // $nombrePago="EFECTIVO";
        // if($tipoPago!=1){
        //     $nombrePago="TARJETA/OTROS";
        // }
    }

    if($siat_codigotipoemision==2){
        $sqlConf="select id, valor from siat_leyendas where id=3";
    }else{
        $sqlConf="select id, valor from siat_leyendas where id=2";
    }
    $respConf=mysqli_query($enlaceCon,$sqlConf);
    $txtLeyendaFin=mysqli_result($respConf,0,1);
    
    /***********************************************************************************/
    /*                          Datos Generales de la Factura                          */
    /***********************************************************************************/
    // Tamaño Carta
    // $pdf = new FPDF($orientation='P',$unit='mm', 'Letter');
    // Media Hoja Carta
    $pdf = new FPDF($orientation='P',$unit='mm', 'A4');
    $pdf->AddPage();
    #Establecemos los márgenes izquierda, arriba y derecha:
    $pdf->SetMargins(10, 25 , 30);

    /************************************/
    /*              TITULO              */
    /************************************/
    $pdf->SetFont('Arial','B',12);    
    $textypos = 5;
    $pdf->setY(6);$pdf->setX(82);
    $pdf->Cell(5,$textypos,utf8_decode("NOTA REMISIÓN"));
    // Nro Venta
    $pdf->SetFont('Arial','',10);    
    $textypos = 5;
    $pdf->setY(18);$pdf->setX(125);
    $pdf->Cell(5,$textypos,utf8_decode("Nro.      ".$nroDocVenta));

    $pdf->setY(14); $pdf->setX(125);
    $pdf->Cell(5, $textypos,utf8_decode("NIT:".$nitTxt));

    $pdf->SetFont('Arial','',7); 
    $pdf->setY(10);$pdf->setX(98);
    $pdf->Cell(5,$textypos,utf8_decode(""),0,0,'C');


    /************************************/
    /*              LOGO                */
    /************************************/
    $pdf->setY(35);$pdf->setX(10);
    // $pdf->Image('pruebaImg.jpg' , 10, 20, 20, 20,'JPG', 'http://www.desarrolloweb.com');

    /********************************************************/
    /*              Datos Generales de la Factura           */
    /********************************************************/
    // Titulos
    $url_img = obtenerValorConfiguracion($enlaceCon,13);
    $pdf->Image('assets/imagenes/'.$url_img, 7, 10, 29, 29);
    $pdf->SetFont('Arial','B',7);    
    $pdf->setY(12); $pdf->setX(32);
    $pdf->Cell(5, $textypos, utf8_decode($nombreTxt)); 
    $pdf->setY(15); $pdf->setX(32);
    $pdf->Cell(5, $textypos,utf8_decode($sucursalTxt));
    $pdf->setY(18); $pdf->setX(32);
    $pdf->Cell(5, $textypos,utf8_decode($direccionTxt));

    $pdf->SetFont('Arial','B',6);    
    $pdf->setY(21); $pdf->setX(32);
    $pdf->Cell(5, $textypos,"TEL: $telefonoTxt");
    $pdf->setY(24); $pdf->setX(32);
    $pdf->SetFont('Arial','B',7);    
    $pdf->Cell(5, $textypos,utf8_decode($ciudadTxt));
    $pdf->setY(27); $pdf->setX(32);
    $pdf->Cell(5, $textypos,"");


    // Detalle

    $longitud = strlen($cuf);
    $mitad = $longitud / 2;
    $mitad=$mitad+1;
    $parte1 = substr($cuf, 0, $mitad);
    $parte2 = substr($cuf, $mitad);

    $pdf->SetFont('Arial','B',7);
    $pdf->setY(22); $pdf->setX(191);
    $pdf->Cell(5, $textypos,utf8_decode(""), 0, 0,'R');
    $pdf->setY(25); $pdf->setX(190);
    $pdf->Cell(5, $textypos,utf8_decode(""), 0, 0,'R');

    /********************************************/
    /*              Datos de Cliente            */
    /********************************************/
    // DETALLE CLIENTE
    $sql="select dir_cliente from clientes where cod_cliente = '$cod_cliente'";
    $resp=mysqli_query($enlaceCon,$sql);
    $direccion = '';
    while($row=mysqli_fetch_array($resp)){
        $direccion=$row['dir_cliente'];
    }
    // Información
    $pdf->SetFont('Arial','B',7);    
    $pdf->setY(35);$pdf->setX(10);
    $pdf->Cell(30,$textypos,utf8_decode("Nombre/Razón Social :"), 'LTB', 0, 'L'); 
    $pdf->setY(40);$pdf->setX(10);
    $pdf->Cell(20,$textypos,utf8_decode("Dirección :"), 'L', 0, 'L');

    $pdf->SetFont('Arial','',7);    
    $pdf->setY(35);$pdf->setX(40);
    $pdf->Cell(110,$textypos,utf8_decode($razonSocialCliente), 'TB', 0, 'L');  
    $pdf->setY(40);$pdf->setX(25);
    $pdf->MultiCell(120, 3, utf8_decode($direccion), 0, 'L');
    // Datos Especificos
    $pdf->SetFont('Arial','B',7);    
    $pdf->setY(35);$pdf->setX(150);
    $pdf->Cell(30,$textypos,utf8_decode("NIT/CI/CEX:"), 'TB', 0, 'L');
    $pdf->setY(40);$pdf->setX(150);
    $pdf->Cell(20,$textypos,utf8_decode("Cod. Cliente :"), 0, 'L');

    $pdf->SetFont('Arial','',7);    
    $pdf->setY(35); $pdf->setX(170);
    if($siat_complemento!=""){
        $pdf->Cell(30, $textypos, ($nitCliente." - ".$siat_complemento), 'TRB', 0, 'L');
    }else{
        $pdf->Cell(30, $textypos, ($nitCliente." ".$siat_complemento), 'TRB', 0, 'L');
    }
    $pdf->setY(40);$pdf->setX(170);
    $pdf->Cell(30, $textypos, $cod_cliente, 'R', 0, 'L');

    // Detalle
    $pdf->SetFont('Arial','B',7);    
    $pdf->setY(45);$pdf->setX(10);
    $pdf->Cell(70,$textypos,utf8_decode("Vendedor:"), 'BTL', 0, 'L'); 
    $pdf->setY(45);$pdf->setX(80);
    $pdf->Cell(70,$textypos,utf8_decode("Tipo Pago:"), 'BT', 0, 'L');
    $pdf->setY(45);$pdf->setX(150);
    $pdf->Cell(50,$textypos,utf8_decode("Fecha Factura:"), 'BTR', 0, 'L');

    
    $pdf->SetFont('Arial','',7);    
    $pdf->setY(45);$pdf->setX(25);
    $pdf->Cell(60,$textypos,utf8_decode($nombreVendedor), 'B', 0, 'L'); 
    $pdf->setY(45);$pdf->setX(95);
    $pdf->Cell(50,$textypos,utf8_decode($nombrePago), 'B', 0, 'L');
    $pdf->setY(45);$pdf->setX(170);
    $pdf->Cell(10,$textypos,utf8_decode($fechaFactura), 'B', 0, 'L');

    $pdf->Ln();
    /*******************************************************************************/
    /*                      PREPACIÓN DE LISTA DE ITEMS                            */
    /*******************************************************************************/
    $suma_total=0;
        //// Arrar de Productos
    $products = [];

    $contador_items=0;                    
    $cantidad_por_defecto=5;//cantidad de items por defect

    $sqlDetalle="SELECT m.codigo_material, s.orden_detalle,m.descripcion_material,s.observaciones,s.precio_unitario,sum(s.cantidad_unitaria) as cantidad_unitario,
    sum(s.descuento_unitario) as descuento_unitario, sum(s.monto_unitario) as monto_unitario
    from salida_detalle_almacenes s, material_apoyo m 
    where m.codigo_material=s.cod_material and s.cod_salida_almacen='$codigoVenta'
    group by m.codigo_material, s.orden_detalle,m.descripcion_material, s.observaciones,s.precio_unitario
    order by s.orden_detalle;";
    // echo $sqlDetalle;
    $respDetalle=mysqli_query($enlaceCon,$sqlDetalle);

    $yyy=65;
    $montoTotal=0;$descuentoVentaProd=0;
    while($datDetalle=mysqli_fetch_array($respDetalle)){                        
        $observaciones=$datDetalle['observaciones'];
        if($datDetalle['observaciones']==null){
            $observaciones="";
        }
        $codInterno=$datDetalle['codigo_material'];
        $cantUnit=$datDetalle['cantidad_unitario'];
        $nombreMat=$datDetalle['descripcion_material'];
        // $nombreMat=$observaciones;
        $precioUnit=$datDetalle['precio_unitario'];
        $descUnit=$datDetalle['descuento_unitario'];

        //$montoUnit=$datDetalle[5];
        $montoUnit=($cantUnit*$precioUnit)-$descUnit;
        
        //recalculamos el precio unitario para mostrar en la factura.
        //$precioUnitFactura=$montoUnit/$cantUnit;
        $precioUnitFactura=($cantUnit*$precioUnit)/$cantUnit;
        $cantUnit=redondear2($cantUnit);
        $precioUnit=redondear2($precioUnit);
        $montoUnit=redondear2($montoUnit);
        
        $precioUnitFactura=redondear2($precioUnitFactura);
        // $precioUnitFactura=number_format($precioUnitFactura,2);

        // - $descUnit
        $descUnit=redondear2($descUnit);  
        // $descUnit=number_format($descUnit,2);  
        $descuentoVentaProd+=$descUnit;
        $montoUnitProd=($cantUnit*$precioUnit);

        $montoUnitProdDesc=$montoUnitProd-$descUnit;
        $montoUnitProdDesc=redondear2($montoUnitProdDesc);
        // $montoUnitProdDesc=number_format($montoUnitProdDesc,2);

        $montoUnitProd=redondear2($montoUnitProd);
        
        
        $sqlDetalleLote = "SELECT ma.descripcion_material,
                      sda.lote,
                      sda.cantidad_unitaria as cantidad_lote,
                      sda.fecha_vencimiento as vencimiento
               FROM salida_detalle_almacenes sda
               INNER JOIN material_apoyo ma ON sda.cod_material = ma.codigo_material
               WHERE sda.cod_salida_almacen = '$codigoVenta'
               AND sda.cod_material = '$codInterno'
               ORDER BY ma.codigo_material";
        $respDetalleLote = mysqli_query($enlaceCon, $sqlDetalleLote);
        $productos_lote = array();
        if ($respDetalleLote) {
            while ($row = mysqli_fetch_assoc($respDetalleLote)) {
                $productos_lote[] = $row;
            }
        }
        $productos_lote_json = json_encode($productos_lote);

        $products[] = [
            $codInterno,
            $nombreMat,
            $unidad_medida,
            $cantUnit,
            number_format($precioUnitFactura,2),
            number_format($descUnit,2),
            number_format($montoUnitProdDesc,2),
            $productos_lote_json
        ];
        $montoTotal+=$montoUnitProdDesc;
        $contador_items++;
    }

    //// Array de Cabecera
    $header = array("Cod Producto", "Descripción", "LOTE", "CANT", "VENC","Unidad Medida","Cantidad","Precio\nUnitario","Descuento","Subtotal");
    // Column widths
    $w = array(7, 58, 3, 3, 3, 7, 3, 7, 7, 7);
    $pdf->SetFont('Arial','B',6);    
    // Header
    $add_size = 8.5;
    for($i=0;$i<count($header);$i++)
        $pdf->Cell($w[$i] + $add_size, 7, utf8_decode($header[$i]), 'LTRB', 0, 'C');
    $pdf->Ln();
    // Data
    $total = 0;
    $pdf->SetFont('Arial','',6);
    $add_size2 = 7.5;
    foreach($products as $row)
    {
        $array = json_decode($row[7], true) ?? [];
        $lote   = "";
        $cant   = "";
        $cant_u = "";
        $venc   = "";
        $row_index = 0;
        foreach($array as $val){
            $row_index++;
            $lote   .= $val['lote']."\n";
            $cant   .= (empty($val['cantidad_lote']) ? 0 : $val['cantidad_lote'])."\n";
            // $cant_u .= (empty($val->cantidad_loteunitaria) ? 0 : $val->cantidad_loteunitaria)."\n";
            $venc   .= (empty($val['vencimiento'])   ? 0 : $val['vencimiento'])."\n";
        }

        $row_index = ($row_index==0)?1:$row_index;

        $y = $pdf->getY();
        $x = $pdf->GetX();

        $pdf->SetFont('Arial','',7);
        $pdf->multiCell(15.5, 5*$row_index, utf8_decode($row[0]), 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 15.5); // regresar a columna anterior mas espacio de la columna

        $y = $pdf->getY();
        $x = $pdf->GetX();
        $nombreProductoX=$row[1];
        $nombreProductoX=strtoupper( substr($nombreProductoX,0,40) );

        $pdf->multiCell(66.5, 5*$row_index, utf8_decode($nombreProductoX), 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 66.5); // regresar a columna anterior mas espacio de la columna

        $pdf->SetFont('Arial','',4);
        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(11.5, 5, $lote, 1, 'L', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 11.5); // regresar a columna anterior mas espacio de la columna
        /*****************************************************************************/

        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(11.5, 5, $cant, 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 11.5); // regresar a columna anterior mas espacio de la columna
        /*****************************************************************************/
        // $y = $pdf->getY();
        // $x = $pdf->GetX();
        // $pdf->multiCell(5.5, 5, $cant_u, 1, 'B', false);
        // $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        // $pdf->SetY($y); // regresar a fila anterior
        // $pdf->SetX($x + 5.5); // regresar a columna anterior mas espacio de la columna
        /*****************************************************************************/
        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(11.5, 5, $venc, 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 11.5); // regresar a columna anterior mas espacio de la columna

        $pdf->SetFont('Arial','',4);
        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(15.5, 5*$row_index, utf8_decode($row[2]), 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 15.5); // regresar a columna anterior mas espacio de la columna

        $pdf->SetFont('Arial','',6);
        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(11.5, 5*$row_index, utf8_decode($row[3]), 1, 'B', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 11.5); // regresar a columna anterior mas espacio de la columna

        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(15.5, 5*$row_index, utf8_decode($row[4]), 1, 'R', false);
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 15.5); // regresar a columna anterior mas espacio de la columna

        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(15.5, 5*$row_index, utf8_decode($row[5]), 1, 'R');
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 15.5); // regresar a columna anterior mas espacio de la columna

        $y = $pdf->getY();
        $x = $pdf->GetX();
        $pdf->multiCell(15.5, 5*$row_index, utf8_decode($row[6]), 1, 'R');
        $max_y = $pdf->getY() > $y ? $pdf->getY() : $y;
        $pdf->SetY($y); // regresar a fila anterior
        $pdf->SetX($x + 15.5); // regresar a columna anterior mas espacio de la columna

        $pdf->Ln();
        // echo "row[3] : ".$row[3]."<br>";
        // echo "row[4] : ".$row[4]."<br>";
        $total+=$row[3]*$row[4];

    }
    /////////////////////////////
    //// Apartir de aqui esta la tabla con los subtotales y totales
    $yposdinamic = 60 + (count($products)*10);
    $pdf->Ln();


    /*******************************************************************************/
    /*                      PREPACIÓN DE TOTALES ITEMS                             */
    /*******************************************************************************/
    //El monto Total es la sumatoria de los productos
    $yyy=$yyy+6;
    // echo $montoTotal;
    $descuentoVenta=number_format($descuentoVenta,2,'.','');
    //$montoFinal=$montoTotal-$descuentoVenta-$descuentoVentaProd;
    $montoFinal=$montoTotal-$descuentoVenta;
    //$montoTotal=number_format($montoTotal,1,'.','')."0";
    $montoFinal=number_format($montoFinal,2,'.','');

    $arrayDecimal=explode('.', $montoFinal);
    if(count($arrayDecimal)>1){
        list($montoEntero, $montoDecimal) = explode('.', $montoFinal);
    }else{
        list($montoEntero,$montoDecimal)=array($montoFinal,0);
    }

    if($montoDecimal==""){
        $montoDecimal="00";
    }
    $txtMonto=NumeroALetras::convertir($montoEntero);

    
    $sqlDir="select valor_configuracion from configuraciones where id_configuracion=46";
    $respDir=mysqli_query($enlaceCon,$sqlDir);
    $urlDir=mysqli_result($respDir,0,0);
               
    $cadenaQR=$urlDir."/consulta/QR?nit=$nitTxt&cuf=$cuf&numero=$nroDocVenta&t=2";
    $codeContents = $cadenaQR; 

    $fechahora=date("dmy.His");
    $fileName="qrs/".$fechahora.$nroDocVenta.".png"; 
        
    QRcode::png($codeContents, $fileName,QR_ECLEVEL_L, 3);

    $sqlGlosa="select cod_tipopreciogeneral from `salida_almacenes` s where s.`cod_salida_almacenes`=$codigoVenta";
    $respGlosa=mysqli_query($enlaceCon,$sqlGlosa);
    $codigoPrecio=mysqli_result($respGlosa,0,0);
    // $txtGlosaDescuento="";
    // $sql1="SELECT glosa_factura from tipos_preciogeneral where codigo=$codigoPrecio and glosa_estado=1";
    // $resp1=mysqli_query($enlaceCon,$sql1);
    // while($filaDesc=mysqli_fetch_array($resp1)){    
    //     $txtGlosaDescuento=iconv('utf-8', 'windows-1252', $filaDesc[0]);        
    // }


    //////////////////////////////////////////////////////////////////////////////////////
    $header = array("", "");
    // Column widths
    $w2 = array(30, 30);
    // Header

    /********************************************************/
    /*          TOTALES Y SUBTOTALES DE REGISTRO            */
    /********************************************************/
    // $entero=floor(round($importe,2));
    // $decimal=$importe-$entero;
    // $centavos=round($decimal*100);
    // if($centavos<10){
    //     $centavos="0".$centavos;
    // }

    $pdf->setY(-65);
    $pdf->SetFont('Arial','B',7);  
    $pdf->Cell(115, 6, ("Son: ".$txtMonto." ".$montoDecimal."/100 Bolivianos"), 1, 0, 'L');

    $pdf->SetFont('Arial','',7);
    $pdf->Cell(45, 6, utf8_decode("SUBTOTAL Bs:"), 1, 0, 'R');
    $pdf->Cell($w2[1], 6, number_format($montoTotal, 2, ".",","), 1, 0, 'R');
    $pdf->Ln();

    /********************************/
    /*          Codigo QR           */
    /********************************/
    $pdf->setX(10);
    // $pdf->Cell(115, 20,$pdf->Image($fileName , 15, $pdf->GetY(), 20, 20,'PNG', ''), 1, 0, 'R');
    $pdf->Cell(115, 20, "", 1, 0, 'L');

    /****************************************/
    /*          TOTALES DE REGISTRO         */
    /****************************************/
    $pdf->setX(125);
    $pdf->Cell($w2[0]+15, 5,utf8_decode("DESCUENTO Bs: "), 1, 0, 'R');
    $pdf->Cell($w2[1], 5, number_format($descuentoVenta, 2, ".",","), 1, 0, 'R');
    $pdf->Ln();

    $pdf->setX(125);
    $pdf->Cell($w2[0]+15, 5,utf8_decode("TOTAL Bs:"), 1, 0, 'R');
    $pdf->Cell($w2[1], 5, number_format($montoFinal, 2, ".",","), 1, 0, 'R');
    $pdf->Ln();

    $pdf->SetFont('Arial','B',6);
    $pdf->setX(125);
    $pdf->Cell($w2[0]+15, 5,utf8_decode("MONTO A PAGAR Bs:"), 1, 0, 'R');
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell($w2[1], 5, number_format($montoFinal, 2, ".",","), 1, 0, 'R');
    $pdf->Ln();

    $pdf->SetFont('Arial','B',6);
    $pdf->setX(125);
    $pdf->Cell($w2[0]+15, 5,utf8_decode("IMPORTE BASE CRÉDITO FISCAL:"),1, 0, 'R');
    $pdf->SetFont('Arial','B',7);
    $pdf->Cell($w2[1], 5, number_format($montoFinal, 2, ".",","), 1, 0, 'R');
    $pdf->Ln();
    $pdf->Ln(); 

    /////////////////////////////

    $yposdinamic += 60;

    $y = $pdf->getY();
    $x = $pdf->GetX();
    $pdf->SetFont('Arial', '', 7);
    $pdf->multiCell(190, 3, utf8_decode(""), 0, 'C', false);
    $pdf->multiCell(190, 3, utf8_decode($txt3), 0, 'C', false);
    $pdf->multiCell(190, 3, utf8_decode($txtLeyendaFin), 0, 'C', false);


    // Leyenda
    // $pdf->setY($yposdinamic);
    // $pdf->setX(10);
    // $pdf->SetFont('Arial', '', 5);    
    // $pdf->MultiCell(190, 3, utf8_decode($txt2.' '.$txt3.' '.$txtLeyendaFin), 0, 'C');

    $pdf->output($cuf.".pdf", "I");
?>
