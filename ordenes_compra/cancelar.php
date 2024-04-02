<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_GET['id'];

    $query = "CALL CancelarOrdenCompraYDetalle($id);";

    try {
        $resultSet = $mysql->query($query);

        if($resultSet){
            echo json_encode(["status"=>"success","message" => "Orden de compra cancelada exitosamente"]);
        } else {
            throw new Exception("Error al eliminar la orden de compra: " . $mysql->error);
        }
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
