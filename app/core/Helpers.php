<?php
/**
 * EduGest - Global Helpers
 */

function url(string $path = ''): string {
    return APP_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function uploads(string $path): string {
    return UPLOADS_URL . '/' . ltrim($path, '/');
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function formatMoney(float $amount, string $symbol = '$'): string {
    return $symbol . ' ' . number_format($amount, 0, ',', '.');
}

function formatDate(string $date, string $format = 'd/m/Y'): string {
    if (!$date) return '';
    return date($format, strtotime($date));
}

function formatDatetime(string $datetime, string $format = 'd/m/Y H:i'): string {
    if (!$datetime) return '';
    return date($format, strtotime($datetime));
}

function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    if ($time < 60)    return 'hace ' . $time . ' seg';
    if ($time < 3600)  return 'hace ' . floor($time/60) . ' min';
    if ($time < 86400) return 'hace ' . floor($time/3600) . ' h';
    return 'hace ' . floor($time/86400) . ' días';
}

function getDayName(int $day): string {
    return match($day) {
        1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo',
        default => ''
    };
}

function getDesempeno(float $nota, array $escala): array {
    foreach ($escala as $nivel) {
        if ($nota >= $nivel['nota_minima'] && $nota <= $nivel['nota_maxima']) {
            return $nivel;
        }
    }
    return ['nombre' => 'N/A', 'color' => '#999'];
}

function generateCode(string $prefix = '', int $length = 8): string {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = $prefix;
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

function sanitizeFilename(string $name): string {
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    return strtolower($name);
}

function csrfField(): string {
    $token = Session::setCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
}

function csrfMeta(): string {
    $token = Session::setCsrfToken();
    return '<meta name="csrf-token" content="' . $token . '">';
}

function flashAlert(): string {
    $types = ['success', 'error', 'warning', 'info'];
    $html = '';
    foreach ($types as $type) {
        $msg = Session::getFlash($type);
        if ($msg) {
            $bsType = $type === 'error' ? 'danger' : $type;
            $html .= '<div class="alert alert-' . $bsType . ' alert-dismissible fade show" role="alert">';
            $html .= e($msg);
            $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            $html .= '</div>';
        }
    }
    return $html;
}

function isActive(string $path): string {
    $current = $_GET['url'] ?? '';
    return str_starts_with(trim($current, '/'), trim($path, '/')) ? 'active' : '';
}

function str_slug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[áàäâ]/u', 'a', $text);
    $text = preg_replace('/[éèëê]/u', 'e', $text);
    $text = preg_replace('/[íìïî]/u', 'i', $text);
    $text = preg_replace('/[óòöô]/u', 'o', $text);
    $text = preg_replace('/[úùüû]/u', 'u', $text);
    $text = preg_replace('/ñ/u', 'n', $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function paginator(array $pagination, string $baseUrl): string {
    if ($pagination['last_page'] <= 1) return '';
    $html = '<nav><ul class="pagination pagination-sm justify-content-center">';
    for ($i = 1; $i <= $pagination['last_page']; $i++) {
        $active = $i === $pagination['page'] ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">';
        $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . $i . '">' . $i . '</a>';
        $html .= '</li>';
    }
    $html .= '</ul></nav>';
    return $html;
}
