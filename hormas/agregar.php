<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $nombre = $_POST['nombre'];
    $cliente_id = $_POST['cliente_id'];

    $query = "CALL AgregarHorma('$nombre', '$cliente_id');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Horma creada exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al crear la horma", "error" => $mysql->error]);
    }
}
