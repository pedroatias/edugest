<?php
class Pago extends Model {
    protected string $table = 'pagos';
    protected array $fillable = ['cuenta_id','matricula_id','referencia','metodo_pago','pasarela','referencia_pasarela','valor_pagado','fecha_pago','estado','comprobante','registrado_por','observaciones'];

    public function getByMatricula(int $matriculaId, int $anioId = 0): array {
        $sql = "SELECT p.*, cpc.descripcion AS concepto_desc, cc.nombre AS concepto, cc.tipo AS concepto_tipo, cpc.fecha_vencimiento
            FROM pagos p
            INNER JOIN cuentas_por_cobrar cpc ON cpc.id = p.cuenta_id
            INNER JOIN conceptos_cobro cc ON cc.id = cpc.concepto_id
            WHERE p.matricula_id = ? AND p.estado = 'verificado'";
        $params = [$matriculaId];
        if ($anioId) { $sql .= " AND cpc.anio_lectivo_id = ?"; $params[] = $anioId; }
        $sql .= " ORDER BY p.fecha_pago DESC";
        return Database::fetchAll($sql, $params);
    }

    public function getPendientesValidacion(int $instId): array {
        return Database::fetchAll("
            SELECT p.*, cpc.descripcion AS concepto_desc, cc.nombre AS concepto,
                   CONCAT(e.nombres,' ',e.apellidos) AS estudiante, e.codigo,
                   s.nombre_completo AS seccion
            FROM pagos p
            INNER JOIN cuentas_por_cobrar cpc ON cpc.id = p.cuenta_id
            INNER JOIN conceptos_cobro cc ON cc.id = cpc.concepto_id
            INNER JOIN matriculas m ON m.id = p.matricula_id
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            INNER JOIN secciones s ON s.id = m.seccion_id
            WHERE p.estado = 'pendiente' AND e.institucion_id = ?
            ORDER BY p.created_at DESC
        ", [$instId]);
    }

    public function verificar(int $id, int $adminId, string $obs = ''): bool {
        $pago = $this->find($id);
        if (!$pago) return false;
        Database::beginTransaction();
        try {
            Database::execute("UPDATE pagos SET estado='verificado', registrado_por=?, observaciones=?, updated_at=NOW() WHERE id=?",
                [$adminId, $obs, $id]);
            Database::execute("UPDATE cuentas_por_cobrar SET estado='pagado' WHERE id=?", [$pago['cuenta_id']]);
            Database::commit();
            return true;
        } catch (Exception $e) {
            Database::rollback();
            return false;
        }
    }

    public function getResumenMensual(int $instId, int $anioId): array {
        return Database::fetchAll("
            SELECT DATE_FORMAT(p.fecha_pago,'%Y-%m') AS mes, COUNT(*) AS cantidad, SUM(p.valor_pagado) AS total
            FROM pagos p
            INNER JOIN matriculas m ON m.id = p.matricula_id
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            WHERE e.institucion_id = ? AND m.anio_lectivo_id = ? AND p.estado = 'verificado'
            GROUP BY mes ORDER BY mes
        ", [$instId, $anioId]);
    }

    public function getTotalRecaudado(int $instId, int $anioId): float {
        return (float)Database::fetchValue("
            SELECT COALESCE(SUM(p.valor_pagado),0)
            FROM pagos p
            INNER JOIN matriculas m ON m.id = p.matricula_id
            INNER JOIN estudiantes e ON e.id = m.estudiante_id
            WHERE e.institucion_id = ? AND m.anio_lectivo_id = ? AND p.estado = 'verificado'
        ", [$instId, $anioId]);
    }
}