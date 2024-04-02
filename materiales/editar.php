<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $horma_id = $_POST['horma_id'];

    $query = "CALL EditarMaterial($id, '$nombre', $horma_id);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
//425
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Material editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el material", "error" => $mysql->error]);
    }
}
