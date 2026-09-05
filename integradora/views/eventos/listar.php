<?php
/**
 * Consulta de eventos en tabla HTML.
 * $eventos: arreglo de filas que entrega el controlador.
 */
$titulo  = 'Eventos';
$eventos = $eventos ?? [];
require BASE_PATH . '/views/layout/header.php';
?>

<section class="panel">
    <div class="panel-cabecera">
        <div>
            <p class="hero-etiqueta">// consulta</p>
            <h1>Eventos registrados</h1>
            <p><?= count($eventos) ?> evento(s) en la base de datos.</p>
        </div>
        <a class="boton boton-primario" href="<?= e(url('crear')) ?>">+ Registrar evento</a>
    </div>

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
                </tr>
            </thead>
            <tbody>
            <?php if ($eventos === []): ?>
                <tr><td colspan="9" class="vacio">No hay eventos registrados todavía.</td></tr>
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
                        <td><?= e($evento['descripcion']) ?></td>
                        <td>
                            <?= e($evento['responsable']) ?><br>
                            <span class="mono"><?= e($evento['correo_responsable']) ?></span>
                        </td>
                        <td class="mono"><?= (int) $evento['tiempo_resolucion_min'] ?> min</td>
                        <td><span class="etiqueta estado-<?= e(clase_css($evento['estado'])) ?>"><?= e($evento['estado']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
