<?php

require_once "../conexionmysqlipdf.inc";

//echo "HOLA";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $datos = json_decode(file_get_contents("php://input"), true); 
    //$accion = NULL;
    
    $mes=$_GET['mes'];
    var_dump($datos);
    echo "MES: ".$mes;
    echo "accion: ".$datos['accion']." identificador: ".$datos['sIdentificador']." key: ".$datos['sKey'];
    
    if (isset($datos['accion']) && isset($datos['sIdentificador']) && isset($datos['sKey'])) {
        if (
            $datos['sIdentificador'] == "farma_online" &&
            $datos['sKey'] == "123456"
        ) {
            $accion = $datos['accion']; // recibimos la accion
            $mesServicio = $datos['mes'];
            $anioServicio = $datos['anio'];
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
        
        header('Content-type: application/json');
        echo json_encode($resultado);
    } else {
        $resp = array(
            "estado" => 5,
            "mensaje" => "El acceso al WS es incorrecto"
        );
        
        header('Content-type: application/json');
        echo json_encode($resp);
    }
}

?>
