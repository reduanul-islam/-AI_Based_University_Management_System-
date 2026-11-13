<?php
require_once BASE_PATH . '/core/Controller.php';

class AuthController extends Controller {

    public function loginPage(): void {
        // Already logged in hole dashboard e pathao
        if ($this->isLoggedIn()) {
            $this->redirectByRole($_SESSION['role']);
        }
        $this->view('auth/login', ['title' => 'Login — UniVerse AI'], null);
    }

    public function login(): void {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->json(['success' => false, 'message' => 'Email and password are required.'], 400);
        }

        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ? AND is_active = 1",
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            $this->json(['success' => false, 'message' => 'Invalid credentials. Please try again.'], 401);
        }

        // Session fixation prevent korte nতুন session ID generate koro
        session_regenerate_id(true);

        // Session set koro
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['role']      = $user['role'];

        // Last login update koro
        $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        $redirect = match($user['role']) {
            'admin'   => 'admin/dashboard',
            'faculty' => 'faculty/dashboard',
            default   => 'student/dashboard',
        };

        $this->json(['success' => true, 'redirect' => BASE_URL . '?url=' . $redirect]);
    }

    public function logout(): void {
        // Session completely destroy koro
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        $this->redirect('/login');
    }

    private function redirectByRole(string $role): void {
        $this->redirect(match($role) {
            'admin'   => '/admin/dashboard',
            'faculty' => '/faculty/dashboard',
            default   => '/student/dashboard',
        });
    }
}
