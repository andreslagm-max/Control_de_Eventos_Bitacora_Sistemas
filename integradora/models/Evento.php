<?php
/**
 * Modelo Evento: único punto de acceso a la tabla `eventos`.
 * Solo contiene SQL con sentencias preparadas; no imprime HTML ni
 * valida reglas de negocio (eso lo hace el controlador).
 */
declare(strict_types=1);

class Evento
{
    private PDO $bd;

    public function __construct(?PDO $bd = null)
    {
        $this->bd = $bd ?? conexion();
    }

    /**
     * Inserta un evento y devuelve el id generado.
     * $datos debe venir ya validado por el controlador.
     */
    public function insertar(array $datos): int
    {
        $sql = 'INSERT INTO eventos
                    (sistema, tipo, severidad, descripcion, responsable,
                     correo_responsable, tiempo_resolucion_min, estado, fecha_evento)
                VALUES
                    (:sistema, :tipo, :severidad, :descripcion, :responsable,
                     :correo_responsable, :tiempo_resolucion_min, :estado, :fecha_evento)';

        $sentencia = $this->bd->prepare($sql);
        $sentencia->bindValue(':sistema', $datos['sistema']);
        $sentencia->bindValue(':tipo', $datos['tipo']);
        $sentencia->bindValue(':severidad', $datos['severidad'], PDO::PARAM_INT);
        $sentencia->bindValue(':descripcion', $datos['descripcion']);
        $sentencia->bindValue(':responsable', $datos['responsable']);
        $sentencia->bindValue(':correo_responsable', $datos['correo_responsable']);
        $sentencia->bindValue(':tiempo_resolucion_min', $datos['tiempo_resolucion_min'], PDO::PARAM_INT);
        $sentencia->bindValue(':estado', $datos['estado']);
        $sentencia->bindValue(':fecha_evento', $datos['fecha_evento']);
        $sentencia->execute();

        return (int) $this->bd->lastInsertId();
    }

    /**
     * Devuelve todos los eventos, los más recientes primero.
     */
    public function obtenerTodos(): array
    {
        $sql = 'SELECT * FROM eventos ORDER BY fecha_evento DESC, id DESC';
        return $this->bd->query($sql)->fetchAll();
    }

    /**
     * Devuelve un evento por id o null si no existe.
     */
    public function obtenerPorId(int $id): ?array
    {
        $sentencia = $this->bd->prepare('SELECT * FROM eventos WHERE id = :id');
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        $fila = $sentencia->fetch();
        return $fila === false ? null : $fila;
    }

    /**
     * Búsqueda por texto (sistema, descripción o responsable) con filtros
     * opcionales de tipo y estado. Los filtros vacíos se ignoran.
     */
    public function buscar(string $termino = '', string $tipo = '', string $estado = ''): array
    {
        $condiciones = [];
        $parametros  = [];

        if ($termino !== '') {
            // Con sentencias preparadas nativas cada marcador debe ser único.
            $condiciones[] = '(sistema LIKE :t_sistema OR descripcion LIKE :t_descripcion OR responsable LIKE :t_responsable)';
            $patron = '%' . $termino . '%';
            $parametros[':t_sistema']     = $patron;
            $parametros[':t_descripcion'] = $patron;
            $parametros[':t_responsable'] = $patron;
        }
        if ($tipo !== '') {
            $condiciones[] = 'tipo = :tipo';
            $parametros[':tipo'] = $tipo;
        }
        if ($estado !== '') {
            $condiciones[] = 'estado = :estado';
            $parametros[':estado'] = $estado;
        }

        $sql = 'SELECT * FROM eventos';
        if ($condiciones !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        }
        $sql .= ' ORDER BY fecha_evento DESC, id DESC';

        $sentencia = $this->bd->prepare($sql);
        $sentencia->execute($parametros);
        return $sentencia->fetchAll();
    }

    /**
     * Elimina un evento. Devuelve true si existía y se borró.
     */
    public function eliminar(int $id): bool
    {
        $sentencia = $this->bd->prepare('DELETE FROM eventos WHERE id = :id');
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->rowCount() === 1;
    }

    /**
     * Cifras para el panel de inicio.
     */
    public function resumen(): array
    {
        $sql = "SELECT
                    COUNT(*)                                                    AS total,
                    SUM(estado IN ('Abierto', 'En proceso'))                    AS activos,
                    SUM(severidad >= 4 AND estado IN ('Abierto', 'En proceso')) AS criticos,
                    SUM(estado IN ('Resuelto', 'Cerrado'))                      AS resueltos
                FROM eventos";
        $fila = $this->bd->query($sql)->fetch();
        return [
            'total'    => (int) ($fila['total'] ?? 0),
            'activos'  => (int) ($fila['activos'] ?? 0),
            'criticos' => (int) ($fila['criticos'] ?? 0),
            'resueltos'=> (int) ($fila['resueltos'] ?? 0),
        ];
    }

    /**
     * Últimos eventos registrados, para el panel de inicio.
     */
    public function ultimos(int $cantidad = 5): array
    {
        $sentencia = $this->bd->prepare('SELECT * FROM eventos ORDER BY id DESC LIMIT :cantidad');
        $sentencia->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetchAll();
    }
}
