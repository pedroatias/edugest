<?php
class Boletin extends Model {
    protected string $table='boletines';
    protected array $fillable=['matricula_id','periodo_id','archivo_pdf','fecha_generacion','fecha_disponible','disponible','generado'];
    public function getByMatricula(int $mid): array {
        return Database::fetchAll("SELECT b.*,pa.nombre AS periodo,pa.numero FROM boletines b INNER JOIN periodos_academicos pa ON pa.id=b.periodo_id WHERE b.matricula_id=? ORDER BY pa.numero",[$mid]);
    }
    public function getPendientesGeneracion(int $iid,int $aid,?int $pid=null): array {
        $sql="SELECT m.id AS matricula_id,e.codigo,CONCAT(e.nombres,' ',e.apellidos) AS estudiante,s.nombre_completo AS seccion,pa.nombre AS periodo,pa.id AS periodo_id,b.id AS boletin_id,b.generado,b.disponible FROM matriculas m INNER JOIN estudiantes e ON e.id=m.estudiante_id INNER JOIN secciones s ON s.id=m.seccion_id INNER JOIN periodos_academicos pa ON pa.anio_lectivo_id=m.anio_lectivo_id LEFT JOIN boletines b ON b.matricula_id=m.id AND b.periodo_id=pa.id WHERE e.institucion_id=? AND m.anio_lectivo_id=?";
        $p=[$iid,$aid];if($pid){$sql.=" AND pa.id=?";$p[]=$pid;}$sql.=" ORDER BY s.nombre_completo,e.apellidos,pa.numero";
        return Database::fetchAll($sql,$p);
    }
}