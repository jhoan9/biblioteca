<?php
$baseUrl = rtrim(dirname(dirname($_SERVER["SCRIPT_NAME"])), "/\\");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de libros | Biblioteca</title>
    <style>
        body {
            background: #f4f6f8;
            color: #202b36;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        main {
            margin: 40px auto;
            max-width: 1100px;
            padding: 0 20px;
        }
        .encabezado {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .formulario, .tabla-contenedor {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
            margin-bottom: 24px;
            padding: 22px;
        }
        .campos {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }
        input {
            border: 1px solid #b8c4ce;
            border-radius: 4px;
            box-sizing: border-box;
            font: inherit;
            padding: 10px;
            width: 100%;
        }
        button {
            background: #145da0;
            border: 0;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
            font: inherit;
            margin-top: 16px;
            padding: 11px 18px;
        }
        .mensaje {
            border-radius: 4px;
            margin-bottom: 18px;
            padding: 12px 16px;
        }
        .exito {
            background: #e4f4e9;
            color: #176b3a;
        }
        .error {
            background: #fff0ef;
            color: #a33b35;
        }
        .error-campo {
            color: #a33b35;
            display: block;
            font-size: .9rem;
            margin-top: 5px;
        }
        a {
            color: #145da0;
        }
        .tabla-contenedor {
            overflow-x: auto;
        }
        table {
            border-collapse: collapse;
            min-width: 700px;
            width: 100%;
        }
        th, td {
            border-bottom: 1px solid #e3e8ed;
            padding: 13px 16px;
            text-align: left;
        }
        th {
            background: #edf3f8;
        }
        tbody tr:last-child td {
            border-bottom: 0;
        }
        .disponible {
            color: #176b3a;
            font-weight: bold;
        }
        .no-disponible {
            color: #a33b35;
            font-weight: bold;
        }
        .vacio {
            padding: 24px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main>
        <div class="encabezado">
            <h1>Catálogo de libros</h1>
            <a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, "UTF-8") ?>/">Volver al inicio</a>
        </div>

        <?php if ($guardado): ?>
            <p class="mensaje exito" role="status">El libro se registró correctamente.</p>
        <?php endif; ?>

        <section class="formulario" aria-labelledby="formulario-titulo">
            <h2 id="formulario-titulo">Registrar un libro</h2>
            <?php if ($errores && !isset($errores["isbn"])): ?>
                <p class="mensaje error" role="alert">Revisa los campos indicados y vuelve a intentarlo.</p>
            <?php endif; ?>
            <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, "UTF-8") ?>/libros/crear" method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, "UTF-8") ?>">
                <div class="campos">
                    <?php foreach (["nombre" => "Nombre", "titulo" => "Título", "autor" => "Autor", "isbn" => "ISBN"] as $campo => $etiqueta): ?>
                        <div>
                            <label for="<?= $campo ?>"><?= $etiqueta ?></label>
                            <input
                                id="<?= $campo ?>"
                                name="<?= $campo ?>"
                                type="text"
                                maxlength="<?= $campo === "isbn" ? 100 : 250 ?>"
                                value="<?= htmlspecialchars($datos[$campo] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                required
                                aria-invalid="<?= isset($errores[$campo]) ? "true" : "false" ?>"
                                <?= isset($errores[$campo]) ? 'aria-describedby="error-' . $campo . '"' : "" ?>
                            >
                            <?php if (isset($errores[$campo])): ?>
                                <span id="error-<?= $campo ?>" class="error-campo"><?= htmlspecialchars($errores[$campo], ENT_QUOTES, "UTF-8") ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Guardar libro</button>
            </form>
        </section>

        <div class="tabla-contenedor">
            <?php if (count($libros) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>ISBN</th>
                            <th>Disponibilidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $libro): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $libro["id_libro"], ENT_QUOTES, "UTF-8") ?></td>
                                <td><?= htmlspecialchars($libro["nombre"], ENT_QUOTES, "UTF-8") ?></td>
                                <td><?= htmlspecialchars($libro["titulo"], ENT_QUOTES, "UTF-8") ?></td>
                                <td><?= htmlspecialchars($libro["autor"], ENT_QUOTES, "UTF-8") ?></td>
                                <td><?= htmlspecialchars($libro["isbn"], ENT_QUOTES, "UTF-8") ?></td>
                                <td>
                                    <?php if ((int) $libro["disponible"] === 1): ?>
                                        <span class="disponible">Disponible</span>
                                    <?php else: ?>
                                        <span class="no-disponible">Prestado</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="vacio">Todavía no hay libros registrados en el catálogo.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
