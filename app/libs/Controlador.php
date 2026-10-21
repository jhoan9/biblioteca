<?php

class Controlador
{
    protected $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
    }

    protected function urlBase()
    {
        $script = $_SERVER["SCRIPT_NAME"] ?? "";
        return rtrim(dirname(dirname($script)), "/\\");
    }

    protected function url($ruta = "")
    {
        return $this->urlBase() . $ruta;
    }

    protected function redirigir($ruta)
    {
        header("Location: " . $this->url($ruta), true, 303);
        exit;
    }

    protected function esPost()
    {
        return ($_SERVER["REQUEST_METHOD"] ?? "") === "POST";
    }

    protected function exigirPost()
    {
        if (!$this->esPost()) {
            header("Allow: POST");
            http_response_code(405);
            echo "Este método requiere una solicitud POST.";
            exit;
        }
    }

    protected function tokenCsrf()
    {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }

        return $_SESSION["csrf_token"];
    }

    protected function csrfValido()
    {
        $enviado = $_POST["csrf_token"] ?? "";
        $sesion = $_SESSION["csrf_token"] ?? "";

        return is_string($enviado)
            && is_string($sesion)
            && $sesion !== ""
            && hash_equals($sesion, $enviado);
    }

    protected function exigirCsrf()
    {
        if (!$this->csrfValido()) {
            http_response_code(403);
            echo "La solicitud expiró o no es válida. Vuelve a intentarlo.";
            exit;
        }
    }

    protected function obtenerTokenCsrf()
    {
        return $this->tokenCsrf();
    }

    protected function flash($tipo, $mensaje)
    {
        $_SESSION["flash"] = [
            "tipo" => $tipo,
            "mensaje" => $mensaje,
        ];
    }

    protected function longitud($valor)
    {
        if (function_exists("mb_strlen")) {
            return mb_strlen($valor, "UTF-8");
        }

        $longitud = preg_match_all("/./us", $valor, $coincidencias);
        return $longitud === false ? PHP_INT_MAX : $longitud;
    }
}
