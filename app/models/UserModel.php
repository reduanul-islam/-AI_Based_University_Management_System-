<?php
require_once BASE_PATH . '/core/Model.php';

class UserModel extends Model {
    protected string $table = 'users';

    public function findByEmail(string $email): array|false {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public function findWithProfile(int $id): array|false {
        return $this->db->fetch(
            "SELECT u.*, s.id as student_profile_id, s.student_id as student_code,
                    s.cgpa, s.semester, s.department_id,
                    f.id as faculty_profile_id, f.employee_id, f.designation,
                    d.name as department_name
             FROM users u
             LEFT JOIN students s ON s.user_id = u.id
             LEFT JOIN faculty f ON f.user_id = u.id
             LEFT JOIN departments d ON (d.id = s.department_id OR d.id = f.department_id)
             WHERE u.id = ?",
            [$id]
        );
    }

    public function getAllWithRoles(): array {
        return $this->db->fetchAll(
            "SELECT u.*, d.name as dept_name FROM users u
             LEFT JOIN students s ON s.user_id = u.id
             LEFT JOIN faculty f ON f.user_id = u.id
             LEFT JOIN departments d ON (d.id = s.department_id OR d.id = f.department_id)
             ORDER BY u.created_at DESC"
        );
    }

    public function updatePassword(int $id, string $password): int {
        return $this->update($id, ['password' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function toggleActive(int $id): void {
        $user = $this->find($id);
        if ($user) {
            $this->update($id, ['is_active' => $user['is_active'] ? 0 : 1]);
        }
    }
}
