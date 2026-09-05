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

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
