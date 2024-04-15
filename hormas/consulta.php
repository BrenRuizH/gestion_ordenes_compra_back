<?php
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $cliente_id = $_GET['cliente_id'];

    $query="SELECT * FROM hormas WHERE cliente_id = $cliente_id";
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
                "matriz" => $matriz,
                "cambrillon" => $cambrillon,
                "materiales" => $materiales,
                "observaciones" => $observaciones,
                "cliente_id" => $cliente_id
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
