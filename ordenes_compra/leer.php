<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $query="SELECT oc.id, oc.folio, c.codigo, oc.orden_compra_c, oc.fecha_orden, oc.fecha_entrega, oc.total_pares, h.precio*oc.total_pares AS precio, oc.facturaNo, oc.status, oc.remision_id
	    FROM ordenes_compra oc 
	    INNER JOIN clientes c ON oc.cliente_id = c.id
            INNER JOIN hormas h ON oc.horma_id = h.id
	    ORDER BY CAST(oc.folio AS UNSIGNED) DESC, oc.fecha_orden DESC;";

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
                "folio" => $folio,
                "codigo" => $codigo,
                "orden_compra_c" => $orden_compra_c,
                "fecha_orden" => $fecha_orden,
                "fecha_entrega" => $fecha_entrega,
                "total_pares" => $total_pares,
                "precio" => $precio,
                "facturaNo" => $facturaNo,
                "status" => $status,
                "remision_id" => $remision_id
            );
            array_push($itemRecords["items"], $itemDetails);
 }
        $queryFolioMax="SELECT MAX(CAST(folio AS UNSIGNED)) as maxFolio FROM ordenes_compra;";
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
?>
