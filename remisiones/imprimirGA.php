<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $response = array();

    // Consultar el cliente_id de la remisión
    $query_cliente_id = "SELECT cliente_id FROM remisiones WHERE id = ?";
    $stmt_cliente_id = $mysql->prepare($query_cliente_id);
    $stmt_cliente_id->bind_param("i", $remision_id);
    $stmt_cliente_id->execute();
    $resultado_cliente_id = $stmt_cliente_id->get_result();

    if ($resultado_cliente_id->num_rows > 0) {
        $cliente_id_row = $resultado_cliente_id->fetch_assoc();
        $cliente_id = $cliente_id_row['cliente_id'];

        // Verificar si el cliente_id es 36
        if ($cliente_id == 36) {
            // Primer servicio para cliente_id 36
            $query = "
                SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
                       r.id AS remision, r.extra, r.descripcion, r.oc,
                       h.id AS horma_id, h.nombre AS nombre_horma, h.precio AS precio_horma,
                       rpc.punto, rpc.cantidad
                FROM remisiones r
                INNER JOIN clientes c ON r.cliente_id = c.id
                INNER JOIN remision_puntos_cantidades rpc ON r.id = rpc.remision_id
                INNER JOIN hormas h ON h.id = rpc.horma_id
                WHERE r.id = ?;";
            
            $stmt = $mysql->prepare($query);
            $stmt->bind_param("i", $remision_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                $response['cliente'] = [];
                $response['remision'] = [];
                $response['orden_compra'] = [];

                while ($row = $resultado->fetch_assoc()) {
                    // Añadir datos del cliente si aún no están añadidos
                    if (empty($response['cliente'])) {
                        $response['cliente'][] = [
                            'id' => $row['cliente'],
                            'razonSocial' => $row['razonSocial'],
                            'direccion' => $row['direccion'],
                            'telefono' => $row['telefono']
                        ];
                    }

                    // Añadir datos de la remisión si aún no están añadidos
                    if (empty($response['remision'])) {
                        $response['remision'][] = [
                            'id' => $row['remision'],
                            'extra' => $row['extra'],
                            'descripcion' => $row['descripcion'],
                            'oc' => $row['oc']
                        ];
                    }

                    // Crear o actualizar detalle de orden de compra por cada horma_id
                    $horma_id = $row['horma_id'];
                    if (!isset($response['orden_compra'][$horma_id])) {
                        $response['orden_compra'][$horma_id] = [
                            'id' => $horma_id,
                            'total_pares' => 0,
                            'horma' => [
                                'id' => $row['horma_id'],
                                'nombre' => $row['nombre_horma'],
                                'precio' => $row['precio_horma']
                            ],
                            'detalles' => []
                        ];
                    }

                    // Añadir detalle de orden de compra a la horma correspondiente
                    $cantidad = $row['cantidad'];
                    $response['orden_compra'][$horma_id]['detalles'][] = [
                        'punto' => $row['punto'],
                        'cantidad' => $cantidad
                    ];

                    // Sumar cantidad al total_pares de la horma correspondiente
                    $response['orden_compra'][$horma_id]['total_pares'] += $cantidad;
                }

                // Convertir arrays a valores para asegurar la estructura correcta en JSON
                $response['cliente'] = array_values($response['cliente']);
                $response['remision'] = array_values($response['remision']);
                $response['orden_compra'] = array_values($response['orden_compra']);

                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No se encontraron datos."));
            }
        } else {
            // Segundo servicio para todos los demás clientes
            $query = "
                SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
                       r.id AS remision, r.extra, r.descripcion,
                       oc.id AS orden_compra, oc.total_pares,
                       h.id AS horma_id, h.nombre AS nombre_horma, h.precio AS precio_horma,
                       doc.punto, doc.cantidad
                FROM remisiones r
                INNER JOIN remision_detalles rd ON rd.remision_id = r.id
                INNER JOIN clientes c ON r.cliente_id = c.id
                INNER JOIN ordenes_compra oc ON rd.folio = oc.folio
                INNER JOIN detalles_orden_compra doc ON doc.orden_compra_id = oc.id
                INNER JOIN hormas h ON h.id = oc.horma_id
                WHERE r.id = ?;";
            
            $stmt = $mysql->prepare($query);
            $stmt->bind_param("i", $remision_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

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
                            'id' => $row['remision'],
                            'extra' => $row['extra'],
                            'descripcion' => $row['descripcion']
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
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontró la remisión."));
    }
}
?>

