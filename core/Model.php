<?php
abstract class Model {
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): array|false {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function all(string $orderBy = ''): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        return $this->db->fetchAll($sql);
    }

    public function create(array $data): int {
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int {
        return $this->db->update(
            $this->table, $data,
            "{$this->primaryKey} = ?", [$id]
        );
    }

    public function delete(int $id): int {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?", [$id]
        );
    }

    public function paginate(int $page, int $perPage, string $where = '', array $params = [], string $order = 'id DESC'): array {
        $offset = ($page - 1) * $perPage;
        $whereSql = $where ? "WHERE $where" : '';
        $total = $this->db->count("SELECT COUNT(*) FROM {$this->table} $whereSql", $params);
        $rows  = $this->db->fetchAll(
            "SELECT * FROM {$this->table} $whereSql ORDER BY $order LIMIT $perPage OFFSET $offset",
            $params
        );
        return [
            'data'        => $rows,
            'total'       => $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'last_page'   => (int)ceil($total / $perPage),
        ];
    }
}
