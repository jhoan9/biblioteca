<?php

class PanelModelo
{
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    public function resumen()
    {
        $conexion = $this->db->getConexion();
        $contar = function ($sql) use ($conexion) {
            return (int) $conexion->query($sql)->fetchColumn();
        };

        return [
            "libros" => $contar("SELECT COUNT(*) FROM libro"),
            "disponibles" => $contar("SELECT COUNT(*) FROM libro WHERE disponible = 1"),
            "prestados" => $contar("SELECT COUNT(*) FROM libro WHERE disponible = 0"),
            "usuarios" => $contar("SELECT COUNT(*) FROM usuario"),
            "activos" => $contar(
                "SELECT COUNT(*) FROM detalle_prestamo d
                 INNER JOIN prestamo p ON p.id_prestamo = d.id_prestamo
                 WHERE p.estado = 'activo' AND d.fecha_devolucion_real IS NULL
                 AND d.fecha_limite_devolucion >= NOW()"
            ),
            "vencidos" => $contar(
                "SELECT COUNT(*) FROM detalle_prestamo d
                 INNER JOIN prestamo p ON p.id_prestamo = d.id_prestamo
                 WHERE p.estado = 'activo' AND d.fecha_devolucion_real IS NULL
                 AND d.fecha_limite_devolucion < NOW()"
            ),
        ];
    }

    public function prestamosRecientes($limite = 6)
    {
        $limite = max(1, (int) $limite);

        return $this->db->getConexion()->query(
            "SELECT p.id_prestamo, p.estado, p.fecha_prestamo,
                    u.nombre_usuario, u.apellido_usuario,
                    l.titulo,
                    d.fecha_limite_devolucion, d.fecha_devolucion_real
             FROM prestamo p
             INNER JOIN usuario u ON u.id_usuario = p.id_usuario
             INNER JOIN detalle_prestamo d ON d.id_prestamo = p.id_prestamo
             INNER JOIN libro l ON l.id_libro = d.id_libro
             ORDER BY p.fecha_prestamo DESC
             LIMIT {$limite}"
        )->fetchAll();
    }

    public function librosRecientes($limite = 5)
    {
        $limite = max(1, (int) $limite);

        return $this->db->getConexion()->query(
            "SELECT id_libro, titulo, autor, disponible
             FROM libro
             ORDER BY id_libro DESC
             LIMIT {$limite}"
        )->fetchAll();
    }
}
