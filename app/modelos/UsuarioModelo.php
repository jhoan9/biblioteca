<?php

class UsuarioModelo
{
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos($busqueda = "")
    {
        $sql = "SELECT u.id_usuario, u.correo_usuario, u.nombre_usuario, u.apellido_usuario,
                       u.telefono_usuario,
                       (SELECT COUNT(*) FROM prestamo p
                        WHERE p.id_usuario = u.id_usuario AND p.estado = 'activo') AS prestamos_activos
                FROM usuario u";
        $parametros = [];

        if ($busqueda !== "") {
            $sql .= " WHERE (u.nombre_usuario LIKE :q1
                         OR u.apellido_usuario LIKE :q2
                         OR u.correo_usuario LIKE :q3)";
            $termino = "%" . $busqueda . "%";
            $parametros["q1"] = $termino;
            $parametros["q2"] = $termino;
            $parametros["q3"] = $termino;
        }

        $sql .= " ORDER BY u.nombre_usuario ASC, u.apellido_usuario ASC";

        $consulta = $this->db->getConexion()->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "SELECT id_usuario, correo_usuario, nombre_usuario, apellido_usuario, telefono_usuario
             FROM usuario
             WHERE id_usuario = :id
             LIMIT 1"
        );
        $consulta->execute(["id" => (int) $id]);

        return $consulta->fetch();
    }

    public function existeCorreo($correo, $exceptoId = null)
    {
        $sql = "SELECT 1 FROM usuario WHERE correo_usuario = :correo";
        $parametros = ["correo" => $correo];

        if ($exceptoId !== null) {
            $sql .= " AND id_usuario <> :excepto";
            $parametros["excepto"] = (int) $exceptoId;
        }

        $sql .= " LIMIT 1";
        $consulta = $this->db->getConexion()->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchColumn() !== false;
    }

    public function crear($datos)
    {
        $consulta = $this->db->getConexion()->prepare(
            "INSERT INTO usuario (correo_usuario, nombre_usuario, apellido_usuario, telefono_usuario)
             VALUES (:correo, :nombre, :apellido, :telefono)"
        );

        $consulta->execute([
            "correo" => $datos["correo"],
            "nombre" => $datos["nombre"],
            "apellido" => $datos["apellido"],
            "telefono" => $datos["telefono"],
        ]);

        return (int) $this->db->getConexion()->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        $consulta = $this->db->getConexion()->prepare(
            "UPDATE usuario
             SET correo_usuario = :correo,
                 nombre_usuario = :nombre,
                 apellido_usuario = :apellido,
                 telefono_usuario = :telefono
             WHERE id_usuario = :id"
        );

        return $consulta->execute([
            "id" => (int) $id,
            "correo" => $datos["correo"],
            "nombre" => $datos["nombre"],
            "apellido" => $datos["apellido"],
            "telefono" => $datos["telefono"],
        ]);
    }

    public function eliminar($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "DELETE FROM usuario WHERE id_usuario = :id"
        );

        return $consulta->execute(["id" => (int) $id]);
    }

    public function tienePrestamos($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "SELECT 1 FROM prestamo WHERE id_usuario = :id LIMIT 1"
        );
        $consulta->execute(["id" => (int) $id]);

        return $consulta->fetchColumn() !== false;
    }

    public function contar()
    {
        return (int) $this->db->getConexion()
            ->query("SELECT COUNT(*) FROM usuario")
            ->fetchColumn();
    }
}
