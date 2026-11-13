<?php
require_once BASE_PATH . '/core/Model.php';

class CourseModel extends Model {
    protected string $table = 'courses';

    public function getAllWithDetails(): array {
        return $this->db->fetchAll(
            "SELECT c.*, d.name as dept_name,
             (SELECT COUNT(*) FROM enrollments WHERE course_id=c.id) as enrolled_count
             FROM courses c LEFT JOIN departments d ON d.id=c.department_id
             WHERE c.is_active=1 ORDER BY c.title"
        );
    }

    public function getByDepartment(int $deptId): array {
        return $this->db->fetchAll(
            "SELECT * FROM courses WHERE department_id=? AND is_active=1 ORDER BY title",
            [$deptId]
        );
    }

    public function getForFaculty(int $facultyId): array {
        return $this->db->fetchAll(
            "SELECT DISTINCT c.*, d.name as dept_name,
             (SELECT COUNT(*) FROM enrollments e2 WHERE e2.course_id=c.id AND e2.faculty_id=?) as enrolled_count
             FROM enrollments e JOIN courses c ON c.id=e.course_id
             LEFT JOIN departments d ON d.id=c.department_id
             WHERE e.faculty_id=? GROUP BY c.id ORDER BY c.title",
            [$facultyId, $facultyId]
        );
    }
}
