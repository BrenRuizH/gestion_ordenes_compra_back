<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_GET['id'];

    $query = "SELECT cam.id, cam.nombre, h.nombre AS horma
              FROM cambrillones cam 
              INNER JOIN hormas h ON cam.horma_id = h.id
              INNER JOIN clientes c ON h.cliente_id = c.id
              WHERE c.id =$id;";

    $resultado=$mysql->query($query);
    if($resultado->num_rows > 0) {
        $itemRecords=array();
        $itemRecords["items"]=array();
        while ($item = $resultado->fetch_assoc()) {
            extract($item);
            $itemDetails=array(
                "id" => $id,
                "nombre" => $nombre,
                "horma" => $horma
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron cambrillones."));
    }
}