<?php
require_once BASE_PATH . '/core/Model.php';

class NoticeModel extends Model {
    protected string $table = 'notices';

    public function getAllWithAuthor(string $role = ''): array {
        $where = $role ? "WHERE n.target_role IN ('all', '$role')" : '';
        return $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n
             JOIN users u ON u.id=n.posted_by
             $where
             ORDER BY n.is_pinned DESC, n.created_at DESC"
        );
    }

    public function getRecent(int $limit = 5, string $role = 'all'): array {
        return $this->db->fetchAll(
            "SELECT n.*, u.name as posted_by_name FROM notices n
             JOIN users u ON u.id=n.posted_by
             WHERE n.target_role IN ('all', ?)
             ORDER BY n.is_pinned DESC, n.created_at DESC LIMIT ?",
            [$role, $limit]
        );
    }
}
