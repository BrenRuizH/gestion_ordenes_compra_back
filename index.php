<?php
include("conexion.php");

$query = $mysql->query("SELECT * FROM usuarios");

$usuarios = array();
while ($row = $query->fetch_assoc()) {
    $usuarios[] = $row;
}

$json_result = json_encode($usuarios);

echo $json_result;
?>
