<?php

class Controlador
{
    protected $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }
}
