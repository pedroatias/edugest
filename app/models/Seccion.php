<?php
class Seccion extends Model {
    protected string $table = 'secciones';
    protected array $fillable = ['grado_id','anio_lectivo_id','nombre','nombre_completo','capacidad_max','docente_director_id','activa'];

    public function getConGrado(int $instId, int $anioId): array {
        return Database::fetchAll("
            SELECT s.*, g.nombre AS grado, g.nivel, g.orden AS grado_orden,
                   CONCAT(g.nombre,'-',s.nombre) AS nombre_completo_calc,
                   COALESCE(s.nombre_completo, CONCAT(g.nombre,'-',s.nombre)) AS display_name,
                   u.nombres AS director_nombres, u.apellidos AS director_apellidos,
                   COUNT(m.id) AS num_estudiantes
            FROM secciones s
            INNER JOIN grados g ON g.id = s.grado_id
            LEFT JOIN usuarios u ON u.id = s.docente_director_id
            LEFT JOIN matriculas m ON m.seccion_id = s.id AND m.anio_lectivo_id = s.anio_lectivo_id AND m.estado = 'matriculado'
            WHERE s.anio_lectivo_id = ? AND g.institucion_id = ? AND s.activa = 1
            GROUP BY s.id
            ORDER BY g.orden, s.nombre
        ", [$anioId, $instId]);
    }

    public function getByDocente(int $docenteId, int $anioId): array {
        return Database::fetchAll("
            SELECT DISTINCT s.*, g.nombre AS grado,
                   COALESCE(s.nombre_completo, CONCAT(g.nombre,'-',s.nombre)) AS display_name
            FROM secciones s
            INNER JOIN grados g ON g.id = s.grado_id
            INNER JOIN asignacion_docentes ad ON ad.seccion_id = s.id
            INNER JOIN docentes d ON d.id = ad.docente_id
            WHERE d.usuario_id = ? AND ad.anio_lectivo_id = ? AND s.activa = 1
            ORDER BY g.orden, s.nombre
        ", [$docenteId, $anioId]);
    }
}
