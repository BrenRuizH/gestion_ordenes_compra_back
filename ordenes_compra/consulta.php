<?php

require_once '../conexion.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['cliente_id'])) {
        $cliente_id = $_GET['cliente_id'];

        $query = "SELECT COUNT(*) as num_ordenes FROM ordenes_compra WHERE cliente_id = ?";
        $stmt = $mysql->prepare($query);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ordenes = $result->fetch_assoc();
        $num_ordenes = $ordenes['num_ordenes'];

        echo json_encode(["num_ordenes" => $num_ordenes]);
    } else {
        echo json_encode(["error" => "No se proporcionó el cliente_id"]);
    }
}
