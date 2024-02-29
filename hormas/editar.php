<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $cliente_id = $_POST['cliente_id'];

    $query = "CALL EditarHorma($id, '$nombre', $cliente_id);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
//425
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Horma editada exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar la horma", "error" => $mysql->error]);
    }
}
