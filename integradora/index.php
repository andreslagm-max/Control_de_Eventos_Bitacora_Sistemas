<?php
/**
 * SysTrace - Control de Eventos y Bitácora de Sistemas
 * Punto de entrada único (front controller).
 *
 * Flujo: Vista -> Controlador -> Modelo -> MySQL.
 * Toda petición llega aquí con ?accion=... y se despacha según una
 * lista blanca de acciones -> [controlador, método]. Cualquier acción
 * desconocida devuelve 404; nunca se incluye un archivo a partir de lo
 * que llegue en la URL.
 */
declare(strict_types=1);

session_start();
date_default_timezone_set('America/Guayaquil');

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

define('BASE_PATH', __DIR__);

require BASE_PATH . '/config/funciones.php';
require BASE_PATH . '/config/catalogos.php';
require BASE_PATH . '/config/conexion.php';

// Lista blanca de acciones -> [controlador, método].
$rutas = [
    'inicio'  => ['EventoController', 'inicio'],
    'crear'   => ['EventoController', 'crear'],
    'guardar' => ['EventoController', 'guardar'],
    'listar'  => ['EventoController', 'listar'],
    'bitacora' => ['BitacoraController', 'listar'],
];

$accion = $_GET['accion'] ?? 'inicio';

if (!is_string($accion) || !isset($rutas[$accion])) {
    http_response_code(404);
    require BASE_PATH . '/views/404.php';
    exit;
}

[$controlador, $metodo] = $rutas[$accion];
require BASE_PATH . '/controllers/' . $controlador . '.php';

try {
    (new $controlador())->$metodo();
} catch (PDOException $excepcion) {
    // Nunca se muestra el detalle SQL al usuario; queda en el log del servidor.
    error_log('[SysTrace] Error de base de datos: ' . $excepcion->getMessage());
    http_response_code(500);
    require BASE_PATH . '/views/error.php';
}
