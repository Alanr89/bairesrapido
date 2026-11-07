<?php
session_start();

$host = 'localhost';
$db   = 'baires_rapido';
$user = 'root';
$pass = '';

// Conectar sin especificar base de datos para crearla si no existe
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Crear base de datos si no existe
$conn->query("CREATE DATABASE IF NOT EXISTS $db");
$conn->select_db($db);

// Crear tabla de usuarios si no existe
$conn->query("
    CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        nombre VARCHAR(100),
        rol ENUM('admin', 'operador') DEFAULT 'operador',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Insertar usuarios de prueba si no existen
$conn->query("INSERT IGNORE INTO usuarios (usuario, password, nombre, rol) VALUES ('admin', 'admin', 'Administrador', 'admin')");
$conn->query("INSERT IGNORE INTO usuarios (usuario, password, nombre, rol) VALUES ('op', 'op', 'Operador', 'operador')");

// Cerrar sesión si se solicita logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, usuario, password, nombre, rol FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if ($password === $user['password']) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Baires Rápido</title>
    <style>
        :root {
            --primary: #003366;
            --accent: #00a8e8;
            --bg: #f0f2f5;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg);
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo {
            max-width: 250px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: var(--accent);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        .btn-login:hover {
            background-color: #002244;
        }
        .footer-text {
            margin-top: 25px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <img src="assets/GrupoBaires.jpg" alt="Grupo Baires" class="logo">
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            
            <button type="submit" class="btn-login">Acceder</button>
        </form>

        <?php if ($error): ?>
            <p style="color: #d32f2f; margin-top: 15px; font-size: 14px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <p class="footer-text">© 2026 Baires Rápido - Sistema de Operaciones</p>
    </div>

</body>
</html>