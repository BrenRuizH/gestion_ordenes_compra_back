<?php
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $cliente_id = $_GET['cliente_id'];

    $query="SELECT oc.folio, oc.total_pares, h.precio*oc.total_pares AS precio
      FROM ordenes_compra oc
      INNER JOIN hormas h ON oc.horma_id = h.id
      WHERE oc.cliente_id = $cliente_id
      ORDER BY oc.fecha_orden DESC, oc.folio DESC;";
  
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
                "total_pares" => $total_pares,
                "precio" => $precio
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron folios con el ID correspondiente."));
    }
}
