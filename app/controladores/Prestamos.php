<?php

require_once __DIR__ . "/../modelos/PrestamoModelo.php";
require_once __DIR__ . "/../modelos/UsuarioModelo.php";
require_once __DIR__ . "/../modelos/LibroModelo.php";

class Prestamos extends Controlador
{
    public function index()
    {
        $filtro = (string) ($_GET["filtro"] ?? "activos");
        if (!in_array($filtro, ["activos", "vencidos", "historial"], true)) {
            $filtro = "activos";
        }

        $modelo = new PrestamoModelo($this->db);
        $prestamos = $modelo->obtenerPorFiltro($filtro);
        $conteos = $modelo->conteos();
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/prestamosVista.php";
    }

    public function crear()
    {
        $datos = [
            "id_usuario" => (int) ($_GET["usuario"] ?? 0),
            "id_libro" => (int) ($_GET["libro"] ?? 0),
            "fecha_limite" => date("Y-m-d", strtotime("+7 days")),
        ];

        $this->mostrarFormulario($datos, [], 200);
    }

    public function guardar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $datos = [
            "id_usuario" => (int) ($_POST["id_usuario"] ?? 0),
            "id_libro" => (int) ($_POST["id_libro"] ?? 0),
            "fecha_limite" => trim((string) ($_POST["fecha_limite"] ?? "")),
        ];

        $errores = [];
        if ($datos["id_usuario"] <= 0) {
            $errores["id_usuario"] = "Selecciona un usuario.";
        }
        if ($datos["id_libro"] <= 0) {
            $errores["id_libro"] = "Selecciona un libro disponible.";
        }

        $fecha = DateTime::createFromFormat("Y-m-d", $datos["fecha_limite"]);
        if (!$fecha || $fecha->format("Y-m-d") !== $datos["fecha_limite"]) {
            $errores["fecha_limite"] = "Selecciona una fecha válida.";
        } elseif ($fecha < new DateTime("today")) {
            $errores["fecha_limite"] = "La fecha límite no puede ser anterior a hoy.";
        }

        if (!$errores) {
            try {
                $modelo = new PrestamoModelo($this->db);
                $modelo->crear(
                    $datos["id_usuario"],
                    $datos["id_libro"],
                    $datos["fecha_limite"] . " 23:59:59"
                );
                $this->flash("exito", "El préstamo se registró correctamente.");
                $this->redirigir("/prestamos/index");
            } catch (RuntimeException $e) {
                $errores["general"] = $e->getMessage();
            }
        }

        $this->mostrarFormulario($datos, $errores, 422);
    }

    public function devolver()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $idDetalle = (int) ($_POST["id_detalle"] ?? 0);

        try {
            $modelo = new PrestamoModelo($this->db);
            $modelo->devolver($idDetalle);
            $this->flash("exito", "La devolución se registró correctamente.");
        } catch (RuntimeException $e) {
            $this->flash("error", $e->getMessage());
        }

        $this->redirigir("/prestamos/index");
    }

    private function mostrarFormulario($datos, $errores, $codigo)
    {
        $usuarioModelo = new UsuarioModelo($this->db);
        $libroModelo = new LibroModelo($this->db);
        $usuarios = $usuarioModelo->obtenerTodos();
        $libros = $libroModelo->disponibles();
        $accion = $this->url("/prestamos/guardar");
        $csrfToken = $this->tokenCsrf();

        http_response_code($codigo);
        require __DIR__ . "/../vistas/prestamoFormVista.php";
    }
}
