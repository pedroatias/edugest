<?php
class Usuario extends Model {
    protected string $table = 'usuarios';
    protected array $fillable = ['institucion_id','numero_documento','tipo_documento','username','password','rol','nombres','apellidos','email','telefono','foto','activo'];

    public function findByUsername(string $username): array|false {
        return Database::fetchOne("SELECT * FROM usuarios WHERE username = ? AND activo = 1 LIMIT 1", [$username]);
    }

    public function findByDocumento(string $doc): array|false {
        return Database::fetchOne("SELECT * FROM usuarios WHERE numero_documento = ? AND activo = 1 LIMIT 1", [$doc]);
    }

    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function updateLastAccess(int $id): void {
        Database::execute("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?", [$id]);
    }

    public function getByInstitucion(int $instId, string $rol = ''): array {
        $sql = "SELECT u.*, CONCAT(u.nombres,' ',u.apellidos) AS nombre_completo FROM usuarios u WHERE u.institucion_id = ? AND u.activo = 1";
        $params = [$instId];
        if ($rol) { $sql .= " AND u.rol = ?"; $params[] = $rol; }
        $sql .= " ORDER BY u.apellidos, u.nombres";
        return Database::fetchAll($sql, $params);
    }

    public function changePassword(int $id, string $newPassword): bool {
        $hash = $this->hashPassword($newPassword);
        return Database::execute("UPDATE usuarios SET password = ? WHERE id = ?", [$hash, $id]) > 0;
    }
}