<?php
require_once __DIR__ . "/layout.php";
renderHeader("Usuarios", "usuarios");
?>

<div class="pagina-cabecera">
    <div>
        <h1>Usuarios</h1>
        <p>
            <?php if ($busqueda !== ""): ?>
                <?= count($usuarios) ?> resultado<?= count($usuarios) === 1 ? "" : "s" ?> para "<?= e($busqueda) ?>"
            <?php else: ?>
                <?= (int) $total ?> usuario<?= $total === 1 ? "" : "s" ?> registrado<?= $total === 1 ? "" : "s" ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="acciones-cabecera">
        <a class="boton boton-primario" href="<?= e(url("/usuarios/crear")) ?>">
            <?= icono("mas") ?> Nuevo usuario
        </a>
    </div>
</div>

<div class="barra-herramientas">
    <form class="buscador" method="get" action="<?= e(url("/usuarios/index")) ?>" role="search">
        <?= icono("buscar") ?>
        <input type="search" name="q" value="<?= e($busqueda) ?>"
               placeholder="Buscar por nombre, apellido o correo…" aria-label="Buscar usuarios">
    </form>
</div>

<?php if ($usuarios): ?>
    <section class="panel">
        <div class="tabla-envoltura">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Teléfono</th>
                        <th>Préstamos activos</th>
                        <th><span class="texto-tenue">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <?php $activos = (int) $usuario["prestamos_activos"]; ?>
                        <tr>
                            <td>
                                <div class="celda-persona">
                                    <span class="fila-avatar">
                                        <?= e(iniciales($usuario["nombre_usuario"] . " " . $usuario["apellido_usuario"])) ?>
                                    </span>
                                    <div>
                                        <div class="fila-titulo">
                                            <?= e($usuario["nombre_usuario"] . " " . $usuario["apellido_usuario"]) ?>
                                        </div>
                                        <div class="fila-sub"><?= e($usuario["correo_usuario"]) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="texto-suave"><?= e($usuario["telefono_usuario"]) ?></td>
                            <td>
                                <span class="insignia <?= $activos > 0 ? "insignia-ambar" : "insignia-gris" ?>">
                                    <?= $activos ?> activo<?= $activos === 1 ? "" : "s" ?>
                                </span>
                            </td>
                            <td>
                                <div class="acciones">
                                    <a class="boton boton-sutil"
                                       href="<?= e(url("/prestamos/crear?usuario=" . (int) $usuario["id_usuario"])) ?>">
                                        Prestar
                                    </a>
                                    <a class="boton boton-sutil"
                                       href="<?= e(url("/usuarios/editar/" . (int) $usuario["id_usuario"])) ?>">
                                        Editar
                                    </a>
                                    <form class="form-inline" method="post" action="<?= e(url("/usuarios/eliminar")) ?>"
                                          data-confirm="¿Eliminar a <?= e($usuario["nombre_usuario"] . " " . $usuario["apellido_usuario"]) ?>?">
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken ?? "") ?>">
                                        <input type="hidden" name="id" value="<?= (int) $usuario["id_usuario"] ?>">
                                        <button type="submit" class="boton boton-peligro">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php else: ?>
    <div class="panel">
        <div class="vacio">
            <span class="vacio-icono"><?= icono("usuarios") ?></span>
            <?php if ($busqueda !== ""): ?>
                <h3>Sin resultados</h3>
                <p>No encontramos usuarios que coincidan con "<?= e($busqueda) ?>".</p>
                <a class="boton boton-secundario" href="<?= e(url("/usuarios/index")) ?>">Limpiar búsqueda</a>
            <?php else: ?>
                <h3>Aún no hay usuarios</h3>
                <p>Registra usuarios para poder asignarles préstamos.</p>
                <a class="boton boton-primario" href="<?= e(url("/usuarios/crear")) ?>">
                    <?= icono("mas") ?> Nuevo usuario
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>
