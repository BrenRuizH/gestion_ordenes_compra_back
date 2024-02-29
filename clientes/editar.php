<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $razonSocial = $_POST['razonSocial'];
    $rfc = $_POST['rfc'];
    $telefono = $_POST['telefono'];
    $pagosCon = $_POST['pagosCon'];
    $pedidosA = $_POST['pedidosA'];
    $recepcionDePedidos = $_POST['recepcionDePedidos'];

    $query = "CALL EditarCliente($id, '$codigo', '$razonSocial', '$rfc', $telefono, '$pagosCon', '$pedidosA', '$recepcionDePedidos');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
//425
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Cliente editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el cliente", "error" => $mysql->error]);
    }
}
