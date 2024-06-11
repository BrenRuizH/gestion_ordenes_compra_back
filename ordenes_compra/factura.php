<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $facturaNo = $_POST['facturaNo'];

    try {
      $mysql->begin_transaction();

      $stmt = $mysql->prepare("UPDATE ordenes_compra SET facturaNo = ? WHERE id = ?;");
      $stmt->bind_param("si", $facturaNo, $id);
      if (!$stmt->execute()) {
        throw new Exception("Error al actualizar el número de factura de la orden: ".$stmt->error);
      }
    
        $mysql->commit();

        echo json_encode(["status"=>"success","message" => "Número de factura de la orden actualizado existosamente"]);
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
