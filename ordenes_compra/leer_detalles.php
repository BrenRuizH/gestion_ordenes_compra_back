<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $orden_id = $_GET['orden_id'];

    $itemRecords=array();
    $itemRecords["items1"]=array();
    $itemRecords["items2"]=array();

    $query1="SELECT oc.id, oc.fecha_orden, oc.fecha_entrega,
                oc.cliente_id, c.razonSocial, c.telefono, c.direccion, c.codigo AS cliente, oc.folio, 
                oc.orden_compra_c, oc.horma_id, h.nombre AS horma, h.matriz AS matriz,
                h.cambrillon AS cambrillon, h.precio, h.materiales AS materiales, 
                h.observaciones AS observaciones, oc.total_pares
             FROM ordenes_compra oc 
             INNER JOIN clientes c ON oc.cliente_id = c.id
             INNER JOIN hormas h ON oc.horma_id = h.id
             WHERE oc.id = $orden_id;";

    $resultado1=$mysql->query($query1);

    if($resultado1->num_rows > 0) {
        while ($item1 = $resultado1->fetch_assoc()) {
            extract($item1);
            $itemDetails1=array(
                "id" => $id,
                "folio" => $folio,
                "remision" => $remision,
                "cliente_id" => $cliente_id,
                "cliente" => $cliente,
                "razonSocial" => $razonSocial,
                "telefono" => $telefono,
                "direccion" => $direccion,
                "orden_compra_c" => $orden_compra_c,
                "fecha_orden" => $fecha_orden,
                "fecha_entrega" => $fecha_entrega,
                "horma_id" => $horma_id,
                "horma" => $horma,
                "matriz" => $matriz,
                "cambrillon" => $cambrillon,
                "materiales" => $materiales,
                "observaciones" => $observaciones,
                "precio" => $precio,
                "total_pares" => $total_pares,
            );
            array_push($itemRecords["items1"], $itemDetails1);
        }
    }

    $query2="SELECT id, punto, cantidad 
             FROM detalles_orden_compra
             WHERE orden_compra_id = $orden_id;";

    $resultado2=$mysql->query($query2);

    if($resultado2->num_rows > 0) {
        while ($item2 = $resultado2->fetch_assoc()) {
            extract($item2);
            $itemDetails2=array(
                "id" => $id,
                "punto" => $punto,
                "cantidad" => $cantidad
            );
            array_push($itemRecords["items2"], $itemDetails2);
        }
    }

    if(empty($itemRecords["items1"])) {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    } else {
        http_response_code(200);
        echo json_encode($itemRecords);
    }
}
?>
