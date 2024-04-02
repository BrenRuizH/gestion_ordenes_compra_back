<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $nombre = $_POST['nombre'];
    $contrasenia = $_POST['contrasenia'];
    $rol_id = $_POST['rol_id'];

    $query = "CALL AgregarUsuario('$nombre', '$contrasenia', '$rol_id');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Usuario creado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al crear el usuario", "error" => $mysql->error]);
    }
    
}
