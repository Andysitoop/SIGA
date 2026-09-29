<?php

namespace App\Helpers;

/**
 * Helper para protección CSRF
 */
class Csrf
{
    /**
     * Genera un token CSRF
     */
    public static function generateToken(): string
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica si el token CSRF es válido
     */
    public static function validateToken(string $token): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Genera el campo oculto del token CSRF para formularios
     */
    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
