<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $codigo = $_POST['codigo'];
    $razonSocial = $_POST['razonSocial'];
    $rfc = isset($_POST['rfc']) ? "'".$_POST['rfc']."'" : 'NULL';
    $telefono = isset($_POST['telefono']) ? "'".$_POST['telefono']."'" : 'NULL';
    $pagosCon = isset($_POST['pagosCon']) ? "'".$_POST['pagosCon']."'" : 'NULL';
    $pedidosA = isset($_POST['pedidosA']) ? "'".$_POST['pedidosA']."'" : 'NULL';
    $direccion = isset($_POST['direccion']) ? "'".$_POST['direccion']."'" : 'NULL';

    $query = "CALL EditarCliente($id, '$codigo', '$razonSocial', $rfc, $telefono, $pagosCon, $pedidosA, $direccion);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }
    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Cliente editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el cliente", "error" => $mysql->error]);
    }
}
