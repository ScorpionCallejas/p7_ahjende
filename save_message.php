<?php
session_start();
include 'conexion.php';

// Asegurar que la conexión use UTF-8
header('Content-Type: text/html; charset=UTF-8');
mysqli_set_charset($conexion, "utf8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_sala = isset($_POST['id_sala']) ? intval($_POST['id_sala']) : 0;
    $id_user = isset($_POST['id_user']) ? intval($_POST['id_user']) : 0;
    $msj_msj = isset($_POST['msj_msj']) ? mysqli_real_escape_string($conexion, $_POST['msj_msj']) : '';
    
    if ($id_sala > 0 && $id_user > 0 && !empty($msj_msj)) {
        // Guardar en base de datos
        $sql = "INSERT INTO Mensaje (id_sala, id_user, msj_msj, fecha_envio) 
                VALUES ($id_sala, $id_user, '$msj_msj', NOW())";
        
        if (mysqli_query($conexion, $sql)) {
            // Obtener información del usuario
            $sql_user = "SELECT nom_user FROM Usuario WHERE id_user = $id_user";
            $result_user = mysqli_query($conexion, $sql_user);
            $user = mysqli_fetch_assoc($result_user);
            
            // Preparar respuesta
            $response = array(
                'id_sala' => $id_sala,
                'id_user' => $id_user,
                'msj_msj' => $msj_msj,
                'nom_user' => $user['nom_user'],
                'fecha_envio' => date('Y-m-d H:i:s')
            );
            
            echo json_encode($response);
        } else {
            echo json_encode(array('error' => 'Error al guardar el mensaje'));
        }
    } else {
        echo json_encode(array('error' => 'Datos incompletos'));
    }
} else {
    echo json_encode(array('error' => 'Método no permitido'));
}