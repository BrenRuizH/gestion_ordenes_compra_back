<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin = $_GET['fecha_fin'];
    $cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : null;
    
    // Construir la consulta
    $query = "SELECT oc.id as orden_id, oc.folio, c.codigo, oc.orden_compra_c, oc.fecha_orden, oc.fecha_entrega, oc.total_pares, oc.facturaNo, oc.remision_id, oc.status
              FROM ordenes_compra oc 
              INNER JOIN clientes c ON oc.cliente_id = c.id
              WHERE oc.fecha_orden BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    // Agregar la condición de cliente_id si se proporciona
    if ($cliente_id !== null) {
        $query .= " AND oc.cliente_id = '$cliente_id'";
    }

    $query .= " ORDER BY oc.fecha_orden DESC, oc.folio DESC";

    $resultado = $mysql->query($query);

    if ($resultado->num_rows > 0) {
        $itemRecords = array();
        $itemRecords["items"] = array();
        while ($item = $resultado->fetch_assoc()) {
            $itemDetails = array(
                "id" => $item["orden_id"],
                "folio" => $item["folio"],
                "codigo" => $item["codigo"],
                "orden_compra_c" => $item["orden_compra_c"],
                "fecha_orden" => $item["fecha_orden"],
                "fecha_entrega" => $item["fecha_entrega"],
                "total_pares" => $item["total_pares"],
                "facturaNo" => $item["facturaNo"],
                "status" => $item["status"],
                "remision_id" => $item["remision_id"]
            );
            array_push($itemRecords["items"], $itemDetails);
        }

        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }
}
?>
