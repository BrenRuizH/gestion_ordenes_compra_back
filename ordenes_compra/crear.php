<?php

setlocale(LC_ALL, 'en_US.UTF-8');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $fecha_orden = $_POST['fecha_orden'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $cliente_id = $_POST['cliente_id'];
    $folio = $_POST['folio'];
    $orden_compra_c = $_POST['orden_compra_c'];
    $horma_id = $_POST['horma_id'];
    $cambrillon_id = isset($_POST['cambrillon_id']) ? $_POST['cambrillon_id'] : NULL;
    $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : NULL;
    $matriz = isset($_POST['matriz']) ? $_POST['matriz'] : NULL;
    $total_pares = $_POST['total_pares'];
    $material_id = explode(',', $_POST['material_id']);
    $punto = array_map('floatval', explode(',', $_POST['punto']));
    $cantidad = array_map('intval', explode(',', $_POST['cantidad']));

    try {
        // Inicia la transacción
        $mysql->begin_transaction();

        // Inserta la orden de compra
        $stmt = $mysql->prepare("CALL CrearOrdenCompra(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissiissi", $fecha_orden, $fecha_entrega, $cliente_id, $folio, $orden_compra_c, $horma_id, $cambrillon_id, $observaciones, $matriz, $total_pares);
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar orden de compra: " . $stmt->error);
        }

        // Obtiene el ID de la orden de compra insertada
        $result = $stmt->get_result(); // obtiene el resultado
        $data = $result->fetch_assoc(); // obtiene la fila de datos
        $orden_compra_id = $data['orden_compra_id']; // obtiene el valor de orden_compra_id

        // Libera el resultado
        $result->free();

        // Cierra la sentencia
        $stmt->close();

        // Prepara la consulta para insertar los detalles de la orden de compra
        $stmt = $mysql->prepare("INSERT INTO detalles_orden_compra(orden_compra_id, punto, cantidad) VALUES (?, ?, ?)");

        // Inserta cada detalle de la orden de compra
        for ($i = 0; $i < count($punto); $i++) {
            if ($punto[$i] && $cantidad[$i]) {
                $stmt->bind_param("idi", $orden_compra_id, $punto[$i], $cantidad[$i]);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar detalle de orden de compra: " . $stmt->error);
                }
            }
        }

        // Prepara la consulta para insertar los detalles de la orden de compra
        $stmt = $mysql->prepare("INSERT INTO materiales_orden_compra(orden_compra_id, material_id) VALUES (?, ?)");

        // Inserta cada material de la orden de compra
        if (is_array($material_id)) {
            foreach ($material_id as $mid) {
                $stmt->bind_param("ii", $orden_compra_id, $mid);
                if (!$stmt->execute()) {
                    throw new Exception("Error al insertar material de orden de compra: " . $stmt->error);
                }
            }
        } else {
            throw new Exception("El material no es un array");
        }
        
        // Si todas las operaciones fueron exitosas, confirma la transacción
        $mysql->commit();

        echo json_encode(["status"=>"success","message" => "Orden de compra creada exitosamente"]);
    } catch (Exception $e) {
        // Si ocurrió un error, revierte la transacción
        $mysql->rollback();

        echo json_encode(["status"=>"error","message" => $e->getMessage()]);
    }
}
