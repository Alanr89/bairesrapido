<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$host = 'localhost';
$db   = 'baires_rapido';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit();
}

$conn->select_db($db);

// Crear tabla si no existe (por seguridad)
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
$conn->query("INSERT IGNORE INTO usuarios (usuario, password, nombre, rol) VALUES ('admin', 'admin', 'Administrador Sistema', 'admin')");
$conn->query("INSERT IGNORE INTO usuarios (usuario, password, nombre, rol) VALUES ('op', 'op', 'Operador Operaciones', 'operador')");

// Obtener parámetros de búsqueda
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT id, usuario, nombre, rol, password FROM usuarios WHERE usuario LIKE ? OR nombre LIKE ? ORDER BY usuario");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare("SELECT id, usuario, nombre, rol, password FROM usuarios ORDER BY usuario");
}

$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = [
        'id' => $row['id'],
        'usuario' => $row['usuario'],
        'nombre' => $row['nombre'] ?? '',
        'rol' => $row['rol'],
        'password' => $row['password'] ?? ''
    ];
}

header('Content-Type: application/json');

// Manejar POST para agregar nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $usuario = $input['usuario'] ?? '';
    $nombre = $input['nombre'] ?? '';
    $rol = $input['rol'] ?? 'operador';
    $password = '123456'; // password por defecto para modo prueba

    if (empty($usuario) || empty($nombre)) {
        http_response_code(400);
        echo json_encode(['error' => 'Usuario y nombre son requeridos']);
        exit();
    }

    if (strlen($password) < 4) {
        http_response_code(400);
        echo json_encode(['error' => 'La contraseña debe tener al menos 4 caracteres']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, nombre, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $usuario, $password, $nombre, $rol);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Error al insertar: ' . $conn->error]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

// Manejar DELETE para eliminar usuario por ID
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de usuario requerido']);
        exit();
    }

    // Verificar que no se elimine el admin principal (opcional, por seguridad)
    $stmtCheck = $conn->prepare("SELECT usuario FROM usuarios WHERE id = ?");
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    $row = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit();
    }

    $stmtDel = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmtDel->bind_param("i", $id);

    if ($stmtDel->execute()) {
        echo json_encode(['success' => true, 'usuario' => $row['usuario'], 'id' => $id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar: ' . $conn->error]);
    }
    $stmtDel->close();
    $conn->close();
    exit();
}

// Manejar PUT para actualizar usuario (incluyendo contraseña)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $usuario = $input['usuario'] ?? '';
    $nombre = $input['nombre'] ?? '';
    $rol = $input['rol'] ?? 'operador';
    $password = $input['password'] ?? null;

    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de usuario requerido']);
        exit();
    }

    if (empty($usuario) || empty($nombre)) {
        http_response_code(400);
        echo json_encode(['error' => 'Usuario y nombre son requeridos']);
        exit();
    }

    // Verificar que el usuario exista
    $stmtCheck = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    $stmtCheck->bind_param("i", $id);
    $stmtCheck->execute();
    if ($stmtCheck->get_result()->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit();
    }
    $stmtCheck->close();

    if ($password !== null && $password !== '') {
        if (strlen($password) < 4) {
            http_response_code(400);
            echo json_encode(['error' => 'La contraseña debe tener al menos 4 caracteres']);
            exit();
        }
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, nombre = ?, rol = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $usuario, $nombre, $rol, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET usuario = ?, nombre = ?, rol = ? WHERE id = ?");
        $stmt->bind_param("sssi", $usuario, $nombre, $rol, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $id]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Error al actualizar: ' . $conn->error]);
    }
    $stmt->close();
    $conn->close();
    exit();
}

echo json_encode($usuarios);

$stmt->close();
$conn->close();