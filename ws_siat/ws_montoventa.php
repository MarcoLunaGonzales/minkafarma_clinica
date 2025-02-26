<?php

require_once "../conexionmysqlipdf.inc";

//echo "HOLA";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // $datos = json_decode(file_get_contents("php://input"), true); 
    
    $mes            = $_GET['mes'] ?? "";
    $anio           = $_GET['anio'] ?? null;
    $accion         = $_GET['accion'] ?? null;
    $sKey           = $_GET['sKey'] ?? null;
    $sIdentificador = $_GET['sIdentificador'] ?? null;
    
    if (!empty($accion) && !empty($sIdentificador) && !empty($sKey)) {
        if (
            $sIdentificador == "farma_online" &&
            $sKey == "123456"
        ) {
            $accion = $accion; // recibimos la accion
            $mesServicio = $mes;
            $anioServicio = $anio;
            $sucursalServicio = "1";

            $montoVenta = 0;
            if ($anioServicio > 0 && $mesServicio > 0) {
                $sql="select sum(s.monto_final) as monto
                    from `salida_almacenes` s where s.`cod_tiposalida`=1001 and s.salida_anulada=0 and
                    s.`cod_almacen` = '1001' and YEAR(s.fecha)='$anioServicio' and MONTH(s.fecha)='$mesServicio' ";  

                //echo $sql;
                
                $resp=mysqli_query($enlaceCon,$sql);
                while($detalle=mysqli_fetch_array($resp)){    
                    $montoVenta+=$detalle[0];         
                }  
            }

            $resultado = array(
                "estado" => 1,
                "mensaje" => "Correcto",
                "montoventa" => $montoVenta
            );
        } else {
            $resultado = array(
                "estado" => 4,
                "mensaje" => "Credenciales Incorrectas"
            );
        }
        ob_clean();
        header('Content-type: application/json');
        echo json_encode($resultado);
    } else {
        $resp = array(
            "estado" => 5,
            "mensaje" => "El acceso al WS es incorrecto"
        );
        
        ob_clean();
        header('Content-type: application/json');
        echo json_encode($resp);
    }
}

?>
