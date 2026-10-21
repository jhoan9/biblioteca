<?php

class PrestamoModelo
{
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    public function obtenerPorFiltro($filtro = "activos")
    {
        $condiciones = [
            "activos" => "p.estado = 'activo'
                          AND d.fecha_devolucion_real IS NULL
                          AND d.fecha_limite_devolucion >= NOW()",
            "vencidos" => "p.estado = 'activo'
                           AND d.fecha_devolucion_real IS NULL
                           AND d.fecha_limite_devolucion < NOW()",
            "historial" => "p.estado = 'devuelto'",
        ];

        $where = $condiciones[$filtro] ?? $condiciones["activos"];

        $consulta = $this->db->getConexion()->query(
            "SELECT p.id_prestamo, p.estado, p.fecha_prestamo, p.fecha_devolucion,
                    u.id_usuario, u.nombre_usuario, u.apellido_usuario, u.correo_usuario,
                    l.id_libro, l.titulo, l.autor, l.isbn,
                    d.id_detalle, d.fecha_limite_devolucion, d.fecha_devolucion_real
             FROM prestamo p
             INNER JOIN usuario u ON u.id_usuario = p.id_usuario
             INNER JOIN detalle_prestamo d ON d.id_prestamo = p.id_prestamo
             INNER JOIN libro l ON l.id_libro = d.id_libro
             WHERE {$where}
             ORDER BY d.fecha_limite_devolucion ASC, p.fecha_prestamo DESC"
        );

        return $consulta->fetchAll();
    }

    public function recientes($limite = 6)
    {
        $limite = max(1, (int) $limite);

        $consulta = $this->db->getConexion()->query(
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
        );

        return $consulta->fetchAll();
    }

    public function conteos()
    {
        $conexion = $this->db->getConexion();
        $base = "FROM detalle_prestamo d
                 INNER JOIN prestamo p ON p.id_prestamo = d.id_prestamo
                 WHERE ";

        return [
            "activos" => (int) $conexion->query(
                "SELECT COUNT(*) {$base} p.estado = 'activo'
                 AND d.fecha_devolucion_real IS NULL
                 AND d.fecha_limite_devolucion >= NOW()"
            )->fetchColumn(),
            "vencidos" => (int) $conexion->query(
                "SELECT COUNT(*) {$base} p.estado = 'activo'
                 AND d.fecha_devolucion_real IS NULL
                 AND d.fecha_limite_devolucion < NOW()"
            )->fetchColumn(),
            "historial" => (int) $conexion->query(
                "SELECT COUNT(*) {$base} p.estado = 'devuelto'"
            )->fetchColumn(),
        ];
    }

    public function crear($idUsuario, $idLibro, $fechaLimite)
    {
        $conexion = $this->db->getConexion();

        try {
            $conexion->beginTransaction();

            $consultaUsuario = $conexion->prepare(
                "SELECT 1 FROM usuario WHERE id_usuario = :id LIMIT 1"
            );
            $consultaUsuario->execute(["id" => (int) $idUsuario]);
            if ($consultaUsuario->fetchColumn() === false) {
                throw new RuntimeException("El usuario seleccionado no existe.");
            }

            $consultaLibro = $conexion->prepare(
                "SELECT disponible FROM libro WHERE id_libro = :id FOR UPDATE"
            );
            $consultaLibro->execute(["id" => (int) $idLibro]);
            $disponible = $consultaLibro->fetchColumn();

            if ($disponible === false) {
                throw new RuntimeException("El libro seleccionado no existe.");
            }
            if ((int) $disponible !== 1) {
                throw new RuntimeException("Ese libro ya no está disponible para préstamo.");
            }

            $prestamo = $conexion->prepare(
                "INSERT INTO prestamo (id_usuario, estado) VALUES (:usuario, 'activo')"
            );
            $prestamo->execute(["usuario" => (int) $idUsuario]);
            $idPrestamo = (int) $conexion->lastInsertId();

            $detalle = $conexion->prepare(
                "INSERT INTO detalle_prestamo (id_prestamo, id_libro, fecha_limite_devolucion)
                 VALUES (:prestamo, :libro, :limite)"
            );
            $detalle->execute([
                "prestamo" => $idPrestamo,
                "libro" => (int) $idLibro,
                "limite" => $fechaLimite,
            ]);

            $actualizar = $conexion->prepare(
                "UPDATE libro SET disponible = 0 WHERE id_libro = :id"
            );
            $actualizar->execute(["id" => (int) $idLibro]);

            $conexion->commit();
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }

        return $idPrestamo;
    }

    public function devolver($idDetalle)
    {
        $conexion = $this->db->getConexion();

        try {
            $conexion->beginTransaction();

            $consulta = $conexion->prepare(
                "SELECT id_prestamo, id_libro, fecha_devolucion_real
                 FROM detalle_prestamo
                 WHERE id_detalle = :id
                 FOR UPDATE"
            );
            $consulta->execute(["id" => (int) $idDetalle]);
            $detalle = $consulta->fetch();

            if (!$detalle) {
                throw new RuntimeException("El préstamo indicado no existe.");
            }
            if ($detalle["fecha_devolucion_real"] !== null) {
                throw new RuntimeException("Este préstamo ya fue devuelto.");
            }

            $conexion->prepare(
                "UPDATE detalle_prestamo
                 SET fecha_devolucion_real = NOW()
                 WHERE id_detalle = :id"
            )->execute(["id" => (int) $idDetalle]);

            $conexion->prepare(
                "UPDATE libro SET disponible = 1 WHERE id_libro = :id"
            )->execute(["id" => (int) $detalle["id_libro"]]);

            $conexion->prepare(
                "UPDATE prestamo
                 SET estado = 'devuelto', fecha_devolucion = NOW()
                 WHERE id_prestamo = :id"
            )->execute(["id" => (int) $detalle["id_prestamo"]]);

            $conexion->commit();
        } catch (Throwable $e) {
            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }
            throw $e;
        }
    }
}
