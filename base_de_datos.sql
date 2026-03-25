CREATE DATABASE IF NOT EXISTS crud_biblioteca_original;
USE crud_biblioteca_original;


-- Tablas y registros para usuarios y roles 
/* TABLA: roles
   Guardamos los tipos de usuario (Administrador, Lector, etc.) */
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);

/* TABLA: usuarios
   Guardamos la informacion completa del usuario y credenciales 
   con medidas de seguridad (intentos_fallidos, tokens) */
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, /* Espacio para hash Bcrypt */
    id_rol INT NOT NULL,
    intentos_fallidos INT DEFAULT 0,
    bloqueado_hasta DATETIME NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

/* DATOS DE PRUEBA - ROLES */
INSERT INTO roles (nombre_rol) VALUES
('Administrador'),
('Lector');

/* DATOS DE PRUEBA - USUARIOS
   Las contraseñas generadas aquí son '1234567' (Pre-Encriptadas en Bcrypt para que el login las procese) */
INSERT INTO usuarios (nombre_completo, correo, password, id_rol) VALUES
('Juan Perez', 'admin@ejemplo.com', '$2y$10$k2ONcb8ju7HysxpQjjaaOO7RS5QORW7sTN5m0attYx8KuDFZiYgSO', 1),
('Maria Lopez', 'lector@ejemplo.com', '$2y$10$k2ONcb8ju7HysxpQjjaaOO7RS5QORW7sTN5m0attYx8KuDFZiYgSO', 2);



-- Tablas y registros para el CRUD del hotel 
/* TABLA: lectores */
CREATE TABLE lectores (
    id_lector INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT DEFAULT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

/* TABLA: libros */
CREATE TABLE libros (
    id_libro INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL UNIQUE,
    autor VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    estado ENUM('Disponible', 'Ocupada', 'Mantenimiento') DEFAULT 'Disponible'
);

/* TABLA: prestamos */
CREATE TABLE prestamos (
    id_prestamo INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    id_lector INT NOT NULL,
    id_libro INT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE NOT NULL,
    estado ENUM('Confirmada', 'Pendiente', 'Cancelada') DEFAULT 'Pendiente',
    FOREIGN KEY (id_lector) REFERENCES lectores(id_lector) ON DELETE CASCADE,
    FOREIGN KEY (id_libro) REFERENCES libros(id_libro) ON DELETE CASCADE
);

/* DATOS DE PRUEBA - CLIENTES */
INSERT INTO lectores (nombre_completo, telefono, estado) VALUES
('Lector 1', '555-0192', 'Activo'),
('Lector 2', '555-3841', 'Activo'),
('Lector 3', '555-7734', 'Inactivo');

/* DATOS DE PRUEBA - LIBROS */
INSERT INTO libros (titulo, autor, precio, estado) VALUES
('El Principito', 'Antoine de Saint-Exupéry', 120.50, 'Disponible'),
('1984', 'George Orwell', 250.00, 'Ocupada'),
('Cien Años de Soledad', 'Gabriel García Márquez', 340.00, 'Mantenimiento'),
('Don Quijote de la Mancha', 'Miguel de Cervantes', 410.00, 'Disponible');

/* DATOS DE PRUEBA - PRESTAMOS */
INSERT INTO prestamos (codigo, id_lector, id_libro, fecha_prestamo, fecha_devolucion, estado) VALUES
('PRE-1928', 1, 2, '2026-03-20', '2026-03-25', 'Confirmada'),
('PRE-5544', 2, 3, '2026-04-10', '2026-04-15', 'Pendiente');

SELECT * FROM roles;
SELECT * FROM usuarios;
SELECT * FROM lectores;
SELECT * FROM libros;
SELECT * FROM prestamos;
