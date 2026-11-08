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

// Crear tabla si no existe
$conn->query("
    CREATE TABLE IF NOT EXISTS operarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        apellido VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT * FROM operarios WHERE nombre LIKE ? OR apellido LIKE ? ORDER BY apellido ASC");
        $like = "%$search%";
        $stmt->bind_param("ss", $like, $like);
    } else {
        $stmt = $conn->prepare("SELECT * FROM operarios ORDER BY apellido ASC");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $nombre = $input['nombre'] ?? '';
    $apellido = $input['apellido'] ?? '';
    if (empty($nombre) || empty($apellido)) {
        http_response_code(400); echo json_encode(['error' => 'Nombre y apellido requeridos']); exit();
    }
    $stmt = $conn->prepare("INSERT INTO operarios (nombre, apellido) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $apellido);
    if ($stmt->execute()) { echo json_encode(['success' => true, 'id' => $stmt->insert_id]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $nombre = $input['nombre'] ?? '';
    $apellido = $input['apellido'] ?? '';
    $stmt = $conn->prepare("UPDATE operarios SET nombre = ?, apellido = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nombre, $apellido, $id);
    if ($stmt->execute()) { echo json_encode(['success' => true]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM operarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) { echo json_encode(['success' => true]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}
$conn->close();
?>