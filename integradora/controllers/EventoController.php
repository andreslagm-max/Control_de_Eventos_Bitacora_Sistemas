<?php
/**
 * Controlador de eventos: recibe las acciones del usuario, coordina el
 * modelo y elige la vista que se muestra.
 */
declare(strict_types=1);

require_once BASE_PATH . '/controllers/Controlador.php';
require_once BASE_PATH . '/models/Evento.php';

class EventoController extends Controlador
{
    private Evento $eventos;

    public function __construct()
    {
        $this->eventos = new Evento();
    }

    /** Panel de inicio con cifras y últimos eventos. */
    public function inicio(): void
    {
        $this->vista('inicio', [
            'resumen' => $this->eventos->resumen(),
            'ultimos' => $this->eventos->ultimos(5),
        ]);
    }

    /** Muestra el formulario de registro vacío. */
    public function crear(): void
    {
        $this->vista('eventos/crear');
    }
}
