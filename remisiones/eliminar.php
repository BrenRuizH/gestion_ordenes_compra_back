<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_GET['id'];

    $query = "CALL EliminarRemisionYDetalle($id);";

    try {
        $resultSet = $mysql->query($query);

        if($resultSet){
            echo json_encode(["status"=>"success","message" => "Remisión eliminada exitosamente"]);
        } else {
            throw new Exception("Error al eliminar la remisión: " . $mysql->error);
        }
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
