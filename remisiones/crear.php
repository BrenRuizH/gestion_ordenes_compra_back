<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $fecha = $_POST['fecha'];
    $cliente_id = $_POST['cliente_id'];
    $total_pares = $_POST['total_pares'];
    $precio_final = $_POST['precio_final'];
    $folio = $_POST['folio'];

    try {
        $mysql->begin_transaction();

        $stmt = $mysql->prepare("INSERT INTO remisiones (?, ?, ?, ?)");
        $stmt->bind_param("siid", $fecha, $cliente_id, $total_pares, $precio_final);
        if (!$stmt->execute()) {
            throw new Exception("Error al crear la remisión: " . $stmt->error);
        }

        $result = $stmt->get_result();

        $data = $result->fetch_assoc();
        $remision_id = $data['remision_id'];

        $result->free();

        $stmt->close();

        $stmt = $mysql->prepare("INSERT INTO remision_detalles(remision_id, folio) VALUES (?, ?)");
        $stmt->bind_param("is", $orden_compra_id, $folio);
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar detalle de remision: " . $stmt->error);
        }

        if ($mysql->commit()) {
            echo json_encode(["status"=>"success","message" => "Remisión creada exitosamente"]);
        } else {
            throw new Exception("Error al confirmar la transacción");
        }
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
