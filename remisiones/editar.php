<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $cliente_id = $_POST['cliente_id'];
    $total_pares = $_POST['total_pares'];
    $precio_final = $_POST['precio_final'];
    $extra = isset($_POST['extra']) && $_POST['extra'] !== ''
    ? (float) $_POST['extra']
    : 0;
$descripcion = $_POST['descripcion'] ?? '';
   $folios = isset($_POST['folios'])
    ? json_decode($_POST['folios'], true)
    : [];

$elementosAgregados = isset($_POST['elementosAgregados'])
    ? json_decode($_POST['elementosAgregados'], true)
    : [];

if (!is_array($folios)) {
    $folios = [];
}

if (!is_array($elementosAgregados)) {
    $elementosAgregados = [];
}

    try {
        $mysql->begin_transaction();

        // Llama al procedimiento almacenado para editar la remisión
        $stmt = $mysql->prepare("CALL EditarRemisionYDetalles(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiidds", $id, $fecha, $cliente_id, $total_pares, $precio_final, $extra, $descripcion);
        if (!$stmt->execute()) {
            throw new Exception("Error al modificar la remisión: " . $stmt->error);
        }

        // Eliminar los detalles existentes de la remisión
        $stmt = $mysql->prepare("DELETE FROM remision_detalles WHERE remision_id = ?");
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Error al eliminar detalles de remisión: " . $stmt->error);
        }

        // Inserta los detalles de remisión y actualiza el estado de las órdenes de compra
        foreach ($folios as $item) {
            $folio = $item['folio'];
            $oc = $item['oc'];
if (!isset($item['precio_unitario'])) {
    throw new Exception("Falta el precio unitario del folio: " . $item['folio']);
}

$precio_unitario = (float) $item['precio_unitario'];
            
            $stmt = $mysql->prepare("INSERT INTO remision_detalles (remision_id, folio, oc, precio) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issd", $id, $folio, $oc, $precio_unitario);
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar detalle de remisión: " . $stmt->error);
            }
            
            $stmt = $mysql->prepare("UPDATE ordenes_compra SET status = 'REMISIONADO', remision_id = ? WHERE folio = ?");
            $stmt->bind_param("is", $id, $folio);
            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar el status: " . $stmt->error);
            }
        }

        // Si el cliente_id es 36, inserta los detalles de horma
        if ($cliente_id == 36) {
            // Elimina los detalles anteriores para la remisión específica
            $stmt = $mysql->prepare("DELETE FROM remision_puntos_cantidades WHERE remision_id = ?");
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Error al eliminar detalles de horma anteriores: " . $stmt->error);
            }

            // Inserta los nuevos detalles
       
// Inserta los nuevos detalles
foreach ($elementosAgregados as $elemento) {
    $horma_id = $elemento['horma_id'];
    $oc = $elemento['oc'];

    // Recuperar el precio unitario histórico de Angular.
if (!isset($elemento['precio_unitario'])) {
    throw new Exception("Falta el precio unitario de la horma: " . $elemento['horma_id']);
}

$precio_unitario = (float) $elemento['precio_unitario'];

    foreach ($elemento['puntos'] as $puntoCantidad) {
        $punto = $puntoCantidad['punto'];
        $cantidad = $puntoCantidad['cantidad'];

        $stmt = $mysql->prepare("
            INSERT INTO remision_puntos_cantidades
            (remision_id, horma_id, punto, cantidad, oc, precio)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iidisd",
            $id,
            $horma_id,
            $punto,
            $cantidad,
            $oc,
            $precio_unitario
        );

        if (!$stmt->execute()) {
            throw new Exception(
                "Error al insertar detalle de horma: " . $stmt->error
            );
        }
    }
}
}
        $mysql->commit();

        echo json_encode(["status" => "success", "message" => "Remisión editada exitosamente"]);
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
