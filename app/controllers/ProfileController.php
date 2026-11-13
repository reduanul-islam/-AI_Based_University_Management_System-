<?php
require_once BASE_PATH . '/core/Controller.php';

class ProfileController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    public function show(): void {
        $user = $this->currentUser();
        $role = $_SESSION['role'];

        $extra = [];
        if ($role === 'student') {
            $extra = $this->db->fetch(
                "SELECT s.*, d.name as dept_name FROM students s
                 LEFT JOIN departments d ON d.id=s.department_id
                 WHERE s.user_id=?", [$_SESSION['user_id']]
            ) ?: [];
        } elseif ($role === 'faculty') {
            $extra = $this->db->fetch(
                "SELECT f.*, d.name as dept_name FROM faculty f
                 LEFT JOIN departments d ON d.id=f.department_id
                 WHERE f.user_id=?", [$_SESSION['user_id']]
            ) ?: [];
        }

        $this->view('profile/show', [
            'title' => 'My Profile',
            'user'  => $user,
            'extra' => $extra,
        ]);
    }

    public function update(): void {
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!$name) {
            $this->json(['success' => false, 'message' => 'Name is required'], 400);
        }

        $this->db->update('users', ['name' => $name], 'id = ?', [$_SESSION['user_id']]);
        $_SESSION['user_name'] = $name;

        $role = $_SESSION['role'];
        if ($role === 'student') {
            $s = $this->db->fetch("SELECT id FROM students WHERE user_id=?", [$_SESSION['user_id']]);
            if ($s) $this->db->update('students', ['phone' => $phone], 'id=?', [$s['id']]);
        } elseif ($role === 'faculty') {
            $f = $this->db->fetch("SELECT id FROM faculty WHERE user_id=?", [$_SESSION['user_id']]);
            if ($f) $this->db->update('faculty', ['phone' => $phone], 'id=?', [$f['id']]);
        }

        $this->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function changePassword(): void {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$new || !$confirm) {
            $this->json(['success' => false, 'message' => 'All fields required'], 400);
        }
        if ($new !== $confirm) {
            $this->json(['success' => false, 'message' => 'New passwords do not match'], 400);
        }
        if (strlen($new) < 6) {
            $this->json(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
        }

        $user = $this->db->fetch("SELECT password FROM users WHERE id=?", [$_SESSION['user_id']]);
        if (!$user || !password_verify($current, $user['password'])) {
            $this->json(['success' => false, 'message' => 'Current password is incorrect'], 400);
        }

        $this->db->update('users',
            ['password' => password_hash($new, PASSWORD_DEFAULT)],
            'id=?', [$_SESSION['user_id']]
        );

        $this->json(['success' => true, 'message' => 'Password changed successfully']);
    }
}
