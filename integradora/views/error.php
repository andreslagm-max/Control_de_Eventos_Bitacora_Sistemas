<?php
$titulo = 'Error';
require BASE_PATH . '/views/layout/header.php';
?>
<section class="panel panel-centrado">
    <p class="hero-etiqueta">// error del sistema</p>
    <h1>No se pudo completar la operación</h1>
    <p class="texto-suave">
        Ocurrió un problema al comunicarse con la base de datos. Verifica que el
        servidor MySQL esté activo y que la base <code>integradora</code> exista.
    </p>
    <a class="boton boton-primario" href="<?= e(url('inicio')) ?>">Volver al inicio</a>
</section>
<?php require BASE_PATH . '/views/layout/footer.php'; ?>
