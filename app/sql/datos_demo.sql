-- Datos de ejemplo opcionales.
-- Impórtalos desde phpMyAdmin si quieres probar los préstamos de inmediato.

USE biblioteca;

INSERT IGNORE INTO usuario (correo_usuario, nombre_usuario, apellido_usuario, telefono_usuario)
VALUES
    ('maria.gomez@example.com', 'María', 'Gómez', '+57 300 111 2233'),
    ('carlos.ruiz@example.com', 'Carlos', 'Ruiz', '+57 301 444 5566'),
    ('lucia.torres@example.com', 'Lucía', 'Torres', '+57 302 777 8899');
