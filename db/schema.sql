CREATE DATABASE IF NOT EXISTS baires_rapido;
USE baires_rapido;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    rol ENUM('admin', 'operador') DEFAULT 'operador',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuarios de prueba
INSERT INTO usuarios (usuario, password, nombre, rol) 
VALUES ('admin', 'admin', 'Administrador', 'admin');

INSERT INTO usuarios (usuario, password, nombre, rol) 
VALUES ('op', 'op', 'Operador', 'operador');