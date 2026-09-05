<?php
/**
 * Cabecera común: <head>, fondo tecnológico y barra de navegación.
 * Espera la variable $titulo definida por la vista que la incluye.
 */
$titulo = $titulo ?? 'SysTrace';
$accionActual = $_GET['accion'] ?? 'inicio';
$menu = [
    'inicio'   => 'Inicio',
    'crear'    => 'Registrar evento',
    'listar'   => 'Eventos',
    'bitacora' => 'Bitácora',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> · SysTrace</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="fondo-tec" aria-hidden="true"></div>

<header class="barra">
    <a class="marca" href="<?= e(url('inicio')) ?>">
        <svg class="marca-logo" viewBox="0 0 40 40" aria-hidden="true">
            <polygon points="20,2 36,11 36,29 20,38 4,29 4,11" fill="none" stroke="currentColor" stroke-width="2.5"/>
            <polyline points="9,22 15,22 18,14 22,30 25,20 31,20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="marca-texto">Sys<span class="marca-acento">Trace</span></span>
        <span class="marca-sub">Control de Eventos de Sistemas</span>
    </a>
    <nav class="menu" aria-label="Navegación principal">
        <?php foreach ($menu as $accion => $etiqueta): ?>
            <a href="<?= e(url($accion)) ?>" class="menu-enlace<?= $accion === $accionActual ? ' activo' : '' ?>">
                <?= e($etiqueta) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>

<main class="contenido">
