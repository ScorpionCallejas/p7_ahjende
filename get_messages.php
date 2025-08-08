<?php
include 'conexion.php';

if (!isset($_GET['sala'])) {
    die(json_encode([]));
}

$sala_id = intval($_GET['sala']);
$current_user_id = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

$sql = "SELECT m.id_msj, m.id_user, m.msj_msj, m.fecha_envio, u.nom_user 
        FROM Mensaje m
        JOIN Usuario u ON m.id_user = u.id_user
        WHERE m.id_sala = $sala_id
        ORDER BY m.fecha_envio ASC";

$result = $conexion->query($sql);
$messages = [];

while($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>