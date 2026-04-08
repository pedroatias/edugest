<?php
class Evento extends Model {
    protected string $table = 'eventos_calendario';
    protected array $fillable = ['institucion_id','creado_por','titulo','descripcion','tipo','fecha_inicio','fecha_fin','todo_el_dia','color','audiencia','seccion_id','grado_id','asignatura_id','anio_lectivo_id','activo'];

    public function getParaPadre(int $instId, int $anioId, int $seccionId): array {
        return Database::fetchAll("
            SELECT ec.*, u.nombres AS creado_por_nombre, a.nombre AS asignatura_nombre
            FROM eventos_calendario ec
            LEFT JOIN usuarios u ON u.id = ec.creado_por
            LEFT JOIN asignaturas a ON a.id = ec.asignatura_id
            WHERE ec.institucion_id = ? AND ec.anio_lectivo_id = ? AND ec.activo = 1
            AND (
                ec.audiencia = 'todos' OR ec.audiencia = 'padres'
                OR (ec.audiencia = 'seccion' AND ec.seccion_id = ?)
                OR (ec.audiencia = 'grado' AND ec.grado_id = (SELECT grado_id FROM secciones WHERE id = ?))
            )
            ORDER BY ec.fecha_inicio
        ", [$instId, $anioId, $seccionId, $seccionId]);
    }

    public function getForCalendar(int $instId, int $anioId, int $seccionId): array {
        $events = $this->getParaPadre($instId, $anioId, $seccionId);
        $result = [];
        foreach ($events as $e) {
            $result[] = [
                'id'      => $e['id'],
                'title'   => $e['titulo'],
                'start'   => $e['fecha_inicio'],
                'end'     => $e['fecha_fin'] ?: $e['fecha_inicio'],
                'allDay'  => (bool)$e['todo_el_dia'],
                'color'   => $e['color'],
                'extendedProps' => [
                    'tipo'        => $e['tipo'],
                    'descripcion' => $e['descripcion'] ?? '',
                    'asignatura'  => $e['asignatura_nombre'] ?? '',
                    'docente'     => $e['creado_por_nombre'] ?? ''
                ]
            ];
        }
        return $result;
    }
}