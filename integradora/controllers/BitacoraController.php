<?php
/**
 * Controlador de la bitácora: solo consulta. La escritura la hacen los
 * demás controladores a través del modelo Bitacora.
 */
declare(strict_types=1);

require_once BASE_PATH . '/controllers/Controlador.php';
require_once BASE_PATH . '/models/Bitacora.php';

class BitacoraController extends Controlador
{
    private Bitacora $bitacora;

    public function __construct()
    {
        $this->bitacora = new Bitacora();
    }

    /** Vista de auditoría con las entradas más recientes. */
    public function listar(): void
    {
        $this->vista('bitacora/listar', [
            'entradas' => $this->bitacora->obtenerTodos(200),
            'resumen'  => $this->bitacora->resumen(),
        ]);
    }
}
