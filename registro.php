<?php 
session_start();
include 'conexion.php';

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($conexion, $_POST['nom_user']);
    $clave = mysqli_real_escape_string($conexion, $_POST['pass_user']);
    $telefono = mysqli_real_escape_string($conexion, $_POST['tel_user']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['det_user']);

    $existe = mysqli_query($conexion, "SELECT * FROM Usuario WHERE nom_user = '$usuario'");
    
    if (!$existe) {
        $error = "Error al verificar el usuario";
    } elseif (mysqli_num_rows($existe) > 0) {
        $error = "El usuario ya existe";
    } else {
        $query = "INSERT INTO Usuario (nom_user, pass_user, tel_user, det_user) 
                 VALUES ('$usuario', '$clave', '$telefono', '$descripcion')";
        
        if (mysqli_query($conexion, $query)) {
            $success = "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
        } else {
            $error = "Error al registrar el usuario: " . mysqli_error($conexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Mensajería</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
            --danger: #ef233c;
            --success: #4cc9f0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: radial-gradient(circle at 10% 20%, #4361ee 0%, #3f37c9 90%);
        }
        
        .register-container {
            width: 100%;
            max-width: 450px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeIn 0.6s ease-out;
        }
        
        .register-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .register-header h2 {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 28px;
        }
        
        .register-header p {
            opacity: 0.9;
            font-weight: 300;
        }
        
        .register-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group label.required:after {
            content: " *";
            color: var(--danger);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        textarea.form-control {
            min-height: 100px;
            padding: 12px 15px;
            resize: vertical;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        
        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .register-footer a:hover {
            color: var(--secondary);
        }
        
        .error-message {
            background-color: #fee2e2;
            color: var(--danger);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            display: <?php echo isset($error) ? 'block' : 'none'; ?>;
        }
        
        .success-message {
            background-color: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            display: <?php echo isset($success) ? 'block' : 'none'; ?>;
        }
        
        .show-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Efecto de onda al hacer clic en el botón */
        @keyframes wave {
            0% {
                box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.4);
            }
            100% {
                box-shadow: 0 0 0 15px rgba(67, 97, 238, 0);
            }
        }
        
        .btn:active {
            animation: wave 0.5s;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2>Crear Cuenta</h2>
            <p>Únete a nuestra comunidad</p>
        </div>
        
        <div class="register-body">
            <?php if(isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username" class="required">Nombre de usuario</label>
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="nom_user" class="form-control" placeholder="Crea tu nombre de usuario" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="required">Contraseña</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="pass_user" class="form-control" placeholder="Crea una contraseña segura" required>
                        <span class="show-password" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="required">Teléfono</label>
                    <div class="input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone" name="tel_user" class="form-control" placeholder="Ingresa tu número telefónico" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción (opcional)</label>
                    <textarea id="description" name="det_user" class="form-control" placeholder="Cuéntanos algo sobre ti..."></textarea>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </form>
            
            <div class="register-footer">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar contraseña
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.querySelector('.show-password i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Efecto de enfoque en los inputs
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                const formGroup = this.closest('.form-group');
                if (formGroup) {
                    formGroup.querySelector('label').style.color = 'var(--primary)';
                }
            });
            
            input.addEventListener('blur', function() {
                const formGroup = this.closest('.form-group');
                if (formGroup) {
                    formGroup.querySelector('label').style.color = 'var(--dark)';
                }
            });
        });
        
        // Validación de teléfono
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9+]/g, '');
            });
        }
    </script>
</body>
</html>