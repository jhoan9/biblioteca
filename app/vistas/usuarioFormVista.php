<?php
require_once __DIR__ . "/layout.php";

$esEdicion = $modo === "editar";
renderHeader($esEdicion ? "Editar usuario" : "Nuevo usuario", "usuarios");

$valor = function ($campo) use ($datos) {
    return e($datos[$campo] ?? "");
};
$error = function ($campo) use ($errores) {
    return $errores[$campo] ?? "";
};
?>

<div class="pagina-cabecera">
    <div>
        <h1><?= $esEdicion ? "Editar usuario" : "Nuevo usuario" ?></h1>
        <p><?= $esEdicion ? "Actualiza los datos de contacto." : "Registra a una persona que podrá tomar préstamos." ?></p>
    </div>
    <a class="boton boton-secundario" href="<?= e(url("/usuarios/index")) ?>">Volver a usuarios</a>
</div>

<section class="panel form-panel">
    <div class="panel-cabecera">
        <div>
            <h2>Datos del usuario</h2>
            <p>Todos los campos son obligatorios.</p>
        </div>
    </div>

    <form class="form-cuerpo" action="<?= e($accion) ?>" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= (int) ($datos["id_usuario"] ?? 0) ?>">
        <?php endif; ?>

        <div class="campos">
            <div class="campo <?= $error("nombre") ? "con-error" : "" ?>">
                <label for="nombre">Nombre</label>
                <input id="nombre" name="nombre" type="text" maxlength="200"
                       value="<?= $valor("nombre") ?>" placeholder="María"
                       required <?= $error("nombre") ? 'aria-invalid="true" aria-describedby="error-nombre"' : "" ?>>
                <?php if ($error("nombre")): ?>
                    <span class="error-campo" id="error-nombre"><?= e($error("nombre")) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= $error("apellido") ? "con-error" : "" ?>">
                <label for="apellido">Apellido</label>
                <input id="apellido" name="apellido" type="text" maxlength="200"
                       value="<?= $valor("apellido") ?>" placeholder="Gómez"
                       required <?= $error("apellido") ? 'aria-invalid="true" aria-describedby="error-apellido"' : "" ?>>
                <?php if ($error("apellido")): ?>
                    <span class="error-campo" id="error-apellido"><?= e($error("apellido")) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo campo-completo <?= $error("correo") ? "con-error" : "" ?>">
                <label for="correo">Correo electrónico</label>
                <input id="correo" name="correo" type="email" maxlength="150"
                       value="<?= $valor("correo") ?>" placeholder="maria@ejemplo.com"
                       required <?= $error("correo") ? 'aria-invalid="true" aria-describedby="error-correo"' : "" ?>>
                <?php if ($error("correo")): ?>
                    <span class="error-campo" id="error-correo"><?= e($error("correo")) ?></span>
                <?php else: ?>
                    <span class="ayuda">Debe ser único; se usará para identificar al usuario.</span>
                <?php endif; ?>
            </div>

            <div class="campo campo-completo <?= $error("telefono") ? "con-error" : "" ?>">
                <label for="telefono">Teléfono</label>
                <input id="telefono" name="telefono" type="tel" maxlength="50"
                       value="<?= $valor("telefono") ?>" placeholder="+57 300 123 4567"
                       required <?= $error("telefono") ? 'aria-invalid="true" aria-describedby="error-telefono"' : "" ?>>
                <?php if ($error("telefono")): ?>
                    <span class="error-campo" id="error-telefono"><?= e($error("telefono")) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="formulario-pie">
            <a class="boton boton-secundario" href="<?= e(url("/usuarios/index")) ?>">Cancelar</a>
            <button class="boton boton-primario" type="submit">
                <?= $esEdicion ? "Guardar cambios" : "Registrar usuario" ?>
            </button>
        </div>
    </form>
</section>

<?php renderFooter(); ?>
