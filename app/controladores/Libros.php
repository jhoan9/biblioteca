<?php

require_once __DIR__ . "/../modelos/LibroModelo.php";

class Libros extends Controlador
{
    public function index()
    {
        $modelo = new LibroModelo($this->db);
        $libros = $modelo->obtenerTodos();
        $errores = [];
        $datos = ["nombre" => "", "titulo" => "", "autor" => "", "isbn" => ""];
        $csrfToken = $this->obtenerTokenCsrf();
        $guardado = isset($_GET["guardado"]) && $_GET["guardado"] === "1";

        require __DIR__ . "/../vistas/librosVista.php";
    }

    public function crear()
    {
        if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
            header("Allow: POST");
            http_response_code(405);
            echo "Este método requiere una solicitud POST.";
            return;
        }

        $csrfEnviado = $_POST["csrf_token"] ?? "";
        $csrfSesion = $_SESSION["csrf_token"] ?? "";
        if (
            !is_string($csrfEnviado)
            || !is_string($csrfSesion)
            || $csrfSesion === ""
            || !hash_equals($csrfSesion, $csrfEnviado)
        ) {
            http_response_code(403);
            echo "La solicitud expiró o no es válida. Regresa al catálogo e inténtalo nuevamente.";
            return;
        }

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

        $modelo = new LibroModelo($this->db);
        if (!$errores && $modelo->existeIsbn($datos["isbn"])) {
            $errores["isbn"] = "Ya existe un libro registrado con ese ISBN.";
        }

        if (!$errores) {
            try {
                $modelo->crear($datos);
            } catch (PDOException $e) {
                if ($e->getCode() !== "23000") {
                    throw $e;
                }

                $errores["isbn"] = "Ya existe un libro registrado con ese ISBN.";
            }
        }

        if (!$errores) {
            $baseUrl = rtrim(dirname(dirname($_SERVER["SCRIPT_NAME"])), "/\\");
            header("Location: " . $baseUrl . "/libros/index?guardado=1", true, 303);
            exit;
        }

        http_response_code(422);
        $libros = $modelo->obtenerTodos();
        $csrfToken = $this->obtenerTokenCsrf();
        $guardado = false;
        require __DIR__ . "/../vistas/librosVista.php";
    }

    private function obtenerTokenCsrf()
    {
        if (!isset($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        return $_SESSION["csrf_token"];
    }

    private function longitud($valor)
    {
        if (function_exists("mb_strlen")) {
            return mb_strlen($valor, "UTF-8");
        }

        $longitud = preg_match_all("/./us", $valor, $coincidencias);
        return $longitud === false ? PHP_INT_MAX : $longitud;
    }
}
