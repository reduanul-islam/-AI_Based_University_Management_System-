<?php
class Router {
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void {
        $this->routes['GET'][$path] = [$controller, $method];
    }

    public function post(string $path, string $controller, string $method): void {
        $this->routes['POST'][$path] = [$controller, $method];
    }

    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // ?url= query parameter theke path poro
        $url = $_GET['url'] ?? '';
        $url = trim($url, '/');

        // Kono url na thakle — login page e pathao (session thakle AuthController e redirect korbe)
        if ($url === '') {
            $url = 'login';
        }

        $requestUri = '/' . $url;

        // Exact match
        if (isset($this->routes[$requestMethod][$requestUri])) {
            [$controllerName, $action] = $this->routes[$requestMethod][$requestUri];
            $this->callController($controllerName, $action, []);
            return;
        }

        // Pattern match (e.g. /faculty/courses/5/students)
        foreach ($this->routes[$requestMethod] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $requestUri, $matches)) {
                array_shift($matches);
                [$controllerName, $action] = $handler;
                $this->callController($controllerName, $action, $matches);
                return;
            }
        }

        // 404
        http_response_code(404);
        include BASE_PATH . '/app/views/layouts/404.php';
    }

    private function callController(string $controllerName, string $action, array $params): void {
        $file = BASE_PATH . "/app/controllers/{$controllerName}.php";
        if (!file_exists($file)) {
            die("Controller not found: $controllerName");
        }
        require_once $file;
        $controller = new $controllerName();
        if (!method_exists($controller, $action)) {
            die("Method '$action' not found in '$controllerName'");
        }
        call_user_func_array([$controller, $action], $params);
    }
}
