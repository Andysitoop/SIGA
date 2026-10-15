<?php

namespace App\Helpers;

class Access
{
    public static function requireLogin(): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (($_SESSION['logged_in'] ?? false) !== true) {
            header('Location: /login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Acceso denegado. Se requiere una cuenta de administrador.');
        }
    }
}
