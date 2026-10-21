<?php

class LibroModelo
{
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos($busqueda = "", $estado = "todos")
    {
        $condiciones = [];
        $parametros = [];

        if ($busqueda !== "") {
            $condiciones[] = "(titulo LIKE :q1 OR autor LIKE :q2 OR isbn LIKE :q3 OR nombre LIKE :q4)";
            $termino = "%" . $busqueda . "%";
            $parametros["q1"] = $termino;
            $parametros["q2"] = $termino;
            $parametros["q3"] = $termino;
            $parametros["q4"] = $termino;
        }

        if ($estado === "disponibles") {
            $condiciones[] = "disponible = 1";
        } elseif ($estado === "prestados") {
            $condiciones[] = "disponible = 0";
        }

        $sql = "SELECT id_libro, nombre, titulo, autor, isbn, disponible FROM libro";
        if ($condiciones) {
            $sql .= " WHERE " . implode(" AND ", $condiciones);
        }
        $sql .= " ORDER BY titulo ASC, id_libro ASC";

        $consulta = $this->db->getConexion()->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    public function disponibles()
    {
        $consulta = $this->db->getConexion()->query(
            "SELECT id_libro, titulo, autor, isbn
             FROM libro
             WHERE disponible = 1
             ORDER BY titulo ASC"
        );

        return $consulta->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "SELECT id_libro, nombre, titulo, autor, isbn, disponible
             FROM libro
             WHERE id_libro = :id
             LIMIT 1"
        );
        $consulta->execute(["id" => (int) $id]);

        return $consulta->fetch();
    }

    public function existeIsbn($isbn, $exceptoId = null)
    {
        $sql = "SELECT 1 FROM libro WHERE isbn = :isbn";
        $parametros = ["isbn" => $isbn];

        if ($exceptoId !== null) {
            $sql .= " AND id_libro <> :excepto";
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
            "INSERT INTO libro (nombre, titulo, autor, isbn)
             VALUES (:nombre, :titulo, :autor, :isbn)"
        );

        return $consulta->execute([
            "nombre" => $datos["nombre"],
            "titulo" => $datos["titulo"],
            "autor" => $datos["autor"],
            "isbn" => $datos["isbn"],
        ]);
    }

    public function actualizar($id, $datos)
    {
        $consulta = $this->db->getConexion()->prepare(
            "UPDATE libro
             SET nombre = :nombre, titulo = :titulo, autor = :autor, isbn = :isbn
             WHERE id_libro = :id"
        );

        return $consulta->execute([
            "id" => (int) $id,
            "nombre" => $datos["nombre"],
            "titulo" => $datos["titulo"],
            "autor" => $datos["autor"],
            "isbn" => $datos["isbn"],
        ]);
    }

    public function eliminar($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "DELETE FROM libro WHERE id_libro = :id"
        );

        return $consulta->execute(["id" => (int) $id]);
    }

    public function tienePrestamos($id)
    {
        $consulta = $this->db->getConexion()->prepare(
            "SELECT 1 FROM detalle_prestamo WHERE id_libro = :id LIMIT 1"
        );
        $consulta->execute(["id" => (int) $id]);

        return $consulta->fetchColumn() !== false;
    }

    public function contar()
    {
        return (int) $this->db->getConexion()
            ->query("SELECT COUNT(*) FROM libro")
            ->fetchColumn();
    }
}
