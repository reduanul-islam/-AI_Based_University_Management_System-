<?php
require_once BASE_PATH . '/core/Model.php';

class StudentModel extends Model {
    protected string $table = 'students';

    public function findByUserId(int $userId): array|false {
        return $this->db->fetch(
            "SELECT s.*, u.name, u.email, d.name as dept_name
             FROM students s
             JOIN users u ON u.id = s.user_id
             LEFT JOIN departments d ON d.id = s.department_id
             WHERE s.user_id = ?",
            [$userId]
        );
    }

    public function getAllWithDetails(string $search = '', int $deptId = 0): array {
        $where = []; $params = [];
        if ($search) { $where[] = "(u.name LIKE ? OR s.student_id LIKE ? OR u.email LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($deptId) { $where[] = "s.department_id = ?"; $params[] = $deptId; }
        $sql = "SELECT s.*, u.name, u.email, u.is_active, d.name as dept_name
                FROM students s JOIN users u ON u.id=s.user_id
                LEFT JOIN departments d ON d.id=s.department_id"
            . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
            . " ORDER BY s.id DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function recalcCgpa(int $studentId): void {
        $row = $this->db->fetch(
            "SELECT SUM(c.credits * e.grade_point) / SUM(c.credits) as cgpa
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             WHERE e.student_id=? AND e.grade_point IS NOT NULL",
            [$studentId]
        );
        if ($row && $row['cgpa'] !== null) {
            $this->update($studentId, ['cgpa' => round((float)$row['cgpa'], 2)]);
        }
    }

    public function getEnrollments(int $studentId): array {
        return $this->db->fetchAll(
            "SELECT e.*, c.title, c.course_code, c.credits,
                    u.name as faculty_name
             FROM enrollments e
             JOIN courses c ON c.id = e.course_id
             LEFT JOIN faculty f ON f.id = e.faculty_id
             LEFT JOIN users u ON u.id = f.user_id
             WHERE e.student_id = ? ORDER BY c.title",
            [$studentId]
        );
    }
}
