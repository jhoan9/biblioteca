<?php
require_once __DIR__ . "/layout.php";
renderHeader("Panel", "panel");
?>

<div class="pagina-cabecera">
    <div>
        <h1>Hola de nuevo</h1>
        <p>Este es el resumen de tu biblioteca hoy.</p>
    </div>
    <div class="acciones-cabecera">
        <a class="boton boton-secundario" href="<?= e(url("/libros/crear")) ?>">
            <?= icono("mas") ?> Registrar libro
        </a>
        <a class="boton boton-primario" href="<?= e(url("/prestamos/crear")) ?>">
            <?= icono("prestamo") ?> Nuevo préstamo
        </a>
    </div>
</div>

<?php if ($resumen["vencidos"] > 0): ?>
    <div class="banner">
        <?= icono("alerta") ?>
        <span>
            Tienes <?= $resumen["vencidos"] ?> préstamo<?= $resumen["vencidos"] === 1 ? "" : "s" ?>
            vencido<?= $resumen["vencidos"] === 1 ? "" : "s" ?> pendiente<?= $resumen["vencidos"] === 1 ? "" : "s" ?> de devolución.
        </span>
        <a href="<?= e(url("/prestamos/index?filtro=vencidos")) ?>">Revisar</a>
    </div>
<?php endif; ?>

<div class="estadisticas">
    <div class="estadistica">
        <span class="estadistica-icono"><?= icono("libro") ?></span>
        <span class="estadistica-numero"><?= (int) $resumen["libros"] ?></span>
        <span class="estadistica-etiqueta">Libros en el catálogo</span>
    </div>
    <div class="estadistica">
        <span class="estadistica-icono verde"><?= icono("check") ?></span>
        <span class="estadistica-numero"><?= (int) $resumen["disponibles"] ?></span>
        <span class="estadistica-etiqueta">Disponibles ahora</span>
    </div>
    <div class="estadistica">
        <span class="estadistica-icono ambar"><?= icono("prestamo") ?></span>
        <span class="estadistica-numero"><?= (int) $resumen["prestados"] ?></span>
        <span class="estadistica-etiqueta">En préstamo</span>
    </div>
    <div class="estadistica">
        <span class="estadistica-icono"><?= icono("reloj") ?></span>
        <span class="estadistica-numero"><?= (int) $resumen["activos"] ?></span>
        <span class="estadistica-etiqueta">Préstamos activos</span>
    </div>
    <div class="estadistica">
        <span class="estadistica-icono"><?= icono("usuarios") ?></span>
        <span class="estadistica-numero"><?= (int) $resumen["usuarios"] ?></span>
        <span class="estadistica-etiqueta">Usuarios registrados</span>
    </div>
</div>

<div class="dashboard-grid">
    <section class="panel dashboard-ancho">
        <div class="panel-cabecera">
            <h2>Préstamos recientes</h2>
            <a href="<?= e(url("/prestamos/index")) ?>">Ver todos</a>
        </div>
        <div class="panel-cuerpo">
            <?php if ($prestamos): ?>
                <?php foreach ($prestamos as $prestamo): ?>
                    <?php
                    $devuelto = $prestamo["fecha_devolucion_real"] !== null;
                    $vencido = !$devuelto && strtotime($prestamo["fecha_limite_devolucion"]) < time();
                    ?>
                    <div class="fila">
                        <div class="fila-avatar"><?= e(iniciales($prestamo["titulo"])) ?></div>
                        <div class="fila-principal">
                            <div class="fila-titulo"><?= e($prestamo["titulo"]) ?></div>
                            <div class="fila-sub">
                                <?= e($prestamo["nombre_usuario"] . " " . $prestamo["apellido_usuario"]) ?>
                                · <?= e(date("d/m/Y", strtotime($prestamo["fecha_prestamo"]))) ?>
                            </div>
                        </div>
                        <div class="fila-lado">
                            <?php if ($devuelto): ?>
                                <span class="insignia insignia-verde">Devuelto</span>
                            <?php elseif ($vencido): ?>
                                <span class="insignia insignia-rojo">Vencido</span>
                            <?php else: ?>
                                <span class="insignia insignia-ambar">Activo</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="panel-vacio">
                    Aún no hay préstamos registrados.
                    <a href="<?= e(url("/prestamos/crear")) ?>">Crear el primero</a>.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="panel dashboard-ancho">
        <div class="panel-cabecera">
            <h2>Últimos libros agregados</h2>
            <a href="<?= e(url("/libros/index")) ?>">Ver catálogo</a>
        </div>
        <div class="panel-cuerpo">
            <?php if ($libros): ?>
                <?php foreach ($libros as $libro): ?>
                    <div class="fila">
                        <div class="fila-avatar"><?= e(iniciales($libro["titulo"])) ?></div>
                        <div class="fila-principal">
                            <a class="fila-titulo" href="<?= e(url("/libros/editar/" . (int) $libro["id_libro"])) ?>">
                                <?= e($libro["titulo"]) ?>
                            </a>
                            <div class="fila-sub"><?= e($libro["autor"]) ?></div>
                        </div>
                        <div class="fila-lado">
                            <?php if ((int) $libro["disponible"] === 1): ?>
                                <span class="insignia insignia-verde">Disponible</span>
                            <?php else: ?>
                                <span class="insignia insignia-ambar">Prestado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="panel-vacio">
                    Tu catálogo está vacío.
                    <a href="<?= e(url("/libros/crear")) ?>">Registra un libro</a>.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php renderFooter(); ?>
