-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_votacion_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_votacion_pro;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    documento_identidad VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    codigo_verificacion VARCHAR(100) DEFAULT NULL,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    verificado TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME NOT NULL,
    ultimo_login DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de roles de administrador
CREATE TABLE IF NOT EXISTS roles_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar roles por defecto
INSERT INTO roles_admin (id, nombre, descripcion) 
VALUES 
(1, 'Super Administrador', 'Control total del sistema, incluida la personalización visual y configuración general'),
(2, 'Administrador', 'Gestión de elecciones, candidatos y visualización de resultados');

-- Tabla de administradores (ahora con campo rol_id)
CREATE TABLE IF NOT EXISTS administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL DEFAULT 2,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME NOT NULL,
    ultimo_login DATETIME DEFAULT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles_admin(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear administrador por defecto como Super Administrador (contraseña: admin123)
INSERT INTO administradores (nombre, apellido, username, email, password, rol_id, activo, fecha_registro)
VALUES ('Admin', 'Sistema', 'admin', 'admin@sistema.com', '$2y$10$8tDjLmH1EgQzOEG9jHnUWuQB6YhP.YS9TcF9.TTQtGMOmqHK4LEoO', 1, 1, NOW());

-- Tabla de elecciones
CREATE TABLE IF NOT EXISTS elecciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de candidatos
CREATE TABLE IF NOT EXISTS candidatos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    biografia TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    eleccion_id INT NOT NULL,
    numero INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME NOT NULL,
    FOREIGN KEY (eleccion_id) REFERENCES elecciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de votos
CREATE TABLE IF NOT EXISTS votos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    candidato_id INT NOT NULL,
    eleccion_id INT NOT NULL,
    fecha_voto DATETIME NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (candidato_id) REFERENCES candidatos(id) ON DELETE CASCADE,
    FOREIGN KEY (eleccion_id) REFERENCES elecciones(id) ON DELETE CASCADE,
    UNIQUE KEY unique_voto (usuario_id, eleccion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de configuración del sistema
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_sistema VARCHAR(100) NOT NULL DEFAULT 'Sistema de Votación Pro',
    logo VARCHAR(255) DEFAULT NULL,
    color_primario VARCHAR(20) DEFAULT '#3498db',
    color_secundario VARCHAR(20) DEFAULT '#2ecc71',
    color_texto VARCHAR(20) DEFAULT '#333333',
    footer_texto TEXT DEFAULT 'Sistema de Votación Pro © 2023',
    ultima_actualizacion DATETIME NOT NULL,
    actualizado_por INT NOT NULL,
    FOREIGN KEY (actualizado_por) REFERENCES administradores(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar configuración inicial del sistema
INSERT INTO configuracion_sistema (nombre_sistema, color_primario, color_secundario, color_texto, footer_texto, ultima_actualizacion, actualizado_por)
VALUES ('Sistema de Votación Pro', '#3498db', '#2ecc71', '#333333', 'Sistema de Votación Pro © 2023', NOW(), 1);

-- Añadir tabla para códigos secretos de acceso
CREATE TABLE IF NOT EXISTS codigos_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rol_id INT NOT NULL,
    codigo VARCHAR(255) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME NOT NULL,
    ultima_modificacion DATETIME DEFAULT NULL,
    FOREIGN KEY (rol_id) REFERENCES roles_admin(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar código secreto para Super Administrador (código: MASTER2023)
INSERT INTO codigos_acceso (rol_id, codigo, activo, fecha_creacion)
VALUES (1, '$2y$10$8tDjLmH1EgQzOEG9jHnUWuQB6YhP.YS9TcF9.TTQtGMOmqHK4LEoO', 1, NOW());