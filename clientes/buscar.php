<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $texto = $_GET['texto'];

    $query = "SELECT * FROM clientes WHERE codigo LIKE '%$texto%' OR razonSocial LIKE '%$texto%';";

    $resultado=$mysql->query($query);

    if($resultado->num_rows > 0) {
        $itemRecords=array();
        $itemRecords["items"]=array();
        while ($item = $resultado->fetch_assoc()) {
            extract($item);
            $itemDetails=array(
                "id" => $id,
                "codigo" => $codigo,
                "razonSocial" => $razonSocial,
                "rfc" => $rfc,
                "telefono" => $telefono,
                "pagosCon" => $pagosCon,
                "pedidosA" => $pedidosA,
                "recepcionDePedidos" => $recepcionDePedidos
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron clientes."));
    }
}