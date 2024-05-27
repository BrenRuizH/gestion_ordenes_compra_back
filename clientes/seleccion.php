<?php
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $id = $_GET['id'];

    $query="SELECT * FROM clientes WHERE id = $id";
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
                "razonSocial" => $razonSocial,
                "rfc" => $rfc,
                "telefono" => $telefono,
                "pagosCon" => $pagosCon,
                "pedidosA" => $pedidosA,
                "direccion" => $direccion
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron cambrillones con el ID correspondiente."));
    }
}
