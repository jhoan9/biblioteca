<?php
require_once __DIR__ . "/layout.php";
renderHeader("Préstamos", "prestamos");

$tabs = [
    "activos" => "Activos",
    "vencidos" => "Vencidos",
    "historial" => "Historial",
];
?>

<div class="pagina-cabecera">
    <div>
        <h1>Préstamos</h1>
        <p>Controla las salidas y devoluciones de la colección.</p>
    </div>
    <div class="acciones-cabecera">
        <a class="boton boton-primario" href="<?= e(url("/prestamos/crear")) ?>">
            <?= icono("mas") ?> Nuevo préstamo
        </a>
    </div>
</div>

<div class="barra-herramientas">
    <nav class="filtros" aria-label="Filtrar préstamos">
        <?php foreach ($tabs as $valor => $texto): ?>
            <a class="filtro <?= $filtro === $valor ? "activo" : "" ?>"
               href="<?= e(url("/prestamos/index?filtro=" . $valor)) ?>"
               <?= $filtro === $valor ? 'aria-current="page"' : "" ?>>
                <?= e($texto) ?> (<?= (int) ($conteos[$valor] ?? 0) ?>)
            </a>
        <?php endforeach; ?>
    </nav>
</div>

<?php if ($prestamos): ?>
    <section class="panel">
        <div class="tabla-envoltura">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>Usuario</th>
                        <th>Prestado</th>
                        <th>Devolución</th>
                        <th>Estado</th>
                        <th><span class="texto-tenue">Acción</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $prestamo): ?>
                        <?php
                        $devuelto = $prestamo["fecha_devolucion_real"] !== null;
                        $vencido = !$devuelto
                            && strtotime($prestamo["fecha_limite_devolucion"]) < time();
                        ?>
                        <tr>
                            <td>
                                <div class="celda-persona">
                                    <span class="fila-avatar"><?= e(iniciales($prestamo["titulo"])) ?></span>
                                    <div>
                                        <div class="fila-titulo"><?= e($prestamo["titulo"]) ?></div>
                                        <div class="fila-sub"><?= e($prestamo["autor"]) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fila-titulo">
                                    <?= e($prestamo["nombre_usuario"] . " " . $prestamo["apellido_usuario"]) ?>
                                </div>
                                <div class="fila-sub"><?= e($prestamo["correo_usuario"]) ?></div>
                            </td>
                            <td class="texto-suave">
                                <?= e(date("d/m/Y", strtotime($prestamo["fecha_prestamo"]))) ?>
                            </td>
                            <td>
                                <?php if ($devuelto): ?>
                                    <div class="texto-suave">
                                        <?= e(date("d/m/Y", strtotime($prestamo["fecha_devolucion_real"]))) ?>
                                    </div>
                                    <div class="fila-sub">Devuelto</div>
                                <?php else: ?>
                                    <div class="texto-suave">
                                        <?= e(date("d/m/Y", strtotime($prestamo["fecha_limite_devolucion"]))) ?>
                                    </div>
                                    <div class="fila-sub <?= $vencido ? "texto-rojo" : "" ?>">
                                        <?= e(texto_limite($prestamo["fecha_limite_devolucion"])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($devuelto): ?>
                                    <span class="insignia insignia-verde">Devuelto</span>
                                <?php elseif ($vencido): ?>
                                    <span class="insignia insignia-rojo">Vencido</span>
                                <?php else: ?>
                                    <span class="insignia insignia-ambar">Activo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="acciones">
                                    <?php if (!$devuelto): ?>
                                        <form class="form-inline" method="post"
                                              action="<?= e(url("/prestamos/devolver")) ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="id_detalle"
                                                   value="<?= (int) $prestamo["id_detalle"] ?>">
                                            <button type="submit" class="boton boton-sutil">
                                                <?= icono("devolver") ?> Devolver
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="texto-tenue">—</span>
                                    <?php endif; ?>
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
            <span class="vacio-icono"><?= icono("prestamo") ?></span>
            <?php if ($filtro === "vencidos"): ?>
                <h3>Sin préstamos vencidos</h3>
                <p>Todo está al día. No hay devoluciones pendientes fuera de plazo.</p>
                <a class="boton boton-secundario" href="<?= e(url("/prestamos/index")) ?>">Ver activos</a>
            <?php elseif ($filtro === "historial"): ?>
                <h3>Sin historial todavía</h3>
                <p>Las devoluciones que registres aparecerán aquí.</p>
                <a class="boton boton-secundario" href="<?= e(url("/prestamos/index")) ?>">Ver activos</a>
            <?php else: ?>
                <h3>No hay préstamos activos</h3>
                <p>Registra un préstamo para comenzar a mover la colección.</p>
                <a class="boton boton-primario" href="<?= e(url("/prestamos/crear")) ?>">
                    <?= icono("mas") ?> Nuevo préstamo
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>
