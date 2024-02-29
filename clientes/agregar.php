<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $codigo = $_POST['codigo'];
    $razonSocial = $_POST['razonSocial'];
    $rfc = $_POST['rfc'];
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : NULL;
    $pagosCon = isset($_POST['pagosCon']) ? $_POST['pagosCon'] : NULL;
    $pedidosA = isset($_POST['pedidosA']) ? $_POST['pedidosA'] : NULL;
    $recepcionDePedidos = isset($_POST['recepcionDePedidos']) ? $_POST['recepcionDePedidos'] : NULL;

    $query = "CALL AgregarCliente('$codigo', '$razonSocial', '$rfc', $telefono, '$pagosCon', '$pedidosA', '$recepcionDePedidos');";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Cliente creado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al crear el cliente", "error" => $mysql->error]);
    }
}
