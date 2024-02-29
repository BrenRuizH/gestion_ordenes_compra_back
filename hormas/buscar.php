<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $codigo_cliente = $_GET['codigo_cliente'];

    $query = "SELECT h.id, h.nombre, c.codigo AS cliente FROM hormas h 
              INNER JOIN clientes c ON h.cliente_id = c.id 
              WHERE c.codigo = '$codigo_cliente';";

    $resultSet = $mysql->query($query);

    if($resultSet->num_rows > 0){
        $hormas = array();
        while ($row = $resultSet->fetch_assoc()) {
            array_push($hormas, $row);
        }
        echo json_encode($hormas);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron hormas."));
    }
}
