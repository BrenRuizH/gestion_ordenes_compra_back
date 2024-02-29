<?php 

require_once '../conexion.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  // Si es una petición OPTIONS, devolver un código de estado 200 OK
  http_response_code(200);
} else {
  // Para otras peticiones, verificar las credenciales del usuario
  $json = file_get_contents('php://input');

  $params = json_decode($json);

  $query =  "SELECT * FROM usuarios where nombre='$params->nombre' and contrasenia='$params->contrasenia'";

  $resultado = $mysql->query($query);
  
  if ($resultado->num_rows > 0) {
    $datos = $resultado->fetch_assoc();
    echo json_encode($datos);
  } else {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autorizado']);
  }
}
