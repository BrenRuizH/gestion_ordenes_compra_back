<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_GET['id'];

    $query = "SELECT h.id, h.nombre, h.matriz, h.cambrillon, h.materiales, h.observaciones, h.precio, h.precio_anterior, h.fecha_modificacion, c.codigo AS cliente FROM hormas h
              INNER JOIN clientes c ON h.cliente_id = c.id 
              WHERE c.id = $id;";

    $resultado=$mysql->query($query);
    if($resultado->num_rows > 0) {
        $itemRecords=array();
        $itemRecords["items"]=array();
        while ($item = $resultado->fetch_assoc()) {
            extract($item);
            $itemDetails=array(
                "id" => $id,
                "nombre" => $nombre,
                "matriz" => $matriz,
                "cambrillon" => $cambrillon,
                "materiales" => $materiales,
                "observaciones" => $observaciones,
                "precio" => $precio,
                "cliente" => $cliente,
                "precio_anterior" => $precio_anterior,
                "fecha_modificacion" => $fecha_modificacion
            );
            array_push($itemRecords["items"], $itemDetails);
        }

        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron hormas."));
    }
}
