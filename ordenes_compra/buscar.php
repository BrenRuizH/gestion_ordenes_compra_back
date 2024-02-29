<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $codigo_cliente = $_GET['codigo_cliente'];
    
    $query="SELECT oc.folio, c.codigo, oc.orden_compra_c, oc.fecha_orden, oc.fecha_entrega, oc.total_pares 
            FROM ordenes_compra oc 
            INNER JOIN clientes c ON oc.cliente_id = c.id
            WHERE c.codigo = '$codigo_cliente'";
    $resultado=$mysql->query($query);
    if($resultado->num_rows > 0)
    {
        $itemRecords=array();
        $itemRecords["items"]=array();
        while ($item = $resultado->fetch_assoc())
        {
            extract($item);
            $itemDetails=array(
                "folio" => $folio,
                "codigo" => $codigo,
                "orden_compra_c" => $orden_compra_c,
                "fecha_orden" => $fecha_orden,
                "fecha_entrega" => $fecha_entrega,
                "total_pares" => $total_pares
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }
}
