<?php
/**
 * Catálogos fijos de la aplicación. Son la única fuente de verdad para
 * los valores permitidos en los campos de selección: los usan la vista
 * (para pintar las opciones), el controlador (para validar) y el script
 * SQL (como ENUM).
 */
declare(strict_types=1);

const TIPOS_EVENTO = ['Incidente', 'Mantenimiento', 'Alerta', 'Cambio', 'Respaldo'];

const ESTADOS_EVENTO = ['Abierto', 'En proceso', 'Resuelto', 'Cerrado'];

const SEVERIDADES = [
    1 => 'Informativa',
    2 => 'Baja',
    3 => 'Media',
    4 => 'Alta',
    5 => 'Crítica',
];

const LIMITES = [
    'sistema'      => ['min' => 3,  'max' => 60],
    'descripcion'  => ['min' => 10, 'max' => 500],
    'responsable'  => ['min' => 3,  'max' => 80],
    'correo'       => ['max' => 120],
    'tiempo_min'   => 0,
    'tiempo_max'   => 10080, // una semana en minutos
];
