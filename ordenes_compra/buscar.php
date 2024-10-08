<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $texto = $_GET['texto'];
    
    $query="SELECT oc.id as orden_id, oc.folio, c.codigo, oc.orden_compra_c, oc.fecha_orden, oc.fecha_entrega, oc.total_pares, oc.facturaNo, oc.status, oc.remision_id  
            FROM ordenes_compra oc 
            INNER JOIN clientes c ON oc.cliente_id = c.id
            WHERE c.codigo LIKE '%$texto%' OR oc.orden_compra_c LIKE '%$texto%'";
    

    $resultado=$mysql->query($query);
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
                "status" => $status,
                "remision_id" => $remision_id
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        
        $queryFolioMax="SELECT MAX(folio) as maxFolio FROM ordenes_compra";
        $resultadoFolioMax=$mysql->query($queryFolioMax);
        $folioMax = $resultadoFolioMax->fetch_assoc();
        $itemRecords["maxFolio"] = $folioMax['maxFolio'];

        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron órdenes de compra."));
    }
}
