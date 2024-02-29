<?php
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    parse_str(file_get_contents("php://input"),$post_vars);
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $post_vars['id'];
    $nombre = $post_vars['nombre'];
    $cliente_id = $post_vars['cliente_id'];

    $query = "CALL EditarHorma($id, '$nombre', $cliente_id);";

    $resultSet = $mysql->query($query);

    if($mysql->affected_rows > 0){
        echo "Horma actualizada exitosamente";
    } else {
        echo "Error al actualizar la horma";
    }
}
