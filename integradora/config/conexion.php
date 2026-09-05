<?php
/**
 * Conexión a MySQL en un archivo independiente.
 * Credenciales exigidas por la actividad: usuario root sin clave,
 * base de datos "integradora".
 */
declare(strict_types=1);

const BD_HOST    = 'localhost';
const BD_NOMBRE  = 'integradora';
const BD_USUARIO = 'root';
const BD_CLAVE   = '';
const BD_CHARSET = 'utf8mb4';

/**
 * Devuelve una única instancia de PDO para toda la petición.
 * Lanza PDOException si no puede conectar; el controlador la captura.
 */
function conexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', BD_HOST, BD_NOMBRE, BD_CHARSET);
        $pdo = new PDO($dsn, BD_USUARIO, BD_CLAVE, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // filas como arreglos asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // sentencias preparadas reales
        ]);
    }

    return $pdo;
}
