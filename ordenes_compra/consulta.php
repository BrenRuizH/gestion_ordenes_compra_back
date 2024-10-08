<?php

require_once '../conexion.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['cliente_id'])) {
        $cliente_id = $_GET['cliente_id'];

        $query = "SELECT c.acronimo, o.orden_compra_c 
                  FROM ordenes_compra o
                  INNER JOIN clientes c ON o.cliente_id = c.id
                  WHERE o.cliente_id = ? 
                  ORDER BY o.fecha_orden DESC LIMIT 1;";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orden = $result->fetch_assoc();

        $query = "SELECT COUNT(*) as count FROM ordenes_compra WHERE cliente_id = ?";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];

        if ($orden) {
            $orden_compra_c = $orden['orden_compra_c'];

            $prefix = $orden['acronimo'];
            $number = intval(substr($orden_compra_c, strlen($prefix)));

            $new_orden_compra_c = $prefix . ($count + 1);
        } else {
            $query = "SELECT acronimo FROM clientes WHERE id = ?";
            $stmt = $mysql->prepare($query);
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cliente = $result->fetch_assoc();
            $acronimo_cliente = $cliente['acronimo'];

            $new_orden_compra_c = $acronimo_cliente . ($count + 1);
        }

        echo json_encode(["new_orden_compra_c" => $new_orden_compra_c]);
    } else {
        echo json_encode(["error" => "No se proporcionó el cliente_id"]);
    }
}
?>
