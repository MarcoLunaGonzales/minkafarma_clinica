<?php
function obtenerVentaMesAnio($mes, $anio) {

    $url = "http://localhost:8090/minkafarma/ws_siat/ws_montoventa.php";
    $data = array(
        "accion" => "obtener_venta",
        "sIdentificador" => "farma_online",
        "sKey" => "RmFyYl9pdF8wczIwMjI=",
        "mes" => $mes,
        "anio" => $anio
    );

    //var_dump($data);

    $options = array(
        "http" => array(
            "header" => "Content-Type: application/json\r\n",
            "method" => "POST",
            "content" => json_encode($data)
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    return json_decode($response, true);
}

// Llamada a la función

//echo "hola";

$resultado = obtenerVentaMesAnio(9, 2024);
print_r($resultado);
?>