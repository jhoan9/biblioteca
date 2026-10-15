<?php

class Control
{
    private $controlador = "Login";
    private $metodo = "caratula";
    private $parametros = [];
    private $db;

    public function __construct(MySQLdb $db)
    {
        $this->db = $db;
        $this->resolverRuta();
        $this->despachar();
    }

    private function resolverRuta()
    {
        $ruta = trim((string) ($_GET["url"] ?? ""), "/");
        if ($ruta === "") {
            return;
        }

        $segmentos = explode("/", $ruta);
        foreach ($segmentos as $segmento) {
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $segmento)) {
                $this->noEncontrado();
            }
        }

        $this->controlador = ucfirst(array_shift($segmentos));
        if (isset($segmentos[0])) {
            $this->metodo = array_shift($segmentos);
        }
        $this->parametros = $segmentos;
    }

    private function despachar()
    {
        $archivoControlador = __DIR__ . "/../controladores/" . $this->controlador . ".php";
        if (!is_file($archivoControlador)) {
            $this->noEncontrado();
        }

        require_once __DIR__ . "/Controlador.php";
        require_once $archivoControlador;

        if (!class_exists($this->controlador, false)) {
            $this->noEncontrado();
        }

        $controlador = new $this->controlador($this->db);
        if (!method_exists($controlador, $this->metodo)) {
            $this->noEncontrado();
        }

        $metodo = new ReflectionMethod($controlador, $this->metodo);
        if (
            !$metodo->isPublic()
            || $metodo->isStatic()
            || $metodo->isConstructor()
            || strpos($this->metodo, "__") === 0
            || $metodo->getDeclaringClass()->getName() !== $this->controlador
        ) {
            $this->noEncontrado();
        }

        call_user_func_array([$controlador, $this->metodo], $this->parametros);
    }

    private function noEncontrado()
    {
        http_response_code(404);
        echo "La página solicitada no existe.";
        exit;
    }
}