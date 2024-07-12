<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $response = array();

    $query_cliente_36 = "
        SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
               r.id AS remision, 
               h.id AS horma_id, h.nombre AS nombre_horma, h.precio AS precio_horma,
               rpc.punto, rpc.cantidad, 
               total_suma.total_pares
        FROM remisiones r
        INNER JOIN clientes c ON r.cliente_id = c.id
        INNER JOIN remision_puntos_cantidades rpc ON r.id = rpc.remision_id
        INNER JOIN hormas h ON h.id = rpc.horma_id
        INNER JOIN (
          SELECT remision_id, SUM(cantidad) AS total_pares
          FROM remision_puntos_cantidades
          WHERE remision_id = ?
          GROUP BY remision_id
        ) AS total_suma ON r.id = total_suma.remision_id
        WHERE r.id = ?";

    $query_otros_clientes = "
        SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
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
              WHERE r.id = ?";

    $cliente_query = "SELECT cliente_id FROM remisiones WHERE id = ?";
    $stmt = $mysql->prepare($cliente_query);
    $stmt->bind_param("i", $remision_id);
    $stmt->execute();
    $cliente_result = $stmt->get_result();
    $cliente_data = $cliente_result->fetch_assoc();
    $cliente_id = $cliente_data['cliente_id'];

    if ($cliente_id == 36) {
        $stmt = $mysql->prepare($query_cliente_36);
        $stmt->bind_param("ii", $remision_id, $remision_id);
    } else {
        $stmt = $mysql->prepare($query_otros_clientes);
        $stmt->bind_param("i", $remision_id);
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $response['cliente'] = [];
        $response['remision'] = [];
        $response['orden_compra'] = [];

        while ($row = $resultado->fetch_assoc()) {
            if (empty($response['cliente'])) {
                $response['cliente'][] = [
                    'id' => $row['cliente'],
                    'razonSocial' => $row['razonSocial'],
                    'direccion' => $row['direccion'],
                    'telefono' => $row['telefono']
                ];
            }

            if (empty($response['remision'])) {
                $response['remision'][] = [
                    'id' => $row['remision']
                ];
            }

            $orden_compra_id = $row['remision'];
            if (!isset($response['orden_compra'][$orden_compra_id])) {
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

        $response['orden_compra'] = array_values($response['orden_compra']);

        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}
?>
