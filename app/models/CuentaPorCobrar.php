<?php
class CuentaPorCobrar extends Model {
    protected string $table = 'cuentas_por_cobrar';
    protected array $fillable = ['matricula_id','concepto_id','anio_lectivo_id','descripcion','valor','descuento','interes_mora','total','fecha_vencimiento','estado'];

    public function getPendientesByMatricula(int $matriculaId): array {
        return Database::fetchAll("
            SELECT cpc.*, cc.nombre AS concepto, cc.tipo
            FROM cuentas_por_cobrar cpc
            INNER JOIN conceptos_cobro cc ON cc.id = cpc.concepto_id
            WHERE cpc.matricula_id = ? AND cpc.estado IN ('pendiente','parcial')
            ORDER BY cpc.fecha_vencimiento
        ", [$matriculaId]);
    }

    public function getMorosos(int $instId, int $anioId, ?int $gradoId = null, ?int $seccionId = null): array {
        $sql = "
            SELECT e.id, CONCAT(e.nombres,' ',e.apellidos) AS estudiante, e.codigo,
                   g.nombre AS grado, s.nombre_completo AS seccion,
                   COUNT(cpc.id) AS num_cuotas,
                   SUM(cpc.total) AS total_deuda,
                   MIN(cpc.fecha_vencimiento) AS primera_vencida,
                   DATEDIFF(CURDATE(), MIN(cpc.fecha_vencimiento)) AS dias_mora
            FROM cuentas_por_cobrar cpc
            INNER JOIN matriculas m ON m.id = cpc.matricula_id
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            INNER JOIN secciones s ON s.id = m.seccion_id
            INNER JOIN grados g ON g.id = s.grado_id
            WHERE e.institucion_id = ? AND m.anio_lectivo_id = ?
            AND cpc.estado IN ('pendiente','parcial') AND cpc.fecha_vencimiento < CURDATE()";
        $params = [$instId, $anioId];
        if ($gradoId)   { $sql .= " AND g.id = ?"; $params[] = $gradoId; }
        if ($seccionId) { $sql .= " AND s.id = ?"; $params[] = $seccionId; }
        $sql .= " GROUP BY e.id ORDER BY total_deuda DESC";
        return Database::fetchAll($sql, $params);
    }

    public function generarPensiones(int $instId, int $anioId, int $conceptoId): int {
        $matriculas = Database::fetchAll("
            SELECT m.id FROM matriculas m
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            WHERE e.institucion_id = ? AND m.anio_lectivo_id = ? AND m.estado = 'matriculado'
        ", [$instId, $anioId]);
        $concepto = Database::fetchOne("SELECT * FROM conceptos_cobro WHERE id = ?", [$conceptoId]);
        if (!$concepto) return 0;
        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre'];
        $year  = Database::fetchValue("SELECT anio FROM anios_lectivos WHERE id = ?", [$anioId]);
        $count = 0;
        foreach ($matriculas as $mat) {
            foreach ($meses as $i => $mes) {
                $month = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                $vence = "$year-$month-10";
                $existing = Database::fetchOne("SELECT id FROM cuentas_por_cobrar WHERE matricula_id=? AND descripcion=?",
                    [$mat['id'], "Pension $mes de $year"]);
                if (!$existing) {
                    Database::insert("INSERT INTO cuentas_por_cobrar (matricula_id,concepto_id,anio_lectivo_id,descripcion,valor,total,fecha_vencimiento,estado) VALUES (?,?,?,?,?,?,?,'pendiente')",
                        [$mat['id'], $conceptoId, $anioId, "Pension $mes de $year", $concepto['valor'], $concepto['valor'], $vence]);
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getEstadoCuenta(int $matriculaId, int $anioId): array {
        return Database::fetchAll("
            SELECT cpc.*, cc.nombre AS concepto, cc.tipo,
                   COALESCE(SUM(p.valor_pagado),0) AS pagado
            FROM cuentas_por_cobrar cpc
            INNER JOIN conceptos_cobro cc ON cc.id = cpc.concepto_id
            LEFT JOIN pagos p ON p.cuenta_id = cpc.id AND p.estado = 'verificado'
            WHERE cpc.matricula_id = ? AND cpc.anio_lectivo_id = ?
            GROUP BY cpc.id
            ORDER BY cpc.fecha_vencimiento
        ", [$matriculaId, $anioId]);
    }
}