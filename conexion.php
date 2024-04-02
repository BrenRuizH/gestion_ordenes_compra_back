<?php

$mysql = mysqli_connect("localhost", "root", "root", "bd_distribuidora");

if ($mysql->connect_error) {
    die("Error de conexión");
} 
