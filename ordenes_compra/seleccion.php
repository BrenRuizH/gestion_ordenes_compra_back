<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $id = $_GET['id'];

$query="SELECT o.id, o.fecha_orden, o.fecha_entrega, o.cliente_id, o.folio, o.orden_compra_c, o.horma_id, o.cambrillon_id, o.observaciones, o.matriz, o.total_pares,
d.material_id, d.punto, d.cantidad
FROM ordenes_compra o
JOIN detalles_orden_compra d
ON o.id = d.orden_compra_id
WHERE o.id = $id";

    $resultado=$mysql->query($query);
    if($resultado->num_rows > 0)
    {
        $itemRecords=array();
        $itemRecords["items"]=array();
        while ($item = $resultado->fetch_assoc())
        {
            extract($item);
            $itemDetails=array(
                "id" => $id,
                "fecha_entrega" => $fecha_entrega,
                "fecha_orden" => $fecha_orden,
                "folio" => $folio,
                "cliente_id" => $cliente_id,
                "horma_id" => $horma_id,
                "orden_compra_c" => $orden_compra_c,
                "observaciones" => $observaciones,
                "cambrillon_id" => $cambrillon_id,
                "total_pares" => $total_pares,
                "matriz" => $matriz,
                "material_id" => $material_id,
                "cantidad" => $cantidad,
                "punto" => $punto
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra con el ID correspondiente."));
    }
}