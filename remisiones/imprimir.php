<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $itemRecords = array();
    $itemRecords["cliente"] = array();
    $itemRecords["remision"] = array();
    $itemRecords["orden_compra"] = array();
    $itemRecords["horma"] = array();
    $itemRecords["detalles_orden_compra"] = array();

    $query = "SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
                     r.id AS remision, rd.folio,
                     oc.id AS orden_compra, oc.total_pares,
                     h.id AS horma, h.nombre, h.precio,
                     doc.punto, doc.cantidad
              FROM remisiones r
              INNER JOIN remision_detalles rd ON rd.remision_id = r.id
              INNER JOIN clientes c ON r.cliente_id = c.id
              INNER JOIN ordenes_compra oc ON rd.folio = oc.folio
              INNER JOIN detalles_orden_compra doc ON doc.orden_compra_id = oc.id
              INNER JOIN hormas h ON h.cliente_id = c.id
              WHERE r.id = $remision_id;";

    $resultado = $mysql->query($query);

    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            extract($row);
            
            $clienteDetails = array(
                "id" => $cliente,
                "razonSocial" => $razonSocial,
                "direccion" => $direccion,
                "telefono" => $telefono
            );

            $remisionDetails = array(
                "id" => $remision,
                "folio" => $folio
            );

            $ordenCompraDetails = array(
                "id" => $orden_compra,
                "total_pares" => $total_pares
            );

            $hormaDetails = array(
                "id" => $horma,
                "nombre" => $nombre,
                "precio" => $precio
            );

            $detallesOrdenCompraDetails = array(
                "punto" => $punto,
                "cantidad" => $cantidad
            );

            if (empty($itemRecords["cliente"])) {
                array_push($itemRecords["cliente"], $clienteDetails);
            }

            if (empty($itemRecords["remision"])) {
                array_push($itemRecords["remision"], $remisionDetails);
            }

            if (empty($itemRecords["orden_compra"])) {
                array_push($itemRecords["orden_compra"], $ordenCompraDetails);
            }

            if (empty($itemRecords["horma"])) {
                array_push($itemRecords["horma"], $hormaDetails);
            }

            array_push($itemRecords["detalles_orden_compra"], $detallesOrdenCompraDetails);
        }

        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}

?>
