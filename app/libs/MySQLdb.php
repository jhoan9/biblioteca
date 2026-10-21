<?php

class MySQLdb
{
    private $host = "localhost";
    private $usuario = "root";
    private $clave = "";
    private $db = "biblioteca";
    private $conn;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        $this->conn = new PDO($dsn, $this->usuario, $this->clave, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function getConexion()
    {
        return $this->conn;
    }
}