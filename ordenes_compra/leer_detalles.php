<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $orden_id = $_GET['orden_id'];

    $itemRecords=array();
    $itemRecords["items1"]=array();
    $itemRecords["items2"]=array();
    $itemRecords["items3"]=array();

    $query1="SELECT 
    oc.id, 
    oc.fecha_orden, 
    oc.fecha_entrega, 
    oc.cliente_id,
    c.codigo AS cliente, 
    oc.folio, 
    oc.orden_compra_c, 
    oc.horma_id,
    h.nombre AS horma,
    oc.cambrillon_id,
    cam.nombre AS cambrillon,
    oc.observaciones, 
    oc.matriz, 
    oc.total_pares
FROM 
    ordenes_compra oc 
INNER JOIN 
    clientes c ON oc.cliente_id = c.id
INNER JOIN 
    hormas h ON oc.horma_id = h.id
INNER JOIN 
    cambrillones cam ON oc.cambrillon_id = cam.id
WHERE 
    oc.id = $orden_id;";
    $resultado1=$mysql->query($query1);
    if($resultado1->num_rows > 0)
    {
        while ($item1 = $resultado1->fetch_assoc())
        {
            extract($item1);
            $itemDetails1=array(
                "id" => $id,
                "folio" => $folio,
                "cliente_id" => $cliente_id,
                "cliente" => $cliente,
                "orden_compra_c" => $orden_compra_c,
                "fecha_orden" => $fecha_orden,
                "fecha_entrega" => $fecha_entrega,
                "horma_id" => $horma_id,
                "horma" => $horma,
                "cambrillon_id" => $cambrillon_id,
                "cambrillon" => $cambrillon,
                "observaciones" => $observaciones, 
                "matriz" => $matriz,
                "total_pares" => $total_pares,
            );
            array_push($itemRecords["items1"], $itemDetails1);
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }

    $query2="SELECT id, punto, cantidad FROM 
    detalles_orden_compra
    WHERE 
    orden_compra_id = $orden_id;";
    $resultado2=$mysql->query($query2);
    if($resultado2->num_rows > 0)
    {
        while ($item2 = $resultado2->fetch_assoc())
        {
            extract($item2);
            $itemDetails2=array(
                "id" => $id,
                "punto" => $punto,
                "cantidad" => $cantidad
            );
            array_push($itemRecords["items2"], $itemDetails2);
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }

    $query3="SELECT moc.id, m.nombre AS material FROM 
    materiales_orden_compra moc
    INNER JOIN 
    materiales m ON moc.material_id = m.id
    WHERE 
    moc.orden_compra_id = $orden_id;";
    $resultado3=$mysql->query($query3);
    if($resultado3->num_rows > 0)
    {
        while ($item3 = $resultado3->fetch_assoc())
        {
            extract($item3);
            $itemDetails3=array(
                "id" => $id,
                "material" => $material
            );
            array_push($itemRecords["items3"], $itemDetails3);
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }

    http_response_code(200);
    echo json_encode($itemRecords);
}
?>
