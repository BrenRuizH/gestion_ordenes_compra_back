<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    
    parse_str(file_get_contents("php://input"),$post_vars);
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $post_vars['id'];

    $query = "CALL EliminarHorma($id);";

    $resultSet = $mysql->query($query);

    if($mysql->affected_rows > 0){
        echo "Horma eliminada exitosamente";
    } else {
        echo "Error al eliminar la horma";
    }
}
