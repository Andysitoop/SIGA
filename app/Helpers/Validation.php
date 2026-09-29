<?php

namespace App\Helpers;

/**
 * Helper para validaciones
 */
class Validation
{
    /**
     * Valida si un campo está vacío
     */
    public static function required($value): bool
    {
        return !empty($value);
    }
    
    /**
     * Valida si es un email válido
     */
    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Valida si es un número
     */
    public static function numeric($value): bool
    {
        return is_numeric($value);
    }
    
    /**
     * Valida si un número está en un rango
     */
    public static function between($value, float $min, float $max): bool
    {
        $num = (float)$value;
        return $num >= $min && $num <= $max;
    }
    
    /**
     * Valida una fecha
     */
    public static function date(string $value): bool
    {
        return (bool)strtotime($value);
    }
    
    /**
     * Valida longitud mínima
     */
    public static function minLength(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }
    
    /**
     * Valida longitud máxima
     */
    public static function maxLength(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }
    
    /**
     * Sanitiza una cadena para salida HTML
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
