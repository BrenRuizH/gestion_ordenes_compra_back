<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_GET['id'];

    $query = "CALL EliminarMaterial($id);";

    try {
        $resultSet = $mysql->query($query);

        if($resultSet){
            echo json_encode(["status"=>"success","message" => "Material eliminado exitosamente"]);
        } else {
            throw new Exception("Error al eliminar el material: " . $mysql->error);
        }
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
