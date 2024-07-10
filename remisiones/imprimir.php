<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $response = array();

    $query = "SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
                     r.id AS remision,
                     oc.id AS orden_compra, oc.total_pares,
                     h.id AS horma_id, h.nombre AS nombre_horma, h.precio AS precio_horma,
                     doc.punto, doc.cantidad
              FROM remisiones r
              INNER JOIN remision_detalles rd ON rd.remision_id = r.id
              INNER JOIN clientes c ON r.cliente_id = c.id
              INNER JOIN ordenes_compra oc ON rd.folio = oc.folio
              INNER JOIN detalles_orden_compra doc ON doc.orden_compra_id = oc.id
              INNER JOIN hormas h ON h.id = oc.horma_id
              WHERE r.id = $remision_id;";

    $resultado = $mysql->query($query);

    if ($resultado->num_rows > 0) {
        $response['cliente'] = [];
        $response['remision'] = [];
        $response['orden_compra'] = [];

        while ($row = $resultado->fetch_assoc()) {
            // Datos del cliente (solo se agrega una vez)
            if (empty($response['cliente'])) {
                $response['cliente'][] = [
                    'id' => $row['cliente'],
                    'razonSocial' => $row['razonSocial'],
                    'direccion' => $row['direccion'],
                    'telefono' => $row['telefono']
                ];
            }

            // Datos de la remisión (solo se agrega una vez)
            if (empty($response['remision'])) {
                $response['remision'][] = [
                    'id' => $row['remision']
                ];
            }

            $orden_compra_id = $row['orden_compra'];
            if (!isset($response['orden_compra'][$orden_compra_id])) {
                // Datos de la orden de compra
                $response['orden_compra'][$orden_compra_id] = [
                    'id' => $orden_compra_id,
                    'total_pares' => $row['total_pares'],
                    'horma' => [
                        'id' => $row['horma_id'],
                        'nombre' => $row['nombre_horma'],
                        'precio' => $row['precio_horma']
                    ],
                    'detalles' => []
                ];
            }

            // Detalles de la orden de compra (se agrupan únicamente una vez)
            $detalle_existente = false;
            foreach ($response['orden_compra'][$orden_compra_id]['detalles'] as &$detalle) {
                if ($detalle['punto'] == $row['punto'] && $detalle['cantidad'] == $row['cantidad']) {
                    $detalle_existente = true;
                    break;
                }
            }
            
            if (!$detalle_existente) {
                $response['orden_compra'][$orden_compra_id]['detalles'][] = [
                    'punto' => $row['punto'],
                    'cantidad' => $row['cantidad']
                ];
            }
        }

        // Reindexar el array de orden_compra para resetear los índices
        $response['orden_compra'] = array_values($response['orden_compra']);

        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}
?>
