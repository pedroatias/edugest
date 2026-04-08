<?php
class Notificacion extends Model {
    protected string $table = 'notificaciones';
    protected array $fillable = ['usuario_id','titulo','mensaje','tipo','url','leida','push_enviado'];

    public function getNoLeidas(int $usuarioId): array {
        return Database::fetchAll("SELECT * FROM notificaciones WHERE usuario_id=? AND leida=0 ORDER BY created_at DESC LIMIT 20", [$usuarioId]);
    }

    public function countNoLeidas(int $usuarioId): int {
        return (int)Database::fetchValue("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=? AND leida=0", [$usuarioId]);
    }

    public function marcarLeida(int $id, int $usuarioId): void {
        Database::execute("UPDATE notificaciones SET leida=1 WHERE id=? AND usuario_id=?", [$id, $usuarioId]);
    }

    public function marcarTodasLeidas(int $usuarioId): void {
        Database::execute("UPDATE notificaciones SET leida=1 WHERE usuario_id=?", [$usuarioId]);
    }

    public function crear(int $usuarioId, string $titulo, string $mensaje, string $tipo = 'sistema', string $url = ''): int {
        return (int)Database::insert("INSERT INTO notificaciones (usuario_id,titulo,mensaje,tipo,url) VALUES (?,?,?,?,?)",
            [$usuarioId, $titulo, $mensaje, $tipo, $url]);
    }

    public function crearMasivo(array $usuarioIds, string $titulo, string $mensaje, string $tipo = 'sistema', string $url = ''): int {
        $count = 0;
        foreach ($usuarioIds as $uid) {
            $this->crear($uid, $titulo, $mensaje, $tipo, $url);
            $count++;
        }
        return $count;
    }

    public function getPadresPorSeccion(int $seccionId, int $anioId): array {
        return Database::fetchAll("
            SELECT DISTINCT u.id
            FROM usuarios u
            INNER JOIN representantes r ON r.usuario_id = u.id
            INNER JOIN matriculas m ON m.estudiante_id = r.estudiante_id
            WHERE m.seccion_id = ? AND m.anio_lectivo_id = ? AND u.activo = 1
        ", [$seccionId, $anioId]);
    }
}
