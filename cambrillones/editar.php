<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $nombre = $_POST['nombre'];
    $horma_id = $_POST['horma_id'];
    $id = $_POST['id'];

    $query = "CALL EditarCambrillon('$nombre', $horma_id, $id);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Cambrillón editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el cambrillón", "error" => $mysql->error]);
    }
}
