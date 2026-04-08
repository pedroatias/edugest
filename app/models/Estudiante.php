<?php
class Estudiante extends Model {
    protected string $table = 'estudiantes';
    protected array $fillable = ['usuario_id','institucion_id','codigo','numero_documento','tipo_documento','nombres','apellidos','fecha_nacimiento','genero','foto','email','telefono','direccion','ciudad','estado'];

    public function getConMatricula(int $instId, int $anioId): array {
        return Database::fetchAll("
            SELECT e.*, m.id AS matricula_id, m.estado AS estado_matricula,
                   s.nombre_completo AS seccion, g.nombre AS grado,
                   CONCAT(e.nombres,' ',e.apellidos) AS nombre_completo
            FROM estudiantes e
            INNER JOIN matriculas m ON m.estudiante_id = e.id AND m.anio_lectivo_id = ?
            INNER JOIN secciones s ON s.id = m.seccion_id
            INNER JOIN grados g ON g.id = s.grado_id
            WHERE e.institucion_id = ? AND e.estado = 'activo'
            ORDER BY g.orden, s.nombre, e.apellidos, e.nombres
        ", [$anioId, $instId]);
    }

    public function getByPadre(int $usuarioId): array {
        return Database::fetchAll("
            SELECT e.*, m.id AS matricula_id, m.seccion_id,
                   s.nombre_completo AS seccion, g.nombre AS grado,
                   al.anio AS anio_lectivo, al.id AS anio_lectivo_id
            FROM estudiantes e
            INNER JOIN representantes r ON r.estudiante_id = e.id AND r.usuario_id = ?
            INNER JOIN matriculas m ON m.estudiante_id = e.id
            INNER JOIN anios_lectivos al ON al.id = m.anio_lectivo_id AND al.activo = 1
            INNER JOIN secciones s ON s.id = m.seccion_id
            INNER JOIN grados g ON g.id = s.grado_id
            WHERE e.estado = 'activo'
            ORDER BY e.nombres
        ", [$usuarioId]);
    }

    public function getBySeccion(int $seccionId, int $anioId): array {
        return Database::fetchAll("
            SELECT e.*, m.id AS matricula_id,
                   CONCAT(e.nombres,' ',e.apellidos) AS nombre_completo
            FROM estudiantes e
            INNER JOIN matriculas m ON m.estudiante_id = e.id
            WHERE m.seccion_id = ? AND m.anio_lectivo_id = ? AND e.estado = 'activo'
            ORDER BY e.apellidos, e.nombres
        ", [$seccionId, $anioId]);
    }

    public function generateCodigo(int $instId, int $anio): string {
        $count = $this->count("institucion_id = ?", [$instId]) + 1;
        return $anio . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function search(int $instId, string $term): array {
        $like = '%' . $term . '%';
        return Database::fetchAll("
            SELECT e.id, CONCAT(e.nombres,' ',e.apellidos) AS nombre_completo,
                   e.codigo, e.numero_documento
            FROM estudiantes e
            WHERE e.institucion_id = ? AND e.estado = 'activo'
            AND (e.nombres LIKE ? OR e.apellidos LIKE ? OR e.codigo LIKE ? OR e.numero_documento LIKE ?)
            LIMIT 20
        ", [$instId, $like, $like, $like, $like]);
    }
}
