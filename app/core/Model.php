<?php
/**
 * EduGest - Base Model
 */
class Model {
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    protected function db(): PDO {
        return Database::getInstance();
    }

    public function find(int $id): array|false {
        return Database::fetchOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function findBy(string $column, mixed $value): array|false {
        return Database::fetchOne(
            "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );
    }

    public function all(string $orderBy = '', string $where = '', array $params = []): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        return Database::fetchAll($sql, $params);
    }

    public function create(array $data): string|false {
        $data = $this->filterFillable($data);
        if (empty($data)) return false;
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})";
        return Database::insert($sql, array_values($data));
    }

    public function update(int $id, array $data): int {
        $data = $this->filterFillable($data);
        if (empty($data)) return 0;
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";
        $values = array_values($data);
        $values[] = $id;
        return Database::execute($sql, $values);
    }

    public function delete(int $id): int {
        return Database::execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function softDelete(int $id, string $column = 'activo'): int {
        return Database::execute(
            "UPDATE {$this->table} SET {$column} = 0 WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";
        return (int)Database::fetchValue($sql, $params);
    }

    public function paginate(int $page, int $perPage, string $where = '', array $params = [], string $orderBy = ''): array {
        $offset = ($page - 1) * $perPage;
        $total  = $this->count($where, $params);
        $sql = "SELECT * FROM {$this->table}";
        if ($where) $sql .= " WHERE {$where}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        return [
            'data'       => Database::fetchAll($sql, $params),
            'total'      => $total,
            'per_page'   => $perPage,
            'page'       => $page,
            'last_page'  => (int)ceil($total / $perPage),
        ];
    }

    private function filterFillable(array $data): array {
        if (empty($this->fillable)) return $data;
        return array_intersect_key($data, array_flip($this->fillable));
    }
}
