<?php
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $id = $_GET['id'];

    $query="SELECT * FROM hormas WHERE id = $id";
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
                "nombre" => $nombre,
                "cliente_id" => $cliente_id,
                "matriz" => $matriz,
                "cambrillon" => $cambrillon,
                "materiales" => $materiales,
                "observaciones" => $observaciones,
                "precio" => $precio,
                "precio_anterior" => $precio_anterior,
                "motivo_cambio" => $motivo_cambio,
                "fecha_modificacion" => $fecha_modificacion
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron hormas con el ID correspondiente."));
    }
}
