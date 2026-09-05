<?php
/**
 * Controlador de eventos: recibe las acciones del usuario, valida los
 * datos, coordina el modelo y elige la vista que se muestra.
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

    /**
     * Recibe el formulario (POST), valida en servidor y pide al modelo
     * el INSERT. Si hay errores vuelve a mostrar el formulario con los
     * valores escritos y el mensaje de cada campo.
     */
    public function guardar(): void
    {
        if (!$this->esPost()) {
            redirigir('crear');
        }

        if (!csrf_valido($_POST['csrf_token'] ?? null)) {
            mensaje_flash('error', 'La sesión del formulario expiró. Vuelve a intentarlo.');
            redirigir('crear');
        }

        ['datos' => $datos, 'errores' => $errores] = $this->validar($_POST);

        if ($errores !== []) {
            $this->vista('eventos/crear', ['valores' => $this->valoresEnviados($_POST), 'errores' => $errores]);
            return;
        }

        $id = $this->eventos->insertar($datos);

        mensaje_flash('exito', sprintf('Evento #%d registrado correctamente: %s en %s.', $id, $datos['tipo'], $datos['sistema']));
        redirigir('listar');
    }

    /** Consulta de todos los eventos en una tabla HTML. */
    public function listar(): void
    {
        $this->vista('eventos/listar', [
            'eventos' => $this->eventos->obtenerTodos(),
        ]);
    }

    /* ------------------------------------------------------------------
       Validación en servidor. Repite las reglas del JavaScript porque el
       cliente puede desactivarlo o enviar la petición con otra herramienta.
       ------------------------------------------------------------------ */

    private function validar(array $entrada): array
    {
        $errores = [];
        $datos   = [];

        $sistema = $this->campo($entrada, 'sistema');
        $largo   = mb_strlen($sistema);
        if ($sistema === '') {
            $errores['sistema'] = 'El sistema afectado es obligatorio.';
        } elseif ($largo < LIMITES['sistema']['min'] || $largo > LIMITES['sistema']['max']) {
            $errores['sistema'] = 'Debe tener entre 3 y 60 caracteres.';
        }
        $datos['sistema'] = $sistema;

        $tipo = $this->campo($entrada, 'tipo');
        if ($tipo === '') {
            $errores['tipo'] = 'Selecciona el tipo de evento.';
        } elseif (!in_array($tipo, TIPOS_EVENTO, true)) {
            $errores['tipo'] = 'El tipo seleccionado no es válido.';
        }
        $datos['tipo'] = $tipo;

        $severidad = $this->campo($entrada, 'severidad');
        if ($severidad === '') {
            $errores['severidad'] = 'La severidad es obligatoria.';
        } elseif (filter_var($severidad, FILTER_VALIDATE_INT) === false) {
            $errores['severidad'] = 'La severidad debe ser un número entero.';
        } elseif ((int) $severidad < 1 || (int) $severidad > 5) {
            $errores['severidad'] = 'La severidad debe estar entre 1 y 5.';
        }
        $datos['severidad'] = (int) $severidad;

        $fechaTexto = $this->campo($entrada, 'fecha_evento');
        $fecha = DateTime::createFromFormat('Y-m-d\TH:i', $fechaTexto)
              ?: DateTime::createFromFormat('Y-m-d H:i:s', $fechaTexto)
              ?: DateTime::createFromFormat('Y-m-d\TH:i:s', $fechaTexto);
        if ($fechaTexto === '') {
            $errores['fecha_evento'] = 'Indica la fecha y hora del evento.';
        } elseif ($fecha === false) {
            $errores['fecha_evento'] = 'La fecha no tiene un formato válido.';
        } elseif ($fecha > new DateTime()) {
            $errores['fecha_evento'] = 'La fecha no puede ser futura.';
        }
        $datos['fecha_evento'] = $fecha ? $fecha->format('Y-m-d H:i:s') : '';

        $responsable = $this->campo($entrada, 'responsable');
        $largo = mb_strlen($responsable);
        if ($responsable === '') {
            $errores['responsable'] = 'El responsable es obligatorio.';
        } elseif ($largo < LIMITES['responsable']['min'] || $largo > LIMITES['responsable']['max']) {
            $errores['responsable'] = 'Debe tener entre 3 y 80 caracteres.';
        }
        $datos['responsable'] = $responsable;

        $correo = $this->campo($entrada, 'correo_responsable');
        if ($correo === '') {
            $errores['correo_responsable'] = 'El correo es obligatorio.';
        } elseif (mb_strlen($correo) > LIMITES['correo']['max']) {
            $errores['correo_responsable'] = 'El correo no puede superar 120 caracteres.';
        } elseif (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            $errores['correo_responsable'] = 'Escribe un correo válido (nombre@dominio.com).';
        }
        $datos['correo_responsable'] = $correo;

        $tiempo = $this->campo($entrada, 'tiempo_resolucion_min');
        if ($tiempo === '') {
            $errores['tiempo_resolucion_min'] = 'Indica el tiempo de resolución (0 si no aplica).';
        } elseif (filter_var($tiempo, FILTER_VALIDATE_INT) === false) {
            $errores['tiempo_resolucion_min'] = 'Debe ser un número entero de minutos.';
        } elseif ((int) $tiempo < LIMITES['tiempo_min']) {
            $errores['tiempo_resolucion_min'] = 'El tiempo no puede ser negativo.';
        } elseif ((int) $tiempo > LIMITES['tiempo_max']) {
            $errores['tiempo_resolucion_min'] = 'El tiempo no puede superar 10080 minutos (una semana).';
        }
        $datos['tiempo_resolucion_min'] = (int) $tiempo;

        $estado = $this->campo($entrada, 'estado');
        if ($estado === '') {
            $errores['estado'] = 'Selecciona el estado.';
        } elseif (!in_array($estado, ESTADOS_EVENTO, true)) {
            $errores['estado'] = 'El estado seleccionado no es válido.';
        }
        $datos['estado'] = $estado;

        $descripcion = $this->campo($entrada, 'descripcion');
        $largo = mb_strlen($descripcion);
        if ($descripcion === '') {
            $errores['descripcion'] = 'La descripción es obligatoria.';
        } elseif ($largo < LIMITES['descripcion']['min']) {
            $errores['descripcion'] = 'La descripción debe tener al menos 10 caracteres.';
        } elseif ($largo > LIMITES['descripcion']['max']) {
            $errores['descripcion'] = 'La descripción no puede superar 500 caracteres.';
        }
        $datos['descripcion'] = $descripcion;

        return ['datos' => $datos, 'errores' => $errores];
    }

    /** Valores tal como los escribió el usuario, para repoblar el formulario. */
    private function valoresEnviados(array $entrada): array
    {
        $campos = ['sistema', 'tipo', 'severidad', 'fecha_evento', 'responsable',
                   'correo_responsable', 'tiempo_resolucion_min', 'estado', 'descripcion'];
        $valores = [];
        foreach ($campos as $campo) {
            $valores[$campo] = $this->campo($entrada, $campo);
        }
        return $valores;
    }
}
