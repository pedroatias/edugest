<?php
class Calificacion extends Model {
    protected string $table = 'calificaciones';
    protected array $fillable = ['matricula_id','asignatura_id','periodo_id','estructura_nota_id','nota','observacion','docente_id'];

    public function getByMatriculaPeriodo(int $matriculaId, int $periodoId): array {
        return Database::fetchAll("
            SELECT c.*, a.nombre AS asignatura, a.color, en.nombre AS componente, en.porcentaje AS peso_componente
            FROM calificaciones c
            INNER JOIN asignaturas a ON a.id = c.asignatura_id
            INNER JOIN estructura_notas en ON en.id = c.estructura_nota_id
            WHERE c.matricula_id = ? AND c.periodo_id = ?
            ORDER BY a.nombre, en.orden
        ", [$matriculaId, $periodoId]);
    }

    public function getNotasPorAsignatura(int $matriculaId, int $periodoId): array {
        $raw = $this->getByMatriculaPeriodo($matriculaId, $periodoId);
        $grouped = [];
        foreach ($raw as $row) {
            $asig = $row['asignatura_id'];
            if (!isset($grouped[$asig])) {
                $grouped[$asig] = [
                    'asignatura_id' => $asig,
                    'asignatura'    => $row['asignatura'],
                    'color'         => $row['color'],
                    'componentes'   => [],
                    'nota_periodo'  => 0
                ];
            }
            $grouped[$asig]['componentes'][] = [
                'id'         => $row['estructura_nota_id'],
                'nombre'     => $row['componente'],
                'nota'       => (float)$row['nota'],
                'porcentaje' => (float)$row['peso_componente']
            ];
        }
        // Calculate period grade
        foreach ($grouped as &$asig) {
            $total = 0;
            foreach ($asig['componentes'] as $comp) {
                $total += ($comp['nota'] * $comp['porcentaje'] / 100);
            }
            $asig['nota_periodo'] = round($total, 2);
        }
        return array_values($grouped);
    }

    public function getAcumulado(int $matriculaId): array {
        return Database::fetchAll("
            SELECT a.id AS asignatura_id, a.nombre AS asignatura, a.color,
                   pa.id AS periodo_id, pa.nombre AS periodo, pa.numero, pa.porcentaje AS peso_periodo,
                   ROUND(SUM(c.nota * en.porcentaje / 100), 2) AS nota_periodo
            FROM calificaciones c
            INNER JOIN asignaturas a ON a.id = c.asignatura_id
            INNER JOIN periodos_academicos pa ON pa.id = c.periodo_id
            INNER JOIN estructura_notas en ON en.id = c.estructura_nota_id
            WHERE c.matricula_id = ?
            GROUP BY a.id, pa.id
            ORDER BY a.nombre, pa.numero
        ", [$matriculaId]);
    }

    public function guardarNota(int $matriculaId, int $asigId, int $periodoId, int $estructuraId, float $nota, int $docenteId): bool {
        $existing = Database::fetchOne("
            SELECT id FROM calificaciones
            WHERE matricula_id=? AND asignatura_id=? AND periodo_id=? AND estructura_nota_id=?
        ", [$matriculaId, $asigId, $periodoId, $estructuraId]);

        if ($existing) {
            return Database::execute("
                UPDATE calificaciones SET nota=?, docente_id=?, fecha_registro=NOW()
                WHERE id=?
            ", [$nota, $docenteId, $existing['id']]) > 0;
        }
        return Database::insert("
            INSERT INTO calificaciones (matricula_id,asignatura_id,periodo_id,estructura_nota_id,nota,docente_id,fecha_registro)
            VALUES (?,?,?,?,?,?,NOW())
        ", [$matriculaId, $asigId, $periodoId, $estructuraId, $nota, $docenteId]) > 0;
    }
}
