CREATE DATABASE IF NOT EXISTS baires_rapido;
USE baires_rapido;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    rol ENUM('admin', 'operador', 'coordinador') DEFAULT 'operador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuario de prueba (password: admin123)
-- Nota: En un sistema real, las contraseñas deben estar hasheadas.
INSERT INTO usuarios (usuario, password, nombre, rol) 
VALUES ('admin', 'admin123', 'Administrador', 'admin');