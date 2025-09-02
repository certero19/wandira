-- Base de datos para BeTravel
-- Ejecutar este script en phpMyAdmin para crear la base de datos y tablas

CREATE DATABASE IF NOT EXISTS betravel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE betravel_db;

-- Tabla para contactos
CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    asunto ENUM('consulta_general', 'reserva_vuelo', 'paquete_turistico', 'hotel', 'soporte', 'otro') NOT NULL,
    mensaje TEXT NOT NULL,
    newsletter BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('nuevo', 'en_proceso', 'resuelto') DEFAULT 'nuevo',
    INDEX idx_email (email),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_estado (estado)
);

-- Tabla para destinos favoritos
CREATE TABLE destinos_favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_ip VARCHAR(45) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_destination (usuario_ip, destino),
    INDEX idx_usuario (usuario_ip),
    INDEX idx_destino (destino)
);

-- Tabla para reservas de paquetes
CREATE TABLE reservas_paquetes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100) NOT NULL,
    telefono_cliente VARCHAR(20),
    paquete VARCHAR(100) NOT NULL,
    precio VARCHAR(20) NOT NULL,
    fecha_viaje DATE,
    numero_personas INT DEFAULT 1,
    comentarios TEXT,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    INDEX idx_email (email_cliente),
    INDEX idx_fecha_reserva (fecha_reserva),
    INDEX idx_estado (estado)
);

-- Tabla para preferencias de usuarios
CREATE TABLE preferencias_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_ip VARCHAR(45) NOT NULL,
    nombre_usuario VARCHAR(100),
    tipo_destino_preferido VARCHAR(50),
    presupuesto_preferido ENUM('bajo', 'medio', 'alto'),
    intereses TEXT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_ip (usuario_ip),
    INDEX idx_usuario (usuario_ip)
);

-- Tabla para estadísticas de la web
CREATE TABLE estadisticas_web (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pagina VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    detalles TEXT,
    usuario_ip VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pagina (pagina),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (usuario_ip)
);

-- Insertar algunos datos de ejemplo
INSERT INTO contactos (nombre, email, telefono, asunto, mensaje, newsletter) VALUES
('Juan Pérez', 'juan@example.com', '+1234567890', 'consulta_general', 'Me interesa conocer más sobre sus paquetes turísticos a Europa.', TRUE),
('María García', 'maria@example.com', '+0987654321', 'reserva_vuelo', 'Necesito reservar un vuelo a París para el próximo mes.', FALSE),
('Carlos López', 'carlos@example.com', '+1122334455', 'paquete_turistico', 'Quiero información sobre el paquete a Maldivas.', TRUE);

INSERT INTO destinos_favoritos (usuario_ip, destino) VALUES
('192.168.1.100', 'París'),
('192.168.1.100', 'Maldivas'),
('192.168.1.101', 'Tokio'),
('192.168.1.102', 'Barcelona');

INSERT INTO reservas_paquetes (nombre_cliente, email_cliente, telefono_cliente, paquete, precio, fecha_viaje, numero_personas, comentarios) VALUES
('Ana Martínez', 'ana@example.com', '+5566778899', 'Paquete Europa Clásica', 'Desde $2,499 USD', '2025-06-15', 2, 'Viaje de luna de miel'),
('Roberto Silva', 'roberto@example.com', '+4433221100', 'Paquete Caribe Paradisíaco', 'Desde $1,899 USD', '2025-07-20', 4, 'Viaje familiar');

INSERT INTO preferencias_usuarios (usuario_ip, nombre_usuario, tipo_destino_preferido, presupuesto_preferido, intereses) VALUES
('192.168.1.100', 'Usuario Demo', 'playa', 'medio', 'Relax, gastronomía, cultura'),
('192.168.1.101', 'Viajero Aventurero', 'montaña', 'alto', 'Aventura, naturaleza, deportes');

-- Crear vistas útiles para reportes
CREATE VIEW vista_contactos_recientes AS
SELECT 
    id,
    nombre,
    email,
    asunto,
    DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha_formateada,
    estado
FROM contactos 
ORDER BY fecha_creacion DESC;

CREATE VIEW vista_destinos_populares AS
SELECT 
    destino,
    COUNT(*) as total_favoritos,
    COUNT(DISTINCT usuario_ip) as usuarios_unicos
FROM destinos_favoritos 
GROUP BY destino 
ORDER BY total_favoritos DESC;

CREATE VIEW vista_estadisticas_generales AS
SELECT 
    (SELECT COUNT(*) FROM contactos) as total_contactos,
    (SELECT COUNT(*) FROM destinos_favoritos) as total_favoritos,
    (SELECT COUNT(*) FROM reservas_paquetes) as total_reservas,
    (SELECT COUNT(DISTINCT usuario_ip) FROM preferencias_usuarios) as usuarios_registrados;
