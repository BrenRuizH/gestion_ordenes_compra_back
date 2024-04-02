<?php

require_once '../conexion.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['cliente_id'])) {
        $cliente_id = $_GET['cliente_id'];

        $query = "SELECT orden_compra_c FROM ordenes_compra WHERE cliente_id = ? ORDER BY fecha_orden DESC LIMIT 1";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orden = $result->fetch_assoc();

        if ($orden) {
            $orden_compra_c = $orden['orden_compra_c'];

            // Extrae el prefijo y el número de orden_compra_c
            $prefix = substr($orden_compra_c, 0, 3);
            $number = intval(substr($orden_compra_c, 3));

            // Incrementa el número en uno
            $number++;

            // Concatena el prefijo y el número para obtener el nuevo valor de orden_compra_c
            $new_orden_compra_c = $prefix . $number;
        } else {
            // Si es la primera orden del cliente, genera el orden_compra_c con las primeras 3 letras del código del cliente y el número 1
            $query = "SELECT codigo FROM clientes WHERE id = ?";
            $stmt = $mysql->prepare($query);
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cliente = $result->fetch_assoc();
            $codigo_cliente = $cliente['codigo'];

            $new_orden_compra_c = substr($codigo_cliente, 0, 3) . '1';
        }

        echo json_encode(["new_orden_compra_c" => $new_orden_compra_c]);
    } else {
        echo json_encode(["error" => "No se proporcionó el cliente_id"]);
    }
}
?>
