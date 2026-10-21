<?php

require_once __DIR__ . "/../modelos/UsuarioModelo.php";

class Usuarios extends Controlador
{
    public function index()
    {
        $busqueda = trim((string) ($_GET["q"] ?? ""));
        $modelo = new UsuarioModelo($this->db);
        $usuarios = $modelo->obtenerTodos($busqueda);
        $total = $modelo->contar();
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/usuariosVista.php";
    }

    public function crear()
    {
        $datos = ["nombre" => "", "apellido" => "", "correo" => "", "telefono" => ""];
        $errores = [];
        $modo = "crear";
        $accion = $this->url("/usuarios/guardar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/usuarioFormVista.php";
    }

    public function guardar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        [$datos, $errores] = $this->validar();

        $modelo = new UsuarioModelo($this->db);
        if (!$errores && $modelo->existeCorreo($datos["correo"])) {
            $errores["correo"] = "Ya existe un usuario con ese correo.";
        }

        if (!$errores) {
            try {
                $modelo->crear($datos);
                $this->flash("exito", "El usuario se registró correctamente.");
                $this->redirigir("/usuarios/index");
            } catch (PDOException $e) {
                if ($e->getCode() !== "23000") {
                    throw $e;
                }
                $errores["correo"] = "Ya existe un usuario con ese correo.";
            }
        }

        http_response_code(422);
        $modo = "crear";
        $accion = $this->url("/usuarios/guardar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/usuarioFormVista.php";
    }

    public function editar($id = null)
    {
        $modelo = new UsuarioModelo($this->db);
        $usuario = $modelo->obtenerPorId((int) $id);

        if (!$usuario) {
            $this->flash("error", "El usuario solicitado no existe.");
            $this->redirigir("/usuarios/index");
        }

        $datos = [
            "id_usuario" => $usuario["id_usuario"],
            "nombre" => $usuario["nombre_usuario"],
            "apellido" => $usuario["apellido_usuario"],
            "correo" => $usuario["correo_usuario"],
            "telefono" => $usuario["telefono_usuario"],
        ];
        $errores = [];
        $modo = "editar";
        $accion = $this->url("/usuarios/actualizar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/usuarioFormVista.php";
    }

    public function actualizar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $id = (int) ($_POST["id"] ?? 0);
        $modelo = new UsuarioModelo($this->db);
        $usuario = $modelo->obtenerPorId($id);

        if (!$usuario) {
            $this->flash("error", "El usuario solicitado no existe.");
            $this->redirigir("/usuarios/index");
        }

        [$datos, $errores] = $this->validar();
        $datos["id_usuario"] = $id;

        if (!$errores && $modelo->existeCorreo($datos["correo"], $id)) {
            $errores["correo"] = "Ya existe otro usuario con ese correo.";
        }

        if (!$errores) {
            try {
                $modelo->actualizar($id, $datos);
                $this->flash("exito", "El usuario se actualizó correctamente.");
                $this->redirigir("/usuarios/index");
            } catch (PDOException $e) {
                if ($e->getCode() !== "23000") {
                    throw $e;
                }
                $errores["correo"] = "Ya existe otro usuario con ese correo.";
            }
        }

        http_response_code(422);
        $modo = "editar";
        $accion = $this->url("/usuarios/actualizar");
        $csrfToken = $this->tokenCsrf();

        require __DIR__ . "/../vistas/usuarioFormVista.php";
    }

    public function eliminar()
    {
        $this->exigirPost();
        $this->exigirCsrf();

        $id = (int) ($_POST["id"] ?? 0);
        $modelo = new UsuarioModelo($this->db);
        $usuario = $modelo->obtenerPorId($id);

        if (!$usuario) {
            $this->flash("error", "El usuario solicitado no existe.");
            $this->redirigir("/usuarios/index");
        }

        if ($modelo->tienePrestamos($id)) {
            $this->flash("error", "No puedes eliminar un usuario con préstamos registrados.");
            $this->redirigir("/usuarios/index");
        }

        $modelo->eliminar($id);
        $this->flash("exito", "El usuario se eliminó correctamente.");
        $this->redirigir("/usuarios/index");
    }

    private function validar()
    {
        $datos = [];
        foreach (["nombre", "apellido", "correo", "telefono"] as $campo) {
            $valor = $_POST[$campo] ?? "";
            $datos[$campo] = is_string($valor) ? trim($valor) : "";
        }

        $limites = [
            "nombre" => 200,
            "apellido" => 200,
            "correo" => 150,
            "telefono" => 50,
        ];

        $errores = [];
        foreach ($limites as $campo => $limite) {
            if ($datos[$campo] === "") {
                $errores[$campo] = "Este campo es obligatorio.";
            } elseif ($this->longitud($datos[$campo]) > $limite) {
                $errores[$campo] = "Este campo no puede superar {$limite} caracteres.";
            }
        }

        if (!isset($errores["correo"]) && !filter_var($datos["correo"], FILTER_VALIDATE_EMAIL)) {
            $errores["correo"] = "Escribe un correo electrónico válido.";
        }

        return [$datos, $errores];
    }
}
