<?php
/**
 * EduGest - Session Manager
 */
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
        // Update last activity
        if (isset($_SESSION['user_id'])) {
            $_SESSION['last_activity'] = time();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        session_destroy();
        $_SESSION = [];
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function userId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }

    public static function userRole(): ?string {
        return $_SESSION['user_role'] ?? null;
    }

    public static function userName(): ?string {
        return $_SESSION['user_name'] ?? null;
    }

    public static function userFullName(): ?string {
        return $_SESSION['user_fullname'] ?? null;
    }

    public static function institucionId(): ?int {
        return $_SESSION['institucion_id'] ?? null;
    }

    public static function anioLectivoId(): ?int {
        return $_SESSION['anio_lectivo_id'] ?? null;
    }

    public static function login(array $user, int $anioLectivoId): void {
        session_regenerate_id(true);
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_role']     = $user['rol'];
        $_SESSION['user_name']     = $user['username'];
        $_SESSION['user_fullname'] = $user['nombres'] . ' ' . $user['apellidos'];
        $_SESSION['institucion_id'] = $user['institucion_id'];
        $_SESSION['anio_lectivo_id'] = $anioLectivoId;
        $_SESSION['last_activity'] = time();
    }

    public static function isRole(string|array $roles): bool {
        if (!self::isLoggedIn()) return false;
        $userRole = self::userRole();
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        return $userRole === $roles;
    }

    public static function requireLogin(string $redirectTo = ''): void {
        if (!self::isLoggedIn()) {
            $redirect = $redirectTo ?: APP_URL . '/login';
            header('Location: ' . $redirect);
            exit;
        }
    }

    public static function requireRole(string|array $roles): void {
        self::requireLogin();
        if (!self::isRole($roles)) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function setCsrfToken(): string {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function verifyCsrf(string $token): bool {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    public static function flash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string {
        $msg = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
}
