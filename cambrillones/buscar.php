<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $codigo_cliente = $_GET['codigo_cliente'];

    $query = "SELECT cam.* FROM cambrillones cam 
              INNER JOIN hormas h ON cam.horma_id = h.id
              INNER JOIN clientes c ON h.cliente_id = c.id
              WHERE c.codigo = '$codigo_cliente';";

    $resultSet = $mysql->query($query);

    if($resultSet->num_rows > 0){
        $cambrillones = array();
        while ($row = $resultSet->fetch_assoc()) {
            array_push($cambrillones, $row);
        }
        echo json_encode($cambrillones);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron cambrillones."));
    }
}

