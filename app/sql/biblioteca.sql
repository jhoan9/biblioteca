
CREATE DATABASE IF NOT EXISTS biblioteca
    CHARACTER SET utf8
    COLLATE utf8_spanish_ci;

USE biblioteca;

-- =========================================
-- TABLA: usuario
-- =========================================

CREATE TABLE usuario (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    correo_usuario VARCHAR(150) NOT NULL,
    nombre_usuario VARCHAR(200) NOT NULL,
    apellido_usuario VARCHAR(200) NOT NULL,
    telefono_usuario VARCHAR(50) NOT NULL,

    PRIMARY KEY (id_usuario),
    UNIQUE KEY uk_usuario_correo (correo_usuario)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- =========================================
-- TABLA: libro
-- =========================================

CREATE TABLE libro (
    id_libro INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(250) NOT NULL,
    titulo VARCHAR(250) NOT NULL,
    autor VARCHAR(250) NOT NULL,
    isbn VARCHAR(100) NOT NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (id_libro),
    UNIQUE KEY uk_libro_isbn (isbn),
    CONSTRAINT chk_libro_disponible
        CHECK (disponible IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- =========================================
-- TABLA: prestamo
-- Cada préstamo pertenece a un usuario
-- =========================================

CREATE TABLE prestamo (
    id_prestamo INT NOT NULL AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha_prestamo DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_devolucion DATETIME DEFAULT NULL,
    estado ENUM(
        'activo',
        'devuelto',
        'vencido'
    ) NOT NULL DEFAULT 'activo',

    PRIMARY KEY (id_prestamo),

    KEY idx_prestamo_usuario (id_usuario),

    CONSTRAINT fk_prestamo_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuario (id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;


-- =========================================
-- TABLA: detalle_prestamo
-- Relaciona los libros con cada préstamo
-- =========================================

CREATE TABLE detalle_prestamo (
    id_detalle INT NOT NULL AUTO_INCREMENT,
    id_prestamo INT NOT NULL,
    id_libro INT NOT NULL,
    fecha_limite_devolucion DATETIME NOT NULL,
    fecha_devolucion_real DATETIME DEFAULT NULL,

    PRIMARY KEY (id_detalle),

    -- Un mismo libro no se repite dentro
    -- del mismo préstamo
    UNIQUE KEY uk_prestamo_libro (
        id_prestamo,
        id_libro
    ),

    KEY idx_detalle_libro (id_libro),

    CONSTRAINT fk_detalle_prestamo
        FOREIGN KEY (id_prestamo)
        REFERENCES prestamo (id_prestamo)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_libro
        FOREIGN KEY (id_libro)
        REFERENCES libro (id_libro)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_general_ci;
