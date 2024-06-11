<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $status = $_POST['status'];

    try {
      $mysql->begin_transaction();

      $stmt = $mysql->prepare("UPDATE ordenes_compra SET status = '$status' WHERE id = $id;");
      $stmt->bind_param("si", $status, $id);
      if (!$stmt->execute()) {
        throw new Exception("Error al actualizar el status de la orden: ".$stmt->error);
      }
    
        $mysql->commit();

        echo json_encode(["status"=>"success","message" => "Status de la orden actualizado existosamente"]);
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
