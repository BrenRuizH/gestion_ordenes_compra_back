<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type:application/json; charset=UTF-8');
header('Access-Control-Allow-Methods:GET,POST,PUT,PATCH,DELETE');
header("Access-Control-Max-Age:86000");
header('Access-Control-Allow-Headers:Content-Type,Access-Control-Allow-Headers,Authorization,X-Requested-With');

if($_SERVER['REQUEST_METHOD']=='OPTIONS'){
    header('HTTP/1.1 200 OK');
    exit();
}

function conexion() {
    $mysql = mysqli_connect("o3iyl77734b9n3tg.cbetxkdyhwsb.us-east-1.rds.amazonaws.com", "ie5g1p0as6ry8me4", "cd1w2cpo2o5c94fa", "xj9i40s68gou6ok7");
    return $conexion;
}

$con=conexion();
