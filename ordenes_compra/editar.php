<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once '../conexion.php';
    include '../config.php';
    
    $id = $_POST['id'];
    $fecha_orden = $_POST['fecha_orden'];
    $fecha_entrega = $_POST['fecha_entrega'];
    $cliente_id = $_POST['cliente_id'];
    $folio = $_POST['folio'];
    $orden_compra_c = $_POST['orden_compra_c'];
    $horma_id = $_POST['horma_id'];
    $cambrillon_id = $_POST['cambrillon_id'];
    $observaciones = $_POST['observaciones'];
    $matriz = $_POST['matriz'];
    $total_pares = $_POST['total_pares'];
    $material_id = $_POST['material_id'];
    $punto = $_POST['punto'];
    $cantidad = $_POST['cantidad'];

    $query = "CALL Actualizar($id, '$fecha_orden', '$fecha_entrega', $cliente_id, '$folio', $orden_compra_c, $horma_id, $cambrillon_id, '$observaciones', '$matriz', $total_pares, $material_id, $punto, $cantidad);";

    $resultSet = $mysql->query($query);
    if(!$resultSet){
        die("Error en la consulta: " . $mysql->error);
    }

    if($resultSet){
        echo json_encode(["status"=>"success","message" => "Cambrillón editado exitosamente"]);
    } else {
        echo json_encode(["status"=>"error","message" => "Error al editar el cambrillón", "error" => $mysql->error]);
    }
}
