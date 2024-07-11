<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $cliente_id = $_POST['cliente_id'];
    $total_pares = $_POST['total_pares'];
    $precio_final = $_POST['precio_final'];
    $folio = $_POST['folio'];

    try {
        $mysql->begin_transaction();

        $stmt = $mysql->prepare("CALL EditarRemisionYDetalles(?, ?, ?, ?, ?)");
        $stmt->bind_param("isiid", $id, $fecha, $cliente_id, $total_pares, $precio_final);
        if (!$stmt->execute()) {
            throw new Exception("Error al modificar la remisión: " . $stmt->error);
        }

       $foliosArray = explode(',', $folio);
        foreach ($foliosArray as $fol) {
            $stmt = $mysql->prepare("INSERT INTO remision_detalles (remision_id, folio) VALUES (?, ?)");
            $stmt->bind_param("is", $id, trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar detalle de remisión: " . $stmt->error);
            }
            
            $stmt= $mysql ->prepare ("UPDATE ordenes_compra SET status = 'REMISIONADO' WHERE folio = ?");
            $stmt->bind_param("s",trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el status: " . $stmt->error);
            }
        }
        
        $mysql->commit();

        echo json_encode(["status"=>"success","message" => "Remisión editada exitosamente"]);
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
