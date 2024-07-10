<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../conexion.php'; // Asegúrate de que este archivo esté incluido correctamente
    include '../config.php'; // Asegúrate de que este archivo esté incluido correctamente

    $fecha = $_POST['fecha'];
    $cliente_id = $_POST['cliente_id'];
    $total_pares = $_POST['total_pares'];
    $precio_final = $_POST['precio_final'];
    $folio = $_POST['folio'];

    try {
        $mysql->begin_transaction();

        // Corrección: Agrega los nombres de las columnas en la consulta
        $stmt = $mysql->prepare("INSERT INTO remisiones (fecha, cliente_id, total_pares, precio_final) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siid", $fecha, $cliente_id, $total_pares, $precio_final);
        if (!$stmt->execute()) {
            throw new Exception("Error al crear la remisión: " . $stmt->error);
        }

        // Obtiene el ID de la remisión insertada
        $remision_id = $stmt->insert_id;

        // Inserta los folios en la tabla de detalles de remisión
        $foliosArray = explode(',', $folio);
        foreach ($foliosArray as $fol) {
            $stmt = $mysql->prepare("INSERT INTO remision_detalles (remision_id, folio) VALUES (?, ?)");
            $stmt->bind_param("is", $remision_id, trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar detalle de remisión: " . $stmt->error);
            }
            $stmt= $mysql ->prepare ("UPDATE ordenes_compra set status = 'REMISIONADO' where folio = 0");
            $stmt->bind_param("s",trim($fol));
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el status: " . $stmt->error);
            }
        }

       

        // Realiza el commit solo si todo fue exitoso
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
