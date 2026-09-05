<?php
/**
 * Consulta de eventos en tabla HTML.
 * $eventos: arreglo de filas que entrega el controlador.
 */
$titulo   = 'Eventos';
$eventos  = $eventos ?? [];
$filtros  = $filtros ?? ['q' => '', 'tipo' => '', 'estado' => ''];
$buscando = $buscando ?? false;
require BASE_PATH . '/views/layout/header.php';
?>

<section class="panel">
    <div class="panel-cabecera">
        <div>
            <p class="hero-etiqueta">// consulta</p>
            <h1>Eventos registrados</h1>
            <p><?= count($eventos) ?> evento(s) <?= $buscando ? 'coinciden con la búsqueda.' : 'en la base de datos.' ?></p>
        </div>
        <a class="boton boton-primario" href="<?= e(url('crear')) ?>">+ Registrar evento</a>
    </div>

    <form class="filtros" method="get" action="index.php" role="search">
        <input type="hidden" name="accion" value="buscar">
        <div class="campo" style="flex: 2;">
            <label for="q">Buscar</label>
            <input type="search" id="q" name="q" maxlength="100" placeholder="Sistema, descripción o responsable" value="<?= e($filtros['q']) ?>">
        </div>
        <div class="campo">
            <label for="f-tipo">Tipo</label>
            <select id="f-tipo" name="tipo">
                <option value="">Todos</option>
                <?php foreach (TIPOS_EVENTO as $tipo): ?>
                    <option value="<?= e($tipo) ?>"<?= $filtros['tipo'] === $tipo ? ' selected' : '' ?>><?= e($tipo) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="f-estado">Estado</label>
            <select id="f-estado" name="estado">
                <option value="">Todos</option>
                <?php foreach (ESTADOS_EVENTO as $estado): ?>
                    <option value="<?= e($estado) ?>"<?= $filtros['estado'] === $estado ? ' selected' : '' ?>><?= e($estado) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="boton boton-secundario">Buscar</button>
        <?php if ($buscando): ?>
            <a class="boton boton-secundario" href="<?= e(url('listar')) ?>">Limpiar</a>
        <?php endif; ?>
    </form>

    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Sistema</th>
                    <th>Tipo</th>
                    <th>Sev.</th>
                    <th>Descripción</th>
                    <th>Responsable</th>
                    <th>Tiempo</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($eventos === []): ?>
                <tr><td colspan="10" class="vacio"><?= $buscando ? 'Ningún evento coincide con los criterios.' : 'No hay eventos registrados todavía.' ?></td></tr>
            <?php else: ?>
                <?php foreach ($eventos as $evento): ?>
                    <tr>
                        <td class="mono">#<?= (int) $evento['id'] ?></td>
                        <td class="mono"><?= e(fecha_legible($evento['fecha_evento'])) ?></td>
                        <td><?= e($evento['sistema']) ?></td>
                        <td><?= e($evento['tipo']) ?></td>
                        <td>
                            <span class="etiqueta sev-<?= (int) $evento['severidad'] ?>" title="<?= e(SEVERIDADES[(int) $evento['severidad']] ?? '') ?>">
                                <?= (int) $evento['severidad'] ?> · <?= e(SEVERIDADES[(int) $evento['severidad']] ?? '') ?>
                            </span>
                        </td>
                        <td class="col-descripcion"><?= e($evento['descripcion']) ?></td>
                        <td>
                            <?= e($evento['responsable']) ?><br>
                            <span class="mono"><?= e($evento['correo_responsable']) ?></span>
                        </td>
                        <td class="mono"><?= (int) $evento['tiempo_resolucion_min'] ?> min</td>
                        <td><span class="etiqueta estado-<?= e(clase_css($evento['estado'])) ?>"><?= e($evento['estado']) ?></span></td>
                        <td>
                            <form method="post" action="<?= e(url('eliminar')) ?>"
                                  data-confirmar="¿Eliminar el evento #<?= (int) $evento['id'] ?> (<?= e($evento['sistema']) ?>)? El borrado quedará en la bitácora.">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $evento['id'] ?>">
                                <button type="submit" class="boton boton-peligro" title="Eliminar evento">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
