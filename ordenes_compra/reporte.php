<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin = $_GET['fecha_fin'];

    try {
    
        $stmt = $mysql -> prepare("SELECT oc.id as orden_id, oc.folio, c.codigo, oc.orden_compra_c, oc.fecha_orden, oc.fecha_entrega, oc.total_pares, oc.facturaNo, oc.status  
            FROM ordenes_compra oc 
            INNER JOIN clientes c ON oc.cliente_id = c.id
            WHERE oc.fecha_orden BETWEEN ? AND ?
            ORDER BY oc.fecha_orden DESC, oc.folio DESC;");

        $stmt->bind_param("ss", $fechaInicio, $fechaFin);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if($resultado->num_rows > 0) {
            $itemRecords=array();
            $itemRecords["items"]=array();
            while ($item = $resultado->fetch_assoc()) {
                extract($item);
            
                $itemDetails=array(
                    "id" => $orden_id,
                    "folio" => $folio,
                    "codigo" => $codigo,
                    "orden_compra_c" => $orden_compra_c,
                    "fecha_orden" => $fecha_orden,
                    "fecha_entrega" => $fecha_entrega,
                    "total_pares" => $total_pares,
                    "facturaNo" => $facturaNo,
                    "status" => $status
                );
                array_push($itemRecords["items"], $itemDetails);
            }

            echo json_encode(["status"=>"success", $itemRecords]);

        } else {
            ["status"=>"error", "message" => "No hay órdenes de compra para las fechas especificadas."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status"=>"error","message" => $e->getMessage()])
    }
}
