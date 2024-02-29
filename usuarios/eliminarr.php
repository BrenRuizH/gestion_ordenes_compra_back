<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"),$post_vars);
    
    require_once '../conexion.php';
    include '../config.php';
    
    $id = $post_vars['id'];

    $query = "CALL EliminarUsuario($id);";

    $resultSet = $mysql->query($query);

    if($resultSet){
        echo "Usuario eliminado exitosamente";
    } else {
        echo "Error al eliminar el usuario";
    }
}
