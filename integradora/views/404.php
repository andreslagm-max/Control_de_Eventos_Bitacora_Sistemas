<?php
$titulo = 'No encontrado';
require BASE_PATH . '/views/layout/header.php';
?>
<section class="panel panel-centrado">
    <p class="hero-etiqueta">// error 404</p>
    <h1>Acción no encontrada</h1>
    <p class="texto-suave">La ruta solicitada no existe en SysTrace.</p>
    <a class="boton boton-primario" href="<?= e(url('inicio')) ?>">Volver al inicio</a>
</section>
<?php require BASE_PATH . '/views/layout/footer.php'; ?>
