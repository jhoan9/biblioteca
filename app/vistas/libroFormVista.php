<?php
require_once __DIR__ . "/layout.php";

$esEdicion = $modo === "editar";
renderHeader($esEdicion ? "Editar libro" : "Registrar libro", "libros");

$valor = function ($campo) use ($datos) {
    return e($datos[$campo] ?? "");
};
$error = function ($campo) use ($errores) {
    return $errores[$campo] ?? "";
};
?>

<div class="pagina-cabecera">
    <div>
        <h1><?= $esEdicion ? "Editar libro" : "Registrar libro" ?></h1>
        <p><?= $esEdicion ? "Actualiza los datos del ejemplar." : "Agrega un nuevo ejemplar al catálogo." ?></p>
    </div>
    <a class="boton boton-secundario" href="<?= e(url("/libros/index")) ?>">Volver al catálogo</a>
</div>

<section class="panel form-panel">
    <div class="panel-cabecera">
        <div>
            <h2>Datos del libro</h2>
            <p>Todos los campos son obligatorios.</p>
        </div>
    </div>

    <form class="form-cuerpo" action="<?= e($accion) ?>" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <?php if ($esEdicion): ?>
            <input type="hidden" name="id" value="<?= (int) ($datos["id_libro"] ?? 0) ?>">
        <?php endif; ?>

        <div class="campos">
            <div class="campo campo-completo <?= $error("titulo") ? "con-error" : "" ?>">
                <label for="titulo">Título</label>
                <input id="titulo" name="titulo" type="text" maxlength="250"
                       value="<?= $valor("titulo") ?>" placeholder="Cien años de soledad"
                       required <?= $error("titulo") ? 'aria-invalid="true" aria-describedby="error-titulo"' : "" ?>>
                <?php if ($error("titulo")): ?>
                    <span class="error-campo" id="error-titulo"><?= e($error("titulo")) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= $error("autor") ? "con-error" : "" ?>">
                <label for="autor">Autor</label>
                <input id="autor" name="autor" type="text" maxlength="250"
                       value="<?= $valor("autor") ?>" placeholder="Gabriel García Márquez"
                       required <?= $error("autor") ? 'aria-invalid="true" aria-describedby="error-autor"' : "" ?>>
                <?php if ($error("autor")): ?>
                    <span class="error-campo" id="error-autor"><?= e($error("autor")) ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= $error("isbn") ? "con-error" : "" ?>">
                <label for="isbn">ISBN</label>
                <input id="isbn" name="isbn" type="text" maxlength="100"
                       value="<?= $valor("isbn") ?>" placeholder="978-84-376-0494-7"
                       required <?= $error("isbn") ? 'aria-invalid="true" aria-describedby="error-isbn"' : "" ?>>
                <?php if ($error("isbn")): ?>
                    <span class="error-campo" id="error-isbn"><?= e($error("isbn")) ?></span>
                <?php else: ?>
                    <span class="ayuda">Debe ser único en el catálogo.</span>
                <?php endif; ?>
            </div>

            <div class="campo campo-completo <?= $error("nombre") ? "con-error" : "" ?>">
                <label for="nombre">Nombre del ejemplar</label>
                <input id="nombre" name="nombre" type="text" maxlength="250"
                       value="<?= $valor("nombre") ?>" placeholder="Ejemplar principal"
                       required <?= $error("nombre") ? 'aria-invalid="true" aria-describedby="error-nombre"' : "" ?>>
                <?php if ($error("nombre")): ?>
                    <span class="error-campo" id="error-nombre"><?= e($error("nombre")) ?></span>
                <?php else: ?>
                    <span class="ayuda">Identificador interno o nombre con el que reconoces esta copia.</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="formulario-pie">
            <a class="boton boton-secundario" href="<?= e(url("/libros/index")) ?>">Cancelar</a>
            <button class="boton boton-primario" type="submit">
                <?= $esEdicion ? "Guardar cambios" : "Registrar libro" ?>
            </button>
        </div>
    </form>
</section>

<?php renderFooter(); ?>
