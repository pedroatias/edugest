<?php
class NotificationHelper {
    public static function enviarAUsuario(int $usuarioId, string $titulo, string $mensaje, string $tipo = 'general', string $url = ''): void {
        Database::insert("INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo, url, leida, created_at) VALUES (?, ?, ?, ?, ?, 0, NOW())", [$usuarioId, $titulo, $mensaje, $tipo, $url]);
        $user = Database::fetchOne("SELECT email, nombres FROM usuarios WHERE id = ?", [$usuarioId]);
        if ($user && !empty($user['email'])) {
            self::enviarEmail($user['email'], $user['nombres'], $titulo, $mensaje, $url);
        }
    }
    private static function enviarEmail(string $email, string $nombre, string $titulo, string $mensaje, string $url = ''): void {
        $asunto = '[EduGest] ' . $titulo;
        $link = $url ? "<p><a href='{$url}' style='background:#1a73e8;color:#fff;padding:8px 16px;text-decoration:none;border-radius:4px;'>Ver detalle</a></p>" : '';
        $nb = htmlspecialchars($nombre);
        $msg = nl2br(htmlspecialchars($mensaje));
        $body = "<html><body style='font-family:Arial,sans-serif;'><div style='max-width:500px;margin:auto;background:#fff;padding:30px;'><h2 style='color:#1a73e8;'>{$titulo}</h2><p>Hola, <strong>{$nb}</strong>:</p><p>{$msg}</p>{$link}<hr><small style='color:#999;'>EduGest</small></div></body></html>";
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\nFrom: EduGest <noreply@edugest.local>\r\n";
        @mail($email, $asunto, $body, $headers);
    }
}
