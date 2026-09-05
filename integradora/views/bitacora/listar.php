<?php
/**
 * Vista de la bitácora de auditoría.
 * $entradas: filas de la tabla bitacora. $resumen: conteo por acción.
 */
$titulo   = 'Bitácora';
$entradas = $entradas ?? [];
$resumen  = $resumen ?? [];
require BASE_PATH . '/views/layout/header.php';
?>

<section class="panel">
    <div class="panel-cabecera">
        <div>
            <p class="hero-etiqueta">// auditoría</p>
            <h1>Bitácora del sistema</h1>
            <p>Rastro automático de cada acción realizada en SysTrace. Solo lectura.</p>
        </div>
    </div>

    <div class="metricas" style="margin: 0 0 22px;">
        <?php foreach ($resumen as $accion => $cantidad): ?>
            <div class="metrica">
                <div class="metrica-valor"><?= (int) $cantidad ?></div>
                <div class="metrica-etiqueta"><?= e(ucfirst(strtolower($accion))) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha y hora</th>
                    <th>Acción</th>
                    <th>Evento</th>
                    <th>Detalle</th>
                    <th>IP origen</th>
                    <th>Navegador</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($entradas === []): ?>
                <tr><td colspan="7" class="vacio">La bitácora está vacía.</td></tr>
            <?php else: ?>
                <?php foreach ($entradas as $entrada): ?>
                    <tr>
                        <td class="mono">#<?= (int) $entrada['id'] ?></td>
                        <td class="mono"><?= e(fecha_legible($entrada['fecha_hora'])) ?></td>
                        <td><span class="etiqueta accion-<?= e(clase_css($entrada['accion'])) ?>"><?= e($entrada['accion']) ?></span></td>
                        <td class="mono"><?= $entrada['evento_id'] === null ? '—' : '#' . (int) $entrada['evento_id'] ?></td>
                        <td class="col-detalle"><?= e($entrada['detalle']) ?></td>
                        <td class="mono"><?= e($entrada['ip_origen']) ?></td>
                        <td class="mono" title="<?= e($entrada['agente']) ?>"><?= e(mb_strimwidth($entrada['agente'], 0, 40, '…')) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
