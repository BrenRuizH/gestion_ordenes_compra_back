<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $response = array();

    $query = "SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
                     r.id AS remision,
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
        $response['cliente'] = [];
        $response['remision'] = [];
        $response['orden_compra'] = [];

        while ($row = $resultado->fetch_assoc()) {
            // Datos del cliente
            if (empty($response['cliente'])) {
                $response['cliente'][] = [
                    'id' => $row['cliente'],
                    'razonSocial' => $row['razonSocial'],
                    'direccion' => $row['direccion'],
                    'telefono' => $row['telefono']
                ];
            }

            // Datos de la remisión
            if (empty($response['remision'])) {
                $response['remision'][] = [
                    'id' => $row['remision']
                ];
            }

            // Datos de las órdenes de compra
            $orden_compra_id = $row['orden_compra'];
            if (!isset($response['orden_compra'][$orden_compra_id])) {
                $response['orden_compra'][$orden_compra_id] = [
                    'id' => $orden_compra_id,
                    'total_pares' => $row['total_pares'],
                    'horma' => [
                        'id' => $row['horma'],
                        'nombre' => $row['nombre'],
                        'precio' => $row['precio']
                    ],
                    'detalles' => []
                ];
            }

            // Datos de los detalles de la orden de compra
            $response['orden_compra'][$orden_compra_id]['detalles'][] = [
                'punto' => $row['punto'],
                'cantidad' => $row['cantidad']
            ];
        }

        $response['orden_compra'] = array_values($response['orden_compra']); // Para resetear los índices del array

        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}
?>
