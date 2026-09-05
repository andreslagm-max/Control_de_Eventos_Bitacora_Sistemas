<?php
$titulo  = 'Inicio';
$resumen = $resumen ?? ['total' => 0, 'activos' => 0, 'criticos' => 0, 'resueltos' => 0];
$ultimos = $ultimos ?? [];
require BASE_PATH . '/views/layout/header.php';
?>

<section class="hero">
    <p class="hero-etiqueta">// consola de operaciones</p>
    <h1>Control de eventos de <span class="acento">sistemas</span> con bitácora integrada</h1>
    <p class="hero-texto">
        Registra incidentes, mantenimientos, alertas, cambios y respaldos de la
        infraestructura tecnológica. Cada acción queda auditada automáticamente.
    </p>
    <div class="hero-acciones">
        <a class="boton boton-primario" href="<?= e(url('crear')) ?>">+ Registrar evento</a>
        <a class="boton boton-secundario" href="<?= e(url('listar')) ?>">Ver eventos</a>
    </div>
</section>

<section class="metricas" aria-label="Resumen de eventos">
    <div class="metrica">
        <div class="metrica-valor"><?= (int) $resumen['total'] ?></div>
        <div class="metrica-etiqueta">Eventos registrados</div>
    </div>
    <div class="metrica">
        <div class="metrica-valor"><?= (int) $resumen['activos'] ?></div>
        <div class="metrica-etiqueta">Activos (abiertos / en proceso)</div>
    </div>
    <div class="metrica">
        <div class="metrica-valor critico"><?= (int) $resumen['criticos'] ?></div>
        <div class="metrica-etiqueta">Activos de severidad alta o crítica</div>
    </div>
    <div class="metrica">
        <div class="metrica-valor ok"><?= (int) $resumen['resueltos'] ?></div>
        <div class="metrica-etiqueta">Resueltos / cerrados</div>
    </div>
</section>

<section class="tarjetas">
    <article class="tarjeta">
        <span class="tarjeta-icono">▣</span>
        <h2>Registro de eventos</h2>
        <p>Formulario validado en el navegador y en el servidor antes de guardar en MySQL.</p>
    </article>
    <article class="tarjeta">
        <span class="tarjeta-icono">☰</span>
        <h2>Consulta y búsqueda</h2>
        <p>Tabla de eventos con filtros por sistema, tipo y estado, y eliminación controlada.</p>
    </article>
    <article class="tarjeta">
        <span class="tarjeta-icono">◎</span>
        <h2>Bitácora de auditoría</h2>
        <p>Rastro automático de cada registro, consulta, búsqueda y borrado con fecha e IP de origen.</p>
    </article>
</section>

<?php if ($ultimos !== []): ?>
<section class="panel" style="margin-top: 36px;">
    <div class="panel-cabecera">
        <div>
            <p class="hero-etiqueta">// actividad reciente</p>
            <h1>Últimos eventos</h1>
        </div>
        <a class="boton boton-secundario" href="<?= e(url('listar')) ?>">Ver todos</a>
    </div>
    <div class="tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr><th>ID</th><th>Fecha</th><th>Sistema</th><th>Tipo</th><th>Sev.</th><th>Estado</th></tr>
            </thead>
            <tbody>
            <?php foreach ($ultimos as $evento): ?>
                <tr>
                    <td class="mono">#<?= (int) $evento['id'] ?></td>
                    <td class="mono"><?= e(fecha_legible($evento['fecha_evento'])) ?></td>
                    <td><?= e($evento['sistema']) ?></td>
                    <td><?= e($evento['tipo']) ?></td>
                    <td><span class="etiqueta sev-<?= (int) $evento['severidad'] ?>"><?= (int) $evento['severidad'] ?></span></td>
                    <td><span class="etiqueta estado-<?= e(clase_css($evento['estado'])) ?>"><?= e($evento['estado']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php endif; ?>

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
