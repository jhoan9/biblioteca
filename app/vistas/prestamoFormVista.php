<?php
require_once __DIR__ . "/layout.php";
renderHeader("Nuevo préstamo", "prestamos");

$error = function ($campo) use ($errores) {
    return $errores[$campo] ?? "";
};
$sinUsuarios = count($usuarios) === 0;
$sinLibros = count($libros) === 0;
?>

<div class="pagina-cabecera">
    <div>
        <h1>Nuevo préstamo</h1>
        <p>Asigna un libro disponible a un usuario de la biblioteca.</p>
    </div>
    <a class="boton boton-secundario" href="<?= e(url("/prestamos/index")) ?>">Volver a préstamos</a>
</div>

<section class="panel form-panel">
    <div class="panel-cabecera">
        <div>
            <h2>Datos del préstamo</h2>
            <p>El libro quedará marcado como prestado al guardar.</p>
        </div>
    </div>

    <form class="form-cuerpo" action="<?= e($accion) ?>" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <?php if ($error("general")): ?>
            <div class="mini-aviso">
                <?= icono("alerta") ?>
                <span><?= e($error("general")) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($sinUsuarios): ?>
            <div class="mini-aviso">
                <?= icono("alerta") ?>
                <span>No hay usuarios registrados. <a href="<?= e(url("/usuarios/crear")) ?>">Crea un usuario</a> para continuar.</span>
            </div>
        <?php endif; ?>

        <?php if ($sinLibros): ?>
            <div class="mini-aviso">
                <?= icono("alerta") ?>
                <span>No hay libros disponibles. <a href="<?= e(url("/libros/index")) ?>">Revisa el catálogo</a>.</span>
            </div>
        <?php endif; ?>

        <div class="campos">
            <div class="campo campo-completo <?= $error("id_usuario") ? "con-error" : "" ?>">
                <label for="id_usuario">Usuario</label>
                <select id="id_usuario" name="id_usuario" required>
                    <option value="0">Selecciona un usuario…</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= (int) $usuario["id_usuario"] ?>"
                            <?= (int) $datos["id_usuario"] === (int) $usuario["id_usuario"] ? "selected" : "" ?>>
                            <?= e($usuario["nombre_usuario"] . " " . $usuario["apellido_usuario"]) ?>
                            — <?= e($usuario["correo_usuario"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($error("id_usuario")): ?>
                    <span class="error-campo"><?= e($error("id_usuario")) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo campo-completo <?= $error("id_libro") ? "con-error" : "" ?>">
                <label for="id_libro">Libro disponible</label>
                <select id="id_libro" name="id_libro" required>
                    <option value="0">Selecciona un libro…</option>
                    <?php foreach ($libros as $libro): ?>
                        <option value="<?= (int) $libro["id_libro"] ?>"
                            <?= (int) $datos["id_libro"] === (int) $libro["id_libro"] ? "selected" : "" ?>>
                            <?= e($libro["titulo"]) ?> — <?= e($libro["autor"]) ?>
                            (<?= e($libro["isbn"]) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($error("id_libro")): ?>
                    <span class="error-campo"><?= e($error("id_libro")) ?></span>
                <?php else: ?>
                    <span class="ayuda">Solo se muestran libros con disponibilidad.</span>
                <?php endif; ?>
            </div>

            <div class="campo campo-completo <?= $error("fecha_limite") ? "con-error" : "" ?>">
                <label for="fecha_limite">Fecha límite de devolución</label>
                <input id="fecha_limite" name="fecha_limite" type="date"
                       value="<?= e($datos["fecha_limite"]) ?>" min="<?= e(date("Y-m-d")) ?>" required>
                <?php if ($error("fecha_limite")): ?>
                    <span class="error-campo"><?= e($error("fecha_limite")) ?></span>
                <?php else: ?>
                    <span class="ayuda">Por defecto se sugieren 7 días a partir de hoy.</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="formulario-pie">
            <a class="boton boton-secundario" href="<?= e(url("/prestamos/index")) ?>">Cancelar</a>
            <button class="boton boton-primario" type="submit"
                <?= ($sinUsuarios || $sinLibros) ? "disabled" : "" ?>>
                Registrar préstamo
            </button>
        </div>
    </form>
</section>

<?php renderFooter(); ?>
