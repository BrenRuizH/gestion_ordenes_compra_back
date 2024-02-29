<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $nombre = $_POST['nombre'];
    $horma_id = $_POST['horma_id'];

    $query = "CALL AgregarMaterial('$nombre', '$horma_id');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Material creado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al crear el material", "error" => $mysql->error]);
    }
}
