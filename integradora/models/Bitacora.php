<?php
/**
 * Modelo Bitacora: rastro automático de cada acción realizada en la
 * aplicación. Solo permite insertar y consultar; desde la aplicación no
 * existe forma de editar ni borrar la bitácora.
 */
declare(strict_types=1);

class Bitacora
{
    public const REGISTRAR = 'REGISTRAR';
    public const CONSULTAR = 'CONSULTAR';
    public const BUSCAR    = 'BUSCAR';
    public const ELIMINAR  = 'ELIMINAR';

    private const ACCIONES = [self::REGISTRAR, self::CONSULTAR, self::BUSCAR, self::ELIMINAR];

    private PDO $bd;

    public function __construct(?PDO $bd = null)
    {
        $this->bd = $bd ?? conexion();
    }

    /**
     * Inserta una entrada en la bitácora con la IP y el navegador de la
     * petición actual. $eventoId es null para acciones generales.
     */
    public function registrar(string $accion, ?int $eventoId, string $detalle): int
    {
        if (!in_array($accion, self::ACCIONES, true)) {
            throw new InvalidArgumentException('Acción de bitácora no permitida: ' . $accion);
        }

        $sql = 'INSERT INTO bitacora (accion, evento_id, detalle, ip_origen, agente)
                VALUES (:accion, :evento_id, :detalle, :ip_origen, :agente)';

        $sentencia = $this->bd->prepare($sql);
        $sentencia->bindValue(':accion', $accion);
        $sentencia->bindValue(':evento_id', $eventoId, $eventoId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $sentencia->bindValue(':detalle', mb_substr($detalle, 0, 255));
        $sentencia->bindValue(':ip_origen', self::ipOrigen());
        $sentencia->bindValue(':agente', self::agente());
        $sentencia->execute();

        return (int) $this->bd->lastInsertId();
    }

    /**
     * Devuelve las entradas más recientes de la bitácora.
     */
    public function obtenerTodos(int $limite = 200): array
    {
        $sentencia = $this->bd->prepare('SELECT * FROM bitacora ORDER BY id DESC LIMIT :limite');
        $sentencia->bindValue(':limite', $limite, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetchAll();
    }

    /**
     * Conteo de entradas por acción, para el encabezado de la vista.
     */
    public function resumen(): array
    {
        $filas = $this->bd->query('SELECT accion, COUNT(*) AS cantidad FROM bitacora GROUP BY accion')->fetchAll();
        $resumen = array_fill_keys(self::ACCIONES, 0);
        foreach ($filas as $fila) {
            $resumen[$fila['accion']] = (int) $fila['cantidad'];
        }
        return $resumen;
    }

    private static function ipOrigen(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }

    private static function agente(): string
    {
        return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }
}
