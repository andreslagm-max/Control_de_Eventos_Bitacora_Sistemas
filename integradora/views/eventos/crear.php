<?php
/**
 * Formulario de registro de eventos.
 * $valores y $errores los define el controlador cuando el servidor rechaza
 * un envío; en la primera carga vienen vacíos.
 */
$titulo  = 'Registrar evento';
$valores = $valores ?? [];
$errores = $errores ?? [];
$v = static fn(string $campo, string $defecto = ''): string => (string) ($valores[$campo] ?? $defecto);
require BASE_PATH . '/views/layout/header.php';
?>

<section class="panel">
    <div class="panel-cabecera">
        <div>
            <p class="hero-etiqueta">// nuevo registro</p>
            <h1>Registrar evento de sistema</h1>
            <p>Los campos marcados con <span class="acento">*</span> son obligatorios.</p>
        </div>
        <a class="boton boton-secundario" href="<?= e(url('listar')) ?>">Ver eventos</a>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="mensaje mensaje-error" role="alert">
            El servidor rechazó el registro. Revisa los campos marcados.
        </div>
    <?php endif; ?>

    <form id="form-evento" class="formulario" method="post" action="<?= e(url('guardar')) ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="campo<?= isset($errores['sistema']) ? ' invalido' : '' ?>">
            <label for="sistema">Sistema afectado <span class="req">*</span></label>
            <input type="text" id="sistema" name="sistema" maxlength="60"
                   placeholder="Ej. Servidor de base de datos" value="<?= e($v('sistema')) ?>">
            <span class="ayuda">Entre 3 y 60 caracteres.</span>
            <span class="error-campo" data-error-para="sistema"><?= e($errores['sistema'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['tipo']) ? ' invalido' : '' ?>">
            <label for="tipo">Tipo de evento <span class="req">*</span></label>
            <select id="tipo" name="tipo">
                <option value="">Selecciona un tipo</option>
                <?php foreach (TIPOS_EVENTO as $tipo): ?>
                    <option value="<?= e($tipo) ?>"<?= $v('tipo') === $tipo ? ' selected' : '' ?>><?= e($tipo) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="ayuda">Incidente, mantenimiento, alerta, cambio o respaldo.</span>
            <span class="error-campo" data-error-para="tipo"><?= e($errores['tipo'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['severidad']) ? ' invalido' : '' ?>">
            <label for="severidad">Severidad (1 a 5) <span class="req">*</span></label>
            <input type="number" id="severidad" name="severidad" min="1" max="5" step="1"
                   placeholder="1 = informativa · 5 = crítica" value="<?= e($v('severidad')) ?>">
            <span class="ayuda">1 informativa · 2 baja · 3 media · 4 alta · 5 crítica.</span>
            <span class="error-campo" data-error-para="severidad"><?= e($errores['severidad'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['fecha_evento']) ? ' invalido' : '' ?>">
            <label for="fecha_evento">Fecha y hora del evento <span class="req">*</span></label>
            <input type="datetime-local" id="fecha_evento" name="fecha_evento" value="<?= e($v('fecha_evento')) ?>">
            <span class="ayuda">No puede ser una fecha futura.</span>
            <span class="error-campo" data-error-para="fecha_evento"><?= e($errores['fecha_evento'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['responsable']) ? ' invalido' : '' ?>">
            <label for="responsable">Responsable <span class="req">*</span></label>
            <input type="text" id="responsable" name="responsable" maxlength="80"
                   placeholder="Nombre de quien atiende el evento" value="<?= e($v('responsable')) ?>">
            <span class="ayuda">Entre 3 y 80 caracteres.</span>
            <span class="error-campo" data-error-para="responsable"><?= e($errores['responsable'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['correo_responsable']) ? ' invalido' : '' ?>">
            <label for="correo_responsable">Correo del responsable <span class="req">*</span></label>
            <input type="email" id="correo_responsable" name="correo_responsable" maxlength="120"
                   placeholder="nombre@empresa.com" value="<?= e($v('correo_responsable')) ?>">
            <span class="ayuda">Se validará el formato del correo.</span>
            <span class="error-campo" data-error-para="correo_responsable"><?= e($errores['correo_responsable'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['tiempo_resolucion_min']) ? ' invalido' : '' ?>">
            <label for="tiempo_resolucion_min">Tiempo de resolución (minutos) <span class="req">*</span></label>
            <input type="number" id="tiempo_resolucion_min" name="tiempo_resolucion_min" min="0" max="10080" step="1"
                   placeholder="0 si aún no se resuelve" value="<?= e($v('tiempo_resolucion_min', '0')) ?>">
            <span class="ayuda">Número entero entre 0 y 10080 (una semana).</span>
            <span class="error-campo" data-error-para="tiempo_resolucion_min"><?= e($errores['tiempo_resolucion_min'] ?? '') ?></span>
        </div>

        <div class="campo<?= isset($errores['estado']) ? ' invalido' : '' ?>">
            <label for="estado">Estado <span class="req">*</span></label>
            <select id="estado" name="estado">
                <?php foreach (ESTADOS_EVENTO as $estado): ?>
                    <option value="<?= e($estado) ?>"<?= $v('estado', 'Abierto') === $estado ? ' selected' : '' ?>><?= e($estado) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="ayuda">Estado actual de la atención.</span>
            <span class="error-campo" data-error-para="estado"><?= e($errores['estado'] ?? '') ?></span>
        </div>

        <div class="campo campo-ancho<?= isset($errores['descripcion']) ? ' invalido' : '' ?>">
            <label for="descripcion">Descripción <span class="req">*</span></label>
            <textarea id="descripcion" name="descripcion" maxlength="500"
                      placeholder="Qué ocurrió, qué se hizo y cuál fue el resultado"><?= e($v('descripcion')) ?></textarea>
            <span class="contador"><span id="contador-descripcion">0</span> / 500</span>
            <span class="error-campo" data-error-para="descripcion"><?= e($errores['descripcion'] ?? '') ?></span>
        </div>

        <div class="formulario-acciones">
            <button type="submit" class="boton boton-primario">Guardar evento</button>
            <button type="reset" class="boton boton-secundario">Limpiar</button>
        </div>
    </form>
</section>

<?php require BASE_PATH . '/views/layout/footer.php'; ?>
