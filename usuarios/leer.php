<?php
if($_SERVER["REQUEST_METHOD"]=="GET")
{
    require_once '../conexion.php';
    include '../config.php';

    $query="SELECT usuarios.id, usuarios.nombre, usuarios.contrasenia, roles.nombre AS rol 
            FROM usuarios 
            INNER JOIN roles ON usuarios.rol_id = roles.id
            ORDER BY usuarios.nombre;";
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
                "contrasenia" => $contrasenia,
                "rol" => $rol
            );
            array_push($itemRecords["items"], $itemDetails);
        }
        http_response_code(200);
        echo json_encode($itemRecords);
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "No se encontraron usuarios."));
    }
}
