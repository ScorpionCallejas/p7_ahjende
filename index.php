<?php
session_start();
include './chat-app/conexion.php';

// Configurar el charset para UTF-8
header('Content-Type: text/html; charset=UTF-8');
mysqli_set_charset($conexion, "utf8mb4"); // Cambiado a utf8mb4 para soporte completo de emojis

// Validar que el usuario esté logueado
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

// Obtener información del usuario actual
$current_user_id = $_SESSION['id_user'];
$sql_user = "SELECT * FROM Usuario WHERE id_user = $current_user_id";
$result_user = mysqli_query($conexion, $sql_user);
$current_user = mysqli_fetch_assoc($result_user);

// Obtener salas del usuario actual con el último mensaje
$sql_salas = "SELECT s.id_sala, s.nom_sala, 
              (SELECT m.msj_msj FROM Mensaje m WHERE m.id_sala = s.id_sala ORDER BY m.fecha_envio DESC LIMIT 1) as last_message,
              (SELECT m.fecha_envio FROM Mensaje m WHERE m.id_sala = s.id_sala ORDER BY m.fecha_envio DESC LIMIT 1) as last_message_time
              FROM Sala s
              JOIN Usuario_Sala us ON s.id_sala = us.id_sala
              WHERE us.id_user = $current_user_id
              ORDER BY COALESCE(last_message_time, '1970-01-01') DESC";
$result_salas = mysqli_query($conexion, $sql_salas);
$salas = array();
while($row = mysqli_fetch_assoc($result_salas)) {
    $salas[] = $row;
}

// Obtener mensajes si hay sala seleccionada
$mensajes = array();
$current_sala = null;
if(isset($_GET['sala'])) {
    $sala_id = intval($_GET['sala']);
    $sql_sala = "SELECT * FROM Sala WHERE id_sala = $sala_id";
    $result_sala = mysqli_query($conexion, $sql_sala);
    $current_sala = mysqli_fetch_assoc($result_sala);
    
    // Obtener participantes de la sala
    $sql_participantes = "SELECT u.id_user, u.nom_user 
                         FROM Usuario_Sala us
                         JOIN Usuario u ON us.id_user = u.id_user
                         WHERE us.id_sala = $sala_id";
    $result_participantes = mysqli_query($conexion, $sql_participantes);
    $participantes = array();
    while($row = mysqli_fetch_assoc($result_participantes)) {
        $participantes[] = $row;
    }
    
    // Obtener mensajes con soporte para emojis
    $sql_mensajes = "SELECT m.id_msj, m.id_user, m.msj_msj, m.fecha_envio, u.nom_user 
                    FROM Mensaje m
                    JOIN Usuario u ON m.id_user = u.id_user
                    WHERE m.id_sala = $sala_id
                    ORDER BY m.fecha_envio ASC";
    $result_mensajes = mysqli_query($conexion, $sql_mensajes);
    while($row = mysqli_fetch_assoc($result_mensajes)) {
        $mensajes[] = $row;
    }
}

// Función para acortar texto
function acortarTexto($texto, $longitud = 30) {
    if (mb_strlen($texto, 'UTF-8') > $longitud) {
        return mb_substr($texto, 0, $longitud, 'UTF-8') . '...';
    }
    return $texto;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #5a75f5;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
            --dark-light: #343a40;
            --danger: #ef233c;
            --success: #4cc9f0;
            --gray: #adb5bd;
            --gray-light: #e9ecef;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.2s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            height: 100vh;
            overflow: hidden;
        }

        .app-container {
            display: flex;
            height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            background-color: var(--white);
            box-shadow: var(--shadow-md);
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            width: 350px;
            border-right: 1px solid var(--gray-light);
            display: flex;
            flex-direction: column;
            background-color: var(--white);
            transition: transform 0.3s ease;
            z-index: 10;
        }

        .sidebar-header {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--primary);
            color: var(--white);
            box-shadow: var(--shadow-sm);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 1rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .sidebar-actions {
            display: flex;
            gap: 1rem;
        }

        .icon-btn {
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-btn:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .search-container {
            padding: 1rem;
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-light);
        }

        .search-box {
            background-color: var(--gray-light);
            border-radius: var(--radius-md);
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .search-box:focus-within {
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .search-box input {
            background: none;
            border: none;
            width: 100%;
            padding: 0.25rem;
            font-size: 0.9rem;
            outline: none;
        }

        .chats-container {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            display: flex;
            padding: 1rem 1.5rem;
            gap: 1rem;
            cursor: pointer;
            border-bottom: 1px solid var(--gray-light);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }

        .chat-item:hover {
            background-color: var(--gray-light);
        }

        .chat-item.active {
            background-color: rgba(67, 97, 238, 0.1);
            border-left: 3px solid var(--primary);
        }

        .chat-info {
            flex: 1;
            overflow: hidden;
            min-width: 0;
        }

        .chat-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .last-message {
            font-size: 0.85rem;
            color: var(--gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-time {
            font-size: 0.75rem;
            color: var(--gray);
            white-space: nowrap;
            margin-left: 0.5rem;
        }

        /* Main Chat Area */
        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #f0f2f5;
            position: relative;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            background-color: var(--white);
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 1rem;
            border-bottom: 1px solid var(--gray-light);
            box-shadow: var(--shadow-sm);
            z-index: 5;
        }

        .chat-title {
            font-weight: 600;
            font-size: 1rem;
        }

        .chat-participants {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .messages-container {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23d1d5db' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }

        .message {
            max-width: 85%;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            position: relative;
            word-wrap: break-word;
            animation: fadeIn 0.3s;
            box-shadow: var(--shadow-sm);
            line-height: 1.5;
            font-size: 0.95rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .message-incoming {
            align-self: flex-start;
            background-color: var(--white);
            border-top-left-radius: 0;
        }

        .message-outgoing {
            align-self: flex-end;
            background-color: var(--primary);
            color: var(--white);
            border-top-right-radius: 0;
        }

        .message-info {
            display: flex;
            justify-content: flex-end;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            opacity: 0.8;
            gap: 0.5rem;
            align-items: center;
        }

        .message-outgoing .message-info {
            color: rgba(255,255,255,0.8);
        }

        .message-sender {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--primary);
        }

        .message-text {
            white-space: pre-wrap;
        }

        .input-container {
            padding: 1rem;
            background-color: var(--white);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-top: 1px solid var(--gray-light);
            position: relative;
        }

        .input-box {
            flex: 1;
            display: flex;
            background-color: var(--white);
            border-radius: var(--radius-lg);
            padding: 0.75rem 1rem;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid var(--gray-light);
            transition: var(--transition);
        }

        .input-box:focus-within {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        .input-box textarea {
            flex: 1;
            border: none;
            resize: none;
            height: 24px;
            max-height: 120px;
            outline: none;
            font-size: 0.95rem;
            padding: 0.25rem;
            background: transparent;
            font-family: inherit;
            line-height: 1.5;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--gray);
            font-size: 1.25rem;
            cursor: pointer;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background-color: var(--gray-light);
            color: var(--dark);
        }

        .send-btn {
            background-color: var(--primary);
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .send-btn:hover {
            background-color: var(--secondary);
            transform: scale(1.05);
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        .no-chat-selected {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--dark);
            text-align: center;
            padding: 2rem;
        }

        .no-chat-selected i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: var(--gray);
            opacity: 0.3;
        }

        .no-chat-selected h2 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .no-chat-selected p {
            color: var(--gray);
            max-width: 400px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: var(--white);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 0.5rem;
        }

        /* Responsive design */
        @media (max-width: 992px) {
            .sidebar {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
                width: 300px;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .chat-header {
                padding-left: 1rem;
            }
        }

        @media (max-width: 576px) {
            .messages-container {
                padding: 1rem;
            }
            
            .input-container {
                padding: 0.75rem;
            }
            
            .message {
                max-width: 90%;
                padding: 0.75rem;
            }
            
            .no-chat-selected {
                padding: 1.5rem;
            }
            
            .no-chat-selected h2 {
                font-size: 1.25rem;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar with chat list -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info">
                    <div class="avatar"><?php echo substr($current_user['nom_user'], 0, 1); ?></div>
                    <div class="user-name"><?php echo htmlspecialchars($current_user['nom_user'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="sidebar-actions">
                    <a href="logout.php" class="icon-btn" title="Cerrar sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
            
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar o empezar un chat nuevo">
                </div>
            </div>
            
            <div class="chats-container">
                <?php foreach($salas as $sala): 
                    $active = isset($current_sala) && $current_sala['id_sala'] == $sala['id_sala'] ? 'active' : '';
                    $last_message = isset($sala['last_message']) ? $sala['last_message'] : 'No hay mensajes';
                    $last_time = isset($sala['last_message_time']) ? date('H:i', strtotime($sala['last_message_time'])) : '';
                ?>
                <a href="index.php?sala=<?php echo $sala['id_sala']; ?>" class="chat-item <?php echo $active; ?>">
                    <div class="avatar"><?php echo substr($sala['nom_sala'], 0, 1); ?></div>
                    <div class="chat-info">
                        <div class="chat-name"><?php echo htmlspecialchars($sala['nom_sala'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="last-message"><?php echo htmlspecialchars(acortarTexto($last_message), ENT_QUOTES, 'UTF-8'); ?></div>
                    </div>
                    <div class="chat-time"><?php echo $last_time; ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Main Chat Area -->
        <div class="chat-container <?php echo isset($current_sala) ? 'active' : ''; ?>">
            <?php if(isset($current_sala)): ?>
                <div class="chat-header">
                    <button class="mobile-menu-btn" id="mobileMenuBtnChat">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="avatar"><?php echo substr($current_sala['nom_sala'], 0, 1); ?></div>
                    <div>
                        <div class="chat-title"><?php echo htmlspecialchars($current_sala['nom_sala'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="chat-participants">
                            <?php 
                            $participant_names = array();
                            foreach($participantes as $p) {
                                $participant_names[] = htmlspecialchars($p['nom_user'], ENT_QUOTES, 'UTF-8');
                            }
                            echo implode(', ', $participant_names);
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="messages-container" id="messagesContainer">
                    <?php foreach($mensajes as $mensaje): 
                        $is_current_user = $mensaje['id_user'] == $current_user_id;
                        $message_class = $is_current_user ? 'message-outgoing' : 'message-incoming';
                    ?>
                    <div class="message <?php echo $message_class; ?>">
                        <?php if(!$is_current_user): ?>
                            <div class="message-sender"><?php echo htmlspecialchars($mensaje['nom_user'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <div class="message-text"><?php echo htmlspecialchars($mensaje['msj_msj'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="message-info">
                            <?php echo date('H:i', strtotime($mensaje['fecha_envio'])); ?>
                            <?php if($is_current_user): ?>
                                <i class="fas fa-check-double"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="input-container">
                    <div class="input-box">
                        <textarea id="messageInput" placeholder="Escribe un mensaje..." rows="1"></textarea>
                    </div>
                    <button class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                
            <?php else: ?>
                <div class="no-chat-selected">
                    <i class="fas fa-comment-dots"></i>
                    <h2>Bienvenido a la aplicación de mensajes</h2>
                    <p>Selecciona un chat de la lista para comenzar a conversar o crea un nuevo chat.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>


    <script>
        // WebSocket Implementation for PHP 5.6
        var socket;
        var reconnectAttempts = 0;
        var maxReconnectAttempts = 5;
        var reconnectDelay = 3000;

        function connectWebSocket() {
            // Asegúrate de usar la URL correcta de tu servidor WebSocket
            var wsUrl = 'wss://socket.ahjende.com/wss/?encoding=text';
            
            try {
                socket = new WebSocket(wsUrl);
                
                socket.onopen = function(e) {
                    console.log('Conexión WebSocket establecida');
                    reconnectAttempts = 0;
                    
                    // Autenticación con el servidor
                    var authMsg = {
                        action: 'auth',
                        userId: <?php echo $current_user_id; ?>,
                        roomId: <?php echo isset($current_sala) ? $current_sala['id_sala'] : 'null'; ?>
                    };
                    socket.send(JSON.stringify(authMsg));
                };
                
                socket.onerror = function(error) {
                    console.error('Error en WebSocket:', error);
                };
                
                socket.onmessage = function(event) {
                    try {
                        var message = JSON.parse(event.data);
                        if (message.action === 'new_message') {
                            addMessageToChat(message);
                        }
                    } catch (e) {
                        console.error('Error al procesar mensaje:', e);
                    }
                };
                
                socket.onclose = function(e) {
                    if (e.code !== 1000 && reconnectAttempts < maxReconnectAttempts) {
                        console.log('Intentando reconectar... Intento ' + (reconnectAttempts + 1));
                        setTimeout(connectWebSocket, reconnectDelay);
                        reconnectAttempts++;
                    }
                };
            } catch (e) {
                console.error('Error al conectar WebSocket:', e);
            }
        }

        // Iniciar conexión cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            var messageInput = document.getElementById('messageInput');
            var sendBtn = document.getElementById('sendBtn');
            var messagesContainer = document.getElementById('messagesContainer');
            
            // Verificar que los elementos existen
            if (!messageInput || !sendBtn || !messagesContainer) {
                console.error('No se encontraron elementos esenciales del chat');
                return;
            }
            
            // Conectar WebSocket solo si hay una sala activa
            if (document.querySelector('.chat-container.active')) {
                connectWebSocket();
            }
            
            // Configurar event listeners
            sendBtn.addEventListener('click', sendMessage);
            
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Auto-resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Función para enviar mensajes
        function sendMessage() {
            var messageInput = document.getElementById('messageInput');
            var messageText = messageInput.value.trim();
            
            if (messageText) {
                var message = {
                    id_sala: <?php echo isset($current_sala) ? $current_sala['id_sala'] : 'null'; ?>,
                    id_user: <?php echo $current_user_id; ?>,
                    msj_msj: messageText,
                    nom_user: '<?php echo addslashes($current_user['nom_user']); ?>',
                    action: 'new_message'
                };
                
                // Intentar enviar por WebSocket primero
                if (socket && socket.readyState === WebSocket.OPEN) {
                    socket.send(JSON.stringify(message));
                } else {
                    console.log('WebSocket no disponible, usando AJAX');
                    sendMessageAjax(message);
                }
                
                // Añadir mensaje localmente
                addMessageToChat({
                    id_sala: message.id_sala,
                    id_user: message.id_user,
                    msj_msj: message.msj_msj,
                    nom_user: message.nom_user,
                    fecha_envio: new Date() // Usar objeto Date directamente
                });
                
                // Limpiar el input
                messageInput.value = '';
                messageInput.style.height = '24px';
            }
        }

        // Función fallback AJAX
        function sendMessageAjax(message) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ',/save_message.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log('Mensaje guardado via AJAX');
                    } else {
                        console.error('Error al guardar mensaje:', xhr.status);
                    }
                }
            };
            
            var params = 'id_sala=' + message.id_sala + 
                         '&id_user=' + message.id_user + 
                         '&msj_msj=' + encodeURIComponent(message.msj_msj);
            
            xhr.send(params);
        }

        // Función para añadir mensajes al chat
        function addMessageToChat(message) {
            var messagesContainer = document.getElementById('messagesContainer');
            if (!messagesContainer) return;
            
            var isCurrentUser = message.id_user == <?php echo $current_user_id; ?>;
            var messageClass = isCurrentUser ? 'message-outgoing' : 'message-incoming';
            
            var messageElement = document.createElement('div');
            messageElement.className = 'message ' + messageClass;
            messageElement.innerHTML = 
                (isCurrentUser ? '' : '<div class="message-sender">' + escapeHtml(message.nom_user) + '</div>') +
                '<div class="message-text">' + escapeHtml(message.msj_msj) + '</div>' +
                '<div class="message-info">' + 
                    formatTime(message.fecha_envio) +
                    (isCurrentUser ? '<i class="fa fa-check-double" style="margin-left: 5px;"></i>' : '') +
                '</div>';
            
            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Función para escapar HTML
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Función para formatear la hora
        function formatTime(dateString) {
    try {
        // Si es una cadena ISO (viene del servidor)
        if(typeof dateString === 'string' && dateString.includes('T')) {
            const date = new Date(dateString);
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        // Si ya es un objeto Date (nuevos mensajes locales)
        if(dateString instanceof Date) {
            return dateString.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        // Si es un timestamp numérico
        if(typeof dateString === 'number') {
            return new Date(dateString).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        // Si no coincide con ningún formato conocido
        return new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch (e) {
        console.error('Error formateando fecha:', e);
        return 'Ahora';
    }
}

        // Polling como fallback
        function checkForNewMessages() {
            var salaId = <?php echo isset($current_sala) ? $current_sala['id_sala'] : 'null'; ?>;
            
            if (salaId) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_messages.php?sala=' + salaId, true);
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            var messages = JSON.parse(xhr.responseText);
                            // Implementar lógica para actualizar mensajes
                            console.log('Mensajes recibidos:', messages);
                        } catch (e) {
                            console.error('Error al parsear mensajes:', e);
                        }
                    }
                };
                
                xhr.send();
            }
        }

        // Iniciar polling cada 5 segundos si WebSocket no está disponible
        setInterval(function() {
            if (!socket || socket.readyState !== WebSocket.OPEN) {
                checkForNewMessages();
            }
        }, 5000);
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenuBtnChat = document.getElementById('mobileMenuBtnChat');
        const sidebar = document.getElementById('sidebar');
        
        if(mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
        
        if(mobileMenuBtnChat) {
            mobileMenuBtnChat.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // Auto-resize textarea
        const messageInput = document.getElementById('messageInput');
        if(messageInput) {
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    </script>
</body>
</html>