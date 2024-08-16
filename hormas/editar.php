<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $cliente_id = $_POST['cliente_id'];
    $matriz = isset($_POST['matriz']) ? "'".$_POST['matriz']."'" : 'NULL';
    $cambrillon = isset($_POST['cambrillon']) ? "'".$_POST['cambrillon']."'" : 'NULL';
    $materiales = isset($_POST['materiales']) ? "'".$_POST['materiales']."'" : 'NULL';
    $observaciones = isset($_POST['observaciones']) ? "'".$_POST['observaciones']."'" : 'NULL';
    $precio = $_POST['precio'];
    $motivo_cambio = isset($_POST['motivo_cambio']) ? "'".$_POST['motivo_cambio']."'" : 'NULL';



    $query = "CALL EditarHorma($id, '$nombre', '$cliente_id', $matriz, $cambrillon, $materiales, $observaciones, $precio, $motivo_cambio);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Horma editada exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar la horma", "error" => $mysql->error]);
    }
}
