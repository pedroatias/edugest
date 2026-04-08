<?php
/**
 * EduGest - Router
 * Maneja el enrutamiento de URLs a controladores
 */
class Router {
    private array $routes = [];

    public function get(string $path, string $controller, string $method): void {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method): void {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(): void {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_GET['url'] ?? '';
        $uri = trim($uri, '/');
        $uri = strtok($uri, '?');

        if (isset($this->routes[$httpMethod][$uri])) {
            $route = $this->routes[$httpMethod][$uri];
            $this->loadController($route['controller'], $route['method']);
        } else {
            $this->notFound();
        }
    }

    private function loadController(string $controllerPath, string $method): void {
        $file = APP_PATH . '/controllers/' . $controllerPath . '.php';
        if (!file_exists($file)) {
            $this->notFound();
            return;
        }
        require_once $file;
        $parts = explode('/', $controllerPath);
        $className = end($parts);
        if (!class_exists($className)) {
            $this->notFound();
            return;
        }
        $controller = new $className();
        if (!method_exists($controller, $method)) {
            $this->notFound();
            return;
        }
        $controller->{$method}();
    }

    private function notFound(): void {
        http_response_code(404);
        $file = APP_PATH . '/views/errors/404.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            echo '<h1>404 - Página no encontrada</h1>';
        }
    }
}
