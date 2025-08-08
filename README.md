# Aplicación de Chat en PHP

Aplicación web de mensajería instantánea con salas de chat, autenticación de usuarios y comunicación en tiempo real.

## Características principales

- Autenticación de usuarios (login/registro)
- Listado de salas de chat
- Visualización de mensajes en tiempo real
- Interfaz responsive para móviles y desktop
- Soporte para emojis (utf8mb4)
- Alternativa de WebSocket y polling para compatibilidad

## Estructura de archivos

chat-app/
├── conexion.php # Conexión a la base de datos
├── get_messages.php # Obtener mensajes de una sala (API)
├── index.php # Página principal del chat
├── login.php # Página de inicio de sesión
├── logout.php # Cierre de sesión
├── registro.php # Página de registro de usuarios
└── save_message.php # Guardar mensajes (API)


## Requisitos del sistema

- PHP 5.6 o superior
- MySQL/MariaDB
- Servidor web (Apache, Nginx, etc.)
- Opcional: Servidor WebSocket para funcionalidad en tiempo real

## Base de datos

La aplicación utiliza las siguientes tablas:

1. **Usuario**: Almacena información de los usuarios
2. **Sala**: Contiene las salas de chat
3. **Usuario_Sala**: Relación muchos-a-muchos entre usuarios y salas
4. **Mensaje**: Almacena todos los mensajes enviados

## Instalación

1. Clonar el repositorio en tu servidor web
2. Crear la base de datos e importar el esquema SQL
3. Configurar los detalles de conexión en `conexion.php`
4. Asegurarse que el servidor tenga permisos de escritura

## Configuración

Editar el archivo `conexion.php` con tus credenciales de base de datos:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "DB_Salas";

$conexion = mysqli_connect($host, $user, $pass, $database);
// ...
```

Tecnologías utilizadas

- PHP
- MySQL
- JavaScript (WebSocket y AJAX)
- HTML5 y CSS3
- Font Awesome para iconos
