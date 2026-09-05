<?php
/**
 * SysTrace - Control de Eventos y Bitácora de Sistemas
 * Punto de entrada único (front controller).
 *
 * Toda petición llega aquí con ?accion=... y se despacha según una
 * lista blanca. Cualquier acción desconocida devuelve 404, nunca se
 * incluye un archivo a partir de lo que llegue en la URL.
 */
declare(strict_types=1);

session_start();

header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');

define('BASE_PATH', __DIR__);

require BASE_PATH . '/config/funciones.php';

// Lista blanca de acciones -> vista que se muestra.
$rutas = [
    'inicio' => 'views/inicio.php',
];

$accion = $_GET['accion'] ?? 'inicio';

if (!is_string($accion) || !isset($rutas[$accion])) {
    http_response_code(404);
    require BASE_PATH . '/views/404.php';
    exit;
}

require BASE_PATH . '/' . $rutas[$accion];
