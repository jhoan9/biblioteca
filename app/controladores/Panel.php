<?php

require_once __DIR__ . "/../modelos/PanelModelo.php";
require_once __DIR__ . "/../modelos/PrestamoModelo.php";

class Panel extends Controlador
{
    public function index()
    {
        $modelo = new PanelModelo($this->db);
        $resumen = $modelo->resumen();
        $prestamos = $modelo->prestamosRecientes(6);
        $libros = $modelo->librosRecientes(5);

        require __DIR__ . "/../vistas/panelVista.php";
    }
}
