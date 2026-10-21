<?php

require_once __DIR__ . "/../modelos/LibroModelo.php";

class Libros extends Controlador
{
    public function index()
    {
        $busqueda = trim((string) ($_GET["q"] ?? ""));
        $estado = (string) ($_GET["estado"] ?? "todos");
        if (!in_array($estado, ["todos", "disponibles", "prestados"], true)) {
            $estado = "todos";
        }

        $modelo = new LibroModelo($this->db);
        $libros = $modelo->obtenerTodos($busqueda, $estado);
        $total = $modelo->contar();
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/librosVista.php";
    }

    public function crear()
    {
        $datos = ["nombre" => "", "titulo" => "", "autor" => "", "isbn" => ""];
        $errores = [];
        $modo = "crear";
        $accion = $this->url("/libros/guardar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/libroFormVista.php";
    }

    public function guardar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        [$datos, $errores] = $this->validar();

        $modelo = new LibroModelo($this->db);
        if (!$errores && $modelo->existeIsbn($datos["isbn"])) {
            $errores["isbn"] = "Ya existe un libro registrado con ese ISBN.";
        }

        if (!$errores) {
            try {
                $modelo->crear($datos);
                $this->flash("exito", "El libro se registró correctamente.");
                $this->redirigir("/libros/index");
            } catch (PDOException $e) {
                if ($e->getCode() !== "23000") {
                    throw $e;
                }
                $errores["isbn"] = "Ya existe un libro registrado con ese ISBN.";
            }
        }

        http_response_code(422);
        $modo = "crear";
        $accion = $this->url("/libros/guardar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/libroFormVista.php";
    }

    public function editar($id = null)
    {
        $modelo = new LibroModelo($this->db);
        $libro = $modelo->obtenerPorId((int) $id);

        if (!$libro) {
            $this->flash("error", "El libro solicitado no existe.");
            $this->redirigir("/libros/index");
        }

        $datos = $libro;
        $errores = [];
        $modo = "editar";
        $accion = $this->url("/libros/actualizar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/libroFormVista.php";
    }

    public function actualizar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $id = (int) ($_POST["id"] ?? 0);
        $modelo = new LibroModelo($this->db);
        $libro = $modelo->obtenerPorId($id);

        if (!$libro) {
            $this->flash("error", "El libro solicitado no existe.");
            $this->redirigir("/libros/index");
        }

        [$datos, $errores] = $this->validar();
        $datos["id_libro"] = $id;

        if (!$errores && $modelo->existeIsbn($datos["isbn"], $id)) {
            $errores["isbn"] = "Ya existe otro libro registrado con ese ISBN.";
        }

        if (!$errores) {
            try {
                $modelo->actualizar($id, $datos);
                $this->flash("exito", "El libro se actualizó correctamente.");
                $this->redirigir("/libros/index");
            } catch (PDOException $e) {
                if ($e->getCode() !== "23000") {
                    throw $e;
                }
                $errores["isbn"] = "Ya existe otro libro registrado con ese ISBN.";
            }
        }

        http_response_code(422);
        $modo = "editar";
        $accion = $this->url("/libros/actualizar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/libroFormVista.php";
    }

    public function eliminar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $id = (int) ($_POST["id"] ?? 0);
        $modelo = new LibroModelo($this->db);
        $libro = $modelo->obtenerPorId($id);

        if (!$libro) {
            $this->flash("error", "El libro solicitado no existe.");
            $this->redirigir("/libros/index");
        }

        if ($modelo->tienePrestamos($id)) {
            $this->flash("error", "No puedes eliminar un libro con préstamos registrados.");
            $this->redirigir("/libros/index");
        }

        $modelo->eliminar($id);
        $this->flash("exito", "El libro se eliminó del catálogo.");
        $this->redirigir("/libros/index");
    }

    private function validar()
    {
        $datos = [];
        foreach (["nombre", "titulo", "autor", "isbn"] as $campo) {
            $valor = $_POST[$campo] ?? "";
            $datos[$campo] = is_string($valor) ? trim($valor) : "";
        }

        $limites = [
            "nombre" => 250,
            "titulo" => 250,
            "autor" => 250,
            "isbn" => 100,
        ];

        $errores = [];
        foreach ($limites as $campo => $limite) {
            if ($datos[$campo] === "") {
                $errores[$campo] = "Este campo es obligatorio.";
            } elseif ($this->longitud($datos[$campo]) > $limite) {
                $errores[$campo] = "Este campo no puede superar {$limite} caracteres.";
            }
        }

        return [$datos, $errores];
    }
}
