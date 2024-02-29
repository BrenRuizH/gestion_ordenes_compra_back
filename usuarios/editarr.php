<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $contrasenia = $_POST['contrasenia'];
    $rol_id = $_POST['rol_id'];

    $query = "CALL EditarUsuario($id, '$nombre', '$contrasenia', '$rol_id');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
//425
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Usuario editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el usuario", "error" => $mysql->error]);
    }
}
