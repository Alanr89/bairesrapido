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
    CREATE TABLE IF NOT EXISTS moviles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        marca VARCHAR(100) NOT NULL,
        modelo VARCHAR(100) NOT NULL,
        patente VARCHAR(20) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $stmt = $conn->prepare("SELECT * FROM moviles WHERE marca LIKE ? OR modelo LIKE ? OR patente LIKE ? ORDER BY patente ASC");
        $like = "%$search%";
        $stmt->bind_param("sss", $like, $like, $like);
    } else {
        $stmt = $conn->prepare("SELECT * FROM moviles ORDER BY patente ASC");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $marca = $input['marca'] ?? '';
    $modelo = $input['modelo'] ?? '';
    $patente = $input['patente'] ?? '';
    if (empty($marca) || empty($modelo) || empty($patente)) {
        http_response_code(400); echo json_encode(['error' => 'Marca, modelo y patente requeridos']); exit();
    }
    $stmt = $conn->prepare("INSERT INTO moviles (marca, modelo, patente) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $marca, $modelo, $patente);
    if ($stmt->execute()) { echo json_encode(['success' => true, 'id' => $stmt->insert_id]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $marca = $input['marca'] ?? '';
    $modelo = $input['modelo'] ?? '';
    $patente = $input['patente'] ?? '';
    $stmt = $conn->prepare("UPDATE moviles SET marca = ?, modelo = ?, patente = ? WHERE id = ?");
    $stmt->bind_param("sssi", $marca, $modelo, $patente, $id);
    if ($stmt->execute()) { echo json_encode(['success' => true]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? 0;
    $stmt = $conn->prepare("DELETE FROM moviles WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) { echo json_encode(['success' => true]); }
    else { http_response_code(400); echo json_encode(['error' => $conn->error]); }
}
$conn->close();
?>