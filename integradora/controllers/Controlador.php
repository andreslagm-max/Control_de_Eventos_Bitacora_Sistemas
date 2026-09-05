<?php
/**
 * Controlador base: utilidades compartidas por todos los controladores.
 */
declare(strict_types=1);

abstract class Controlador
{
    /**
     * Renderiza una vista de views/ pasándole variables por nombre.
     */
    protected function vista(string $nombre, array $datos = []): void
    {
        extract($datos, EXTR_SKIP);
        require BASE_PATH . '/views/' . $nombre . '.php';
    }

    /**
     * Indica si la petición actual es POST.
     */
    protected function esPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Lee un campo de texto del formulario ya recortado.
     */
    protected function campo(array $fuente, string $nombre): string
    {
        $valor = $fuente[$nombre] ?? '';
        return is_string($valor) ? trim($valor) : '';
    }
}
