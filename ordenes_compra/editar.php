<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $fecha_orden = $_POST['fecha_orden'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $cliente_id = $_POST['cliente_id'];
    $folio = $_POST['folio'];
    $orden_compra_c = $_POST['orden_compra_c'];
    $horma_id = $_POST['horma_id'];
    $total_pares = $_POST['total_pares'];
    $punto = array_map('floatval', explode(',', $_POST['punto']));
    $cantidad = array_map('intval', explode(',', $_POST['cantidad']));

    try {
        $mysql->begin_transaction();

        $stmt = $mysql->prepare("CALL EditarOrdenCompraYDetalle(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississii", $id, $fecha_orden, $fecha_entrega, $cliente_id, $folio, $orden_compra_c, $horma_id, $total_pares);
        if (!$stmt->execute()) {
            throw new Exception("Error al modificar orden de compra: " . $stmt->error);
        }

        $stmt = $mysql->prepare("INSERT INTO detalles_orden_compra(orden_compra_id, punto, cantidad) VALUES (?, ?, ?)");

        for ($i = 0; $i < count($punto); $i++) {
            if ($punto[$i] && $cantidad[$i]) {
                $stmt->bind_param("idi", $id, $punto[$i], $cantidad[$i]);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar detalle de orden de compra: " . $stmt->error);
                }
            }
        }
        
        $mysql->commit();

        echo json_encode(["status"=>"success","message" => "Orden de compra editada exitosamente"]);
    } catch (Exception $e) {
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
