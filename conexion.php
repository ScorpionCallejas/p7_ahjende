<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "DB_Salas";

$conexion = mysqli_connect($host, $user, $pass, $database);

if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>
