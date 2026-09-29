<?php

namespace App\Helpers;

/**
 * Helper para manejo de archivos
 */
class File
{
    /**
     * Valida si un archivo es una imagen válida
     */
    public static function isValidImage(array $file, array $allowedTypes, int $maxSize): bool
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        return in_array($mimeType, $allowedTypes);
    }
    
    /**
     * Genera un nombre único para el archivo
     */
    public static function generateUniqueName(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return uniqid('', true) . '_' . time() . '.' . $extension;
    }
    
    /**
     * Mueve un archivo subido a su destino
     */
    public static function upload(array $file, string $destination): bool
    {
        return move_uploaded_file($file['tmp_name'], $destination);
    }
    
    /**
     * Elimina un archivo
     */
    public static function delete(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
