<?php

require_once '../conexion.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['cliente_id'])) {
        $cliente_id = $_GET['cliente_id'];

        $query = "SELECT c.acronimo, orden_compra_c 
                  FROM ordenes_compra 
                  INNER JOIN clientes c
                  WHERE cliente_id = ? 
                  ORDER BY fecha_orden DESC LIMIT 1";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $orden = $result->fetch_assoc();

        if ($orden) {
            $orden_compra_c = $orden['orden_compra_c'];

            $prefix = $cliente['acronimo'];
            $number = intval(substr($orden_compra_c, 3));

            $number++;

            $new_orden_compra_c = $prefix . $number;
        } else {
            $query = "SELECT acronimo FROM clientes WHERE id = ?";
            $stmt = $mysql->prepare($query);
            $stmt->bind_param("i", $cliente_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cliente = $result->fetch_assoc();
            $acronimo_cliente = $cliente['acronimo'];

            $new_orden_compra_c = $acronimo_cliente . '1';
        }

        echo json_encode(["new_orden_compra_c" => $new_orden_compra_c]);
    } else {
        echo json_encode(["error" => "No se proporcionó el cliente_id"]);
    }
}
?>
