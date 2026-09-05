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
