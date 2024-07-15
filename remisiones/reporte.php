<?php

if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin = $_GET['fecha_fin'];
    $cliente_id = isset($_GET['cliente_id']) ? $_GET['cliente_id'] : null;

    $query="SELECT r.id, c.codigo, r.total_pares, r.precio_final
	    FROM remisiones r
	    INNER JOIN clientes c ON r.cliente_id = c.id
      WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";

    if ($cliente_id !== null) {
        $query .= " AND r.cliente_id = '$cliente_id'";
        }
    $query .= " ORDER BY r.id";

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
                "codigo" => $codigo,
                "total_pares" => $total_pares,
                "precio_final" => $precio_final,
            );
            array_push($itemRecords["items"], $itemDetails);
 }
        http_response_code(200);
        echo json_encode($itemRecords);
 } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron remisiones."));
  }
}
?>
