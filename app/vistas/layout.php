<?php

if (!function_exists("e")) {
    function e($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
    }
}

if (!function_exists("url_base")) {
    function url_base()
    {
        $script = $_SERVER["SCRIPT_NAME"] ?? "";
        return rtrim(dirname(dirname($script)), "/\\");
    }
}

if (!function_exists("url")) {
    function url($ruta = "")
    {
        return url_base() . $ruta;
    }
}

if (!function_exists("iniciales")) {
    function iniciales($texto, $maximo = 2)
    {
        $texto = trim((string) $texto);
        if ($texto === "") {
            return "?";
        }

        $partes = preg_split('/\s+/u', $texto);
        $letras = [];
        foreach ($partes as $parte) {
            if ($parte === "") {
                continue;
            }
            $letras[] = function_exists("mb_substr")
                ? mb_strtoupper(mb_substr($parte, 0, 1, "UTF-8"), "UTF-8")
                : strtoupper(substr($parte, 0, 1));
            if (count($letras) >= $maximo) {
                break;
            }
        }

        return $letras ? implode("", $letras) : "?";
    }
}

if (!function_exists("fecha_larga")) {
    function fecha_larga()
    {
        $meses = [
            1 => "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre",
        ];

        return (int) date("j") . " de " . $meses[(int) date("n")] . " de " . date("Y");
    }
}

if (!function_exists("texto_limite")) {
    function texto_limite($fechaLimite)
    {
        if (!$fechaLimite) {
            return "";
        }

        $hoy = new DateTime("today");
        $limite = new DateTime(date("Y-m-d", strtotime($fechaLimite)));

        if ($limite < $hoy) {
            $dias = (int) $hoy->diff($limite)->days;
            return $dias <= 1 ? "Venció ayer" : "Venció hace {$dias} días";
        }

        $dias = (int) $hoy->diff($limite)->days;
        if ($dias === 0) {
            return "Vence hoy";
        }
        if ($dias === 1) {
            return "Vence mañana";
        }

        return "Vence en {$dias} días";
    }
}

if (!function_exists("icono")) {
    function icono($nombre, $clase = "")
    {
        $trazos = [
            "panel" => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/>',
            "libro" => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>',
            "prestamo" => '<path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/>',
            "usuarios" => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            "mas" => '<path d="M12 5v14M5 12h14"/>',
            "buscar" => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            "editar" => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
            "eliminar" => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>',
            "devolver" => '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-4"/>',
            "check" => '<path d="m20 6-11 11-5-5"/>',
            "alerta" => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/>',
            "reloj" => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            "flecha" => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            "menu" => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            "cerrar" => '<path d="M18 6 6 18M6 6l12 12"/>',
            "correo" => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            "telefono" => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.5 2.1L8.1 9.5a16 16 0 0 0 6.4 6.4l1.1-1.1a2 2 0 0 1 2.1-.5c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2z"/>',
        ];
        $trazo = $trazos[$nombre] ?? "";

        return '<svg class="' . e($clase) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            . 'stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            . $trazo . "</svg>";
    }
}

if (!function_exists("renderHeader")) {
    function renderHeader($titulo, $activo = "")
    {
        $flash = $_SESSION["flash"] ?? null;
        unset($_SESSION["flash"]);

        $menu = [
            ["clave" => "panel", "texto" => "Panel", "ruta" => "/", "icono" => "panel"],
            ["clave" => "libros", "texto" => "Catálogo", "ruta" => "/libros/index", "icono" => "libro"],
            ["clave" => "prestamos", "texto" => "Préstamos", "ruta" => "/prestamos/index", "icono" => "prestamo"],
            ["clave" => "usuarios", "texto" => "Usuarios", "ruta" => "/usuarios/index", "icono" => "usuarios"],
        ];
        ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> · Biblioteca</title>
    <link rel="icon" href="<?= e(url("/public/img/favicon.ico")) ?>">
    <link rel="stylesheet" href="<?= e(url("/public/css/estilos.css")) ?>">
</head>
<body>
    <div class="app">
        <aside class="sidebar" id="sidebar" aria-label="Navegación principal">
            <a class="marca" href="<?= e(url("/")) ?>">
                <span class="marca-logo"><?= icono("libro") ?></span>
                <span class="marca-texto">Biblioteca<small>Gestión de colección</small></span>
            </a>

            <nav class="menu">
                <?php foreach ($menu as $item): ?>
                    <a class="menu-item <?= $activo === $item["clave"] ? "activo" : "" ?>"
                       href="<?= e(url($item["ruta"])) ?>"
                       <?= $activo === $item["clave"] ? 'aria-current="page"' : "" ?>>
                        <?= icono($item["icono"]) ?>
                        <span><?= e($item["texto"]) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="sidebar-pie">
                <span class="punto"></span>
                <span>Sistema en línea</span>
            </div>
        </aside>

        <div class="contenido">
            <header class="topbar">
                <button class="icono-boton menu-movil" id="menuMovil" type="button" aria-label="Abrir menú">
                    <?= icono("menu") ?>
                </button>
                <span class="topbar-seccion"><?= e($titulo) ?></span>
                <span class="topbar-fecha"><?= e(fecha_larga()) ?></span>
            </header>

            <main class="principal">
                <?php if ($flash): ?>
                    <div class="aviso <?= $flash["tipo"] === "error" ? "aviso-error" : "aviso-exito" ?>"
                         role="<?= $flash["tipo"] === "error" ? "alert" : "status" ?>">
                        <?= icono($flash["tipo"] === "error" ? "alerta" : "check") ?>
                        <span><?= e($flash["mensaje"]) ?></span>
                    </div>
                <?php endif; ?>
        <?php
    }
}

if (!function_exists("renderFooter")) {
    function renderFooter()
    {
        ?>
            </main>
        </div>
    </div>
    <div class="velo" id="velo"></div>
    <script src="<?= e(url("/public/js/app.js")) ?>"></script>
</body>
</html>
        <?php
    }
}
