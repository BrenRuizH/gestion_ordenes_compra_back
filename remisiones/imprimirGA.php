<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    require_once '../conexion.php';
    include '../config.php';

    $remision_id = $_GET['remision_id'];

    $response = array();

    $query = "
        SELECT c.razonSocial, c.direccion, c.telefono, c.id AS cliente,
               r.id AS remision, r.extra, r.descripcion, r.oc,
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
        WHERE r.id = ?;";
    
    $stmt = $mysql->prepare($query);
    $stmt->bind_param("ii", $remision_id, $remision_id);
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
                    'id' => $row['remision'],
                    'extra' => $row['extra'],
                    'descripcion' => $row['descripcion'],
                    'oc' => $row['oc']
                ];
            }

            // Crear o actualizar detalle de orden de compra por cada horma
            $horma_id = $row['horma_id'];
            if (!isset($response['orden_compra'][$horma_id])) {
                $response['orden_compra'][$horma_id] = [
                    'id' => $horma_id,
                    'horma' => [
                        'id' => $row['horma_id'],
                        'nombre' => $row['nombre_horma'],
                        'precio' => $row['precio_horma']
                    ],
                    'detalles' => [],
                    'total_pares' => $row['total_pares']
                ];
            }

            // Agregar detalle de orden de compra
            $response['orden_compra'][$horma_id]['detalles'][] = [
                'punto' => $row['punto'],
                'cantidad' => $row['cantidad']
            ];
        }

        $response['cliente'] = array_values($response['cliente']);
        $response['remision'] = array_values($response['remision']);
        $response['orden_compra'] = array_values($response['orden_compra']);

        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}
?>
