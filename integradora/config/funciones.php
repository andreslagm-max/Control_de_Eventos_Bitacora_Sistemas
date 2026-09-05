<?php
/**
 * Funciones auxiliares compartidas por controladores y vistas.
 */
declare(strict_types=1);

/**
 * Escapa texto para imprimirlo en HTML (protección contra XSS).
 */
function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Construye una URL interna a partir de una acción de la lista blanca.
 */
function url(string $accion = 'inicio', array $parametros = []): string
{
    $query = ['accion' => $accion] + $parametros;
    return 'index.php?' . http_build_query($query);
}

/**
 * Redirige a otra acción y termina la ejecución.
 */
function redirigir(string $accion, array $parametros = []): never
{
    header('Location: ' . url($accion, $parametros));
    exit;
}

/**
 * Devuelve el token CSRF de la sesión, creándolo si no existe.
 * Se incluye como campo oculto en los formularios que modifican datos.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Comprueba que el token recibido coincide con el de la sesión.
 */
function csrf_valido(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Guarda un mensaje en sesión para mostrarlo en la siguiente página.
 * $tipo: exito | error | info
 */
function mensaje_flash(string $tipo, string $texto): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

/**
 * Devuelve el mensaje pendiente (una sola vez) o null.
 */
function obtener_mensaje_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $mensaje = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $mensaje;
}

/**
 * Formatea una fecha de MySQL (Y-m-d H:i:s) para mostrarla en pantalla.
 */
function fecha_legible(?string $fecha): string
{
    if ($fecha === null || $fecha === '') {
        return '';
    }
    $objeto = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
    return $objeto ? $objeto->format('d/m/Y H:i') : $fecha;
}

/**
 * Convierte un valor a clase CSS segura (minúsculas, guiones).
 */
function clase_css(string $valor): string
{
    $valor = strtolower(trim($valor));
    return preg_replace('/[^a-z0-9]+/', '-', $valor) ?? '';
}
