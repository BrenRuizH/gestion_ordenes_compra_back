<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $cliente_id = $_POST['cliente_id'];
    $matriz = $_POT['matriz'];
    $cambrillon = $_POT['cambrillon'];
    $materiales = $_POT['materiales'];
    $observaciones = $_POT['observaciones'];

    $query = "CALL EditarHorma($id, '$nombre', $cliente_id, $matriz, '$cambrillon', '$materiales', '$observaciones');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
//425
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Horma editada exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar la horma", "error" => $mysql->error]);
    }
}
