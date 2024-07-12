<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../conexion.php';
    include '../config.php';

    $fecha = $_POST['fecha'];
    $cliente_id = $_POST['cliente_id'];
    $total_pares = $_POST['total_pares'];
    $precio_final = $_POST['precio_final'];
    $extra = $_POST['extra'];
    $descripcion = $_POST['descripcion'];
    $oc = $_POST['oc'];
    $folio = $_POST['folio'];

    $elementosAgregados = isset($_POST['elementosAgregados']) ? json_decode($_POST['elementosAgregados'], true) : [];

    try {
        $mysql->begin_transaction();

        $stmt = $mysql->prepare("INSERT INTO remisiones (fecha, cliente_id, total_pares, precio_final, extra, descripcion, oc) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiddss", $fecha, $cliente_id, $total_pares, $precio_final, $extra, $descripcion, $oc);
        if (!$stmt->execute()) {
            throw new Exception("Error al crear la remisión: " . $stmt->error);
        }

        $remision_id = $stmt->insert_id;

        $foliosArray = explode(',', $folio);
        foreach ($foliosArray as $fol) {
            $stmt = $mysql->prepare("INSERT INTO remision_detalles (remision_id, folio) VALUES (?, ?)");
            $stmt->bind_param("is", $remision_id, trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar detalle de remisión: " . $stmt->error);
            }
            $stmt= $mysql ->prepare ("UPDATE ordenes_compra SET status = 'REMISIONADO' WHERE folio = ?");
            $stmt->bind_param("s",trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el status: " . $stmt->error);
            }
        }

        if ($cliente_id == 36) {
            foreach ($elementosAgregados as $elemento) {
                $horma_id = $elemento['horma_id'];
                foreach ($elemento['puntosYcantidades'] as $puntoCantidad) {
                    $punto = $puntoCantidad['punto'];
                    $cantidad = $puntoCantidad['cantidad'];
                    $stmt = $mysql->prepare("INSERT INTO remision_puntos_cantidades (remision_id, horma_id, punto, cantidad) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $remision_id, $horma_id, $punto, $cantidad);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar detalle de horma: " . $stmt->error);
                    }
                }
            }
        }

        if ($mysql->commit()) {
            echo json_encode(["status" => "success", "message" => "Remisión creada exitosamente"]);
        } else {
            throw new Exception("Error al confirmar la transacción");
        }
    } catch (Exception $e) {
        $mysql->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
