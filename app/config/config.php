<?php
/**
 * EduGest - Configuración principal
 * IMPORTANTE: Editar estos valores después de la instalación
 * o usar el asistente install.php
 */

// ============================================================
// BASE DE DATOS
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'tu_base_de_datos');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contrasena');
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// APLICACIÓN
// ============================================================
define('APP_NAME', 'EduGest');
define('APP_URL', 'http://localhost/edugest'); // Sin slash final
define('APP_ENV', 'production'); // development | production
define('APP_DEBUG', false);
define('APP_TIMEZONE', 'America/Bogota');

// ============================================================
// SESIÓN
// ============================================================
define('SESSION_NAME', 'edugest_session');
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_INACTIVITY_WARNING', 120); // Advertir 2 min antes

// ============================================================
// UPLOADS
// ============================================================
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('UPLOADS_URL', APP_URL . '/uploads');
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf']);

// ============================================================
// CORREO ELECTRÓNICO (SMTP)
// ============================================================
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'tu_correo@gmail.com');
define('MAIL_PASS', 'tu_app_password');
define('MAIL_FROM', 'tu_correo@gmail.com');
define('MAIL_FROM_NAME', APP_NAME);
define('MAIL_ENCRYPTION', 'tls');

// ============================================================
// WEB PUSH NOTIFICATIONS (VAPID Keys)
// Generar en: https://vapidkeys.com/
// ============================================================
define('VAPID_PUBLIC_KEY', 'TU_VAPID_PUBLIC_KEY_AQUI');
define('VAPID_PRIVATE_KEY', 'TU_VAPID_PRIVATE_KEY_AQUI');
define('VAPID_SUBJECT', 'mailto:admin@tuinstituto.edu');

// ============================================================
// PASARELA DE PAGOS - WOMPI
// ============================================================
define('WOMPI_PUBLIC_KEY', 'pub_test_XXXXXXXX');
define('WOMPI_PRIVATE_KEY', 'prv_test_XXXXXXXX');
define('WOMPI_ENV', 'sandbox'); // sandbox | production
define('WOMPI_WEBHOOK_SECRET', 'TU_WEBHOOK_SECRET');

// ============================================================
// SISTEMA DE CALIFICACIONES
// ============================================================
define('NOTA_MINIMA', 0.0);
define('NOTA_MAXIMA', 5.0);
define('NOTA_APROBACION', 3.0);
define('DECIMALES_NOTA', 2);

// ============================================================
// PDF
// ============================================================
define('PDF_AUTHOR', APP_NAME);
define('PDF_CREATOR', APP_NAME . ' v' . VERSION);

// ============================================================
// SEGURIDAD
// ============================================================
define('HASH_ALGO', PASSWORD_BCRYPT);
define('HASH_COST', 12);
define('CSRF_TOKEN_NAME', '_token');

// Timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
