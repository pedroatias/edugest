<?php
/**
 * EduGest - Base Controller
 */
class Controller {
    protected function view(string $view, array $data = [], string $layout = 'default'): void {
        extract($data);
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die('View not found: ' . $view);
        }
        // Determine layout based on role
        if ($layout === 'default') {
            $role = Session::userRole();
            $layout = match($role) {
                'admin'   => 'admin',
                'docente' => 'docente',
                'padre'   => 'padre',
                default   => 'auth'
            };
        }
        $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            $content = function() use ($viewFile, $data) {
                extract($data);
                require $viewFile;
            };
            require $layoutFile;
        } else {
            require $viewFile;
        }
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void {
        if (!str_starts_with($url, 'http')) {
            $url = APP_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }

    protected function back(): void {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        $this->redirect($referer);
    }

    protected function input(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function inputInt(string $key, int $default = 0): int {
        return (int)($this->input($key, $default));
    }

    protected function inputFloat(string $key, float $default = 0.0): float {
        return (float)($this->input($key, $default));
    }

    protected function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    protected function requireAuth(string|array $roles = []): void {
        Session::requireLogin();
        if (!empty($roles)) {
            Session::requireRole($roles);
        }
    }

    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function verifyCsrf(): void {
        $token = $this->input(CSRF_TOKEN_NAME) ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!Session::verifyCsrf($token)) {
            if ($this->isAjax()) {
                $this->json(['error' => 'Token CSRF inválido'], 403);
            }
            http_response_code(403);
            die('Token CSRF inválido');
        }
    }

    protected function uploadFile(string $inputName, string $subDir = ''): string|false {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        $file = $_FILES[$inputName];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) return false;
        if ($file['size'] > MAX_UPLOAD_SIZE) return false;
        $dir = UPLOADS_PATH . ($subDir ? '/' . trim($subDir, '/') : '');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = uniqid('', true) . '_' . time() . '.' . $ext;
        $dest = $dir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return ($subDir ? $subDir . '/' : '') . $filename;
        }
        return false;
    }

    protected function success(string $message = '', array $data = []): array {
        return array_merge(['success' => true, 'message' => $message], $data);
    }

    protected function error(string $message = '', int $code = 400): array {
        http_response_code($code);
        return ['success' => false, 'message' => $message];
    }
}
