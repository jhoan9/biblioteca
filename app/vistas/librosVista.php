<?php
require_once __DIR__ . "/layout.php";

$enlaceFiltro = function ($valor) use ($busqueda) {
    $parametros = [];
    if ($busqueda !== "") {
        $parametros["q"] = $busqueda;
    }
    if ($valor !== "todos") {
        $parametros["estado"] = $valor;
    }

    return url("/libros/index" . ($parametros ? "?" . http_build_query($parametros) : ""));
};

$filtros = [
    "todos" => "Todos",
    "disponibles" => "Disponibles",
    "prestados" => "En préstamo",
];

renderHeader("Catálogo", "libros");
?>

<div class="pagina-cabecera">
    <div>
        <h1>Catálogo</h1>
        <p>
            <?php if ($busqueda !== ""): ?>
                <?= count($libros) ?> resultado<?= count($libros) === 1 ? "" : "s" ?> para "<?= e($busqueda) ?>"
            <?php else: ?>
                <?= (int) $total ?> libro<?= $total === 1 ? "" : "s" ?> en la colección
            <?php endif; ?>
        </p>
    </div>
    <div class="acciones-cabecera">
        <a class="boton boton-primario" href="<?= e(url("/libros/crear")) ?>">
            <?= icono("mas") ?> Registrar libro
        </a>
    </div>
</div>

<div class="barra-herramientas">
    <form class="buscador" method="get" action="<?= e(url("/libros/index")) ?>" role="search">
        <?= icono("buscar") ?>
        <input type="search" name="q" value="<?= e($busqueda) ?>"
               placeholder="Buscar por título, autor o ISBN…" aria-label="Buscar libros">
        <?php if ($estado !== "todos"): ?>
            <input type="hidden" name="estado" value="<?= e($estado) ?>">
        <?php endif; ?>
    </form>

    <nav class="filtros" aria-label="Filtrar por disponibilidad">
        <?php foreach ($filtros as $valor => $texto): ?>
            <a class="filtro <?= $estado === $valor ? "activo" : "" ?>"
               href="<?= e($enlaceFiltro($valor)) ?>"
               <?= $estado === $valor ? 'aria-current="page"' : "" ?>>
                <?= e($texto) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</div>

<?php if ($libros): ?>
    <div class="libros-grid">
        <?php foreach ($libros as $libro): ?>
            <?php
            $disponible = (int) $libro["disponible"] === 1;
            $tono = (((int) $libro["id_libro"] * 47) % 360 + 360) % 360;
            ?>
            <article class="libro-card">
                <div class="libro-portada" style="--tono: <?= $tono ?>">
                    <span><?= e(iniciales($libro["titulo"])) ?></span>
                    <span class="insignia libro-estado <?= $disponible ? "insignia-verde" : "insignia-ambar" ?>">
                        <?= $disponible ? "Disponible" : "Prestado" ?>
                    </span>
                </div>
                <div class="libro-cuerpo">
                    <h2 class="libro-titulo"><?= e($libro["titulo"]) ?></h2>
                    <p class="libro-autor"><?= e($libro["autor"]) ?></p>

                    <dl class="libro-meta">
                        <div>
                            <dt>ISBN</dt>
                            <dd title="<?= e($libro["isbn"]) ?>"><?= e($libro["isbn"]) ?></dd>
                        </div>
                        <div>
                            <dt>Ejemplar</dt>
                            <dd title="<?= e($libro["nombre"]) ?>"><?= e($libro["nombre"]) ?></dd>
                        </div>
                    </dl>

                    <div class="libro-acciones">
                        <?php if ($disponible): ?>
                            <a class="boton boton-primario"
                               href="<?= e(url("/prestamos/crear?libro=" . (int) $libro["id_libro"])) ?>">
                                <?= icono("prestamo") ?> Prestar
                            </a>
                        <?php else: ?>
                            <a class="boton boton-secundario" href="<?= e(url("/prestamos/index?filtro=activos")) ?>">
                                <?= icono("reloj") ?> Ver préstamo
                            </a>
                        <?php endif; ?>

                        <a class="boton boton-icono" href="<?= e(url("/libros/editar/" . (int) $libro["id_libro"])) ?>"
                           title="Editar libro" aria-label="Editar <?= e($libro["titulo"]) ?>">
                            <?= icono("editar") ?>
                        </a>

                        <form class="form-inline" method="post" action="<?= e(url("/libros/eliminar")) ?>"
                              data-confirm="¿Eliminar «<?= e($libro["titulo"]) ?>» del catálogo?">
                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken ?? "") ?>">
                            <input type="hidden" name="id" value="<?= (int) $libro["id_libro"] ?>">
                            <button type="submit" class="boton boton-icono peligro"
                                    title="Eliminar libro" aria-label="Eliminar <?= e($libro["titulo"]) ?>">
                                <?= icono("eliminar") ?>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="panel">
        <div class="vacio">
            <span class="vacio-icono"><?= icono("libro") ?></span>
            <?php if ($busqueda !== "" || $estado !== "todos"): ?>
                <h3>Sin resultados</h3>
                <p>No encontramos libros que coincidan con tu búsqueda o filtro.</p>
                <a class="boton boton-secundario" href="<?= e(url("/libros/index")) ?>">Limpiar filtros</a>
            <?php else: ?>
                <h3>Tu catálogo está vacío</h3>
                <p>Registra tu primer libro para empezar a gestionar la colección.</p>
                <a class="boton boton-primario" href="<?= e(url("/libros/crear")) ?>">
                    <?= icono("mas") ?> Registrar libro
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php renderFooter(); ?>
