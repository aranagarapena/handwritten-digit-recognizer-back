-- Crear la base de datos y seleccionarla
CREATE DATABASE IF NOT EXISTS exampleapp;
USE exampleapp;

-- Crear la tabla 'users' con los campos adicionales
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    apellido1 VARCHAR(255) NOT NULL,
    apellido2 VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    dni VARCHAR(9) NOT NULL UNIQUE,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_usuario CHAR(36),
    clave_usuario VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar datos de ejemplo en la tabla 'users'
INSERT INTO users (nombre, apellido1, apellido2, email, dni, username, password) VALUES
('Juan', 'Pérez', 'García', 'juan.perez@example.com', '12345678Z', 'juanperez', 'aB1!cD2@'),
('Ana', 'López', 'Fernández', 'ana.lopez@example.com', '23456789R', 'analopez', 'bC2@dE3#'),
('Carlos', 'Gómez', 'Martínez', 'carlos.gomez@example.com', '34567890W', 'carlosgomez', 'cD3#eF4$'),
('Marta', 'Ruiz', 'Díaz', 'marta.ruiz@example.com', '45678901A', 'martaruiz', 'dE4$fG5%'),
('David', 'Moreno', 'Jiménez', 'david.moreno@example.com', '56789012G', 'davidmoreno', 'eF5&gH6^');

-- Asegúrate de tener desactivado el modo seguro o usa la cláusula WHERE apropiada
-- Generar valores para 'id_usuario' y 'clave_usuario'
UPDATE users
SET id_usuario = UUID(), 
    clave_usuario = SHA1(CONCAT(email, RAND()))
WHERE id IS NOT NULL;


select * from users;
