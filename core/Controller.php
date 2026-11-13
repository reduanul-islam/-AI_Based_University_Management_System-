<?php
class Controller {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->startSession();
    }

    private function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_start();
        }
    }

    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void {
        extract($data);
        $viewFile = BASE_PATH . "/app/views/{$viewPath}.php";
        if (!file_exists($viewFile)) {
            die("View not found: $viewPath");
        }

        if ($layout) {
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            include BASE_PATH . "/app/views/layouts/{$layout}.php";
        } else {
            include $viewFile;
        }
    }

    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void {
        // Convert /path to ?url=path format
        $urlParam = ltrim($url, "/");
        $location = BASE_URL . ($urlParam ? "?url=" . $urlParam : "");
        header("Location: " . $location);
        exit;
    }

    protected function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth(string ...$roles): void {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
        if (!empty($roles) && !in_array($_SESSION['role'], $roles)) {
            http_response_code(403);
            include BASE_PATH . '/app/views/layouts/unauthorized.php';
            exit;
        }
    }

    protected function currentUser(): array|false {
        if (!isset($_SESSION['user_id'])) return false;
        return $this->db->fetch(
            "SELECT u.*, s.id as student_profile_id, s.student_id as student_code, s.cgpa, s.semester,
                    f.id as faculty_profile_id, f.employee_id, f.designation, d.name as department_name
             FROM users u
             LEFT JOIN students s ON s.user_id = u.id
             LEFT JOIN faculty f ON f.user_id = u.id
             LEFT JOIN departments d ON (d.id = s.department_id OR d.id = f.department_id)
             WHERE u.id = ?",
            [$_SESSION['user_id']]
        );
    }

    protected function flash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key): ?string {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    protected function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): bool {
        return isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
    }
}
