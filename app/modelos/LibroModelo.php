<?php

class LibroModelo
{
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    public function obtenerTodos()
    {
        $consulta = $this->db->getConexion()->query(
            "SELECT id_libro, nombre, titulo, autor, isbn, disponible
             FROM libro
             ORDER BY titulo ASC, id_libro ASC"
        );

        return $consulta->fetchAll();
    }

    public function existeIsbn($isbn)
    {
        $consulta = $this->db->getConexion()->prepare(
            "SELECT 1 FROM libro WHERE isbn = :isbn LIMIT 1"
        );
        $consulta->execute(["isbn" => $isbn]);

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
}
