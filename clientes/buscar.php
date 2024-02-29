<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $codigo = $_GET['codigo'];

    $query = "SELECT * FROM clientes WHERE codigo LIKE '%$codigo%';";

    $resultSet = $mysql->query($query);

    if($resultSet->num_rows > 0){
        $clientes = array();
        while ($row = $resultSet->fetch_assoc()) {
            array_push($clientes, $row);
        }
        echo json_encode($clientes);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron clientes."));
    }
}
