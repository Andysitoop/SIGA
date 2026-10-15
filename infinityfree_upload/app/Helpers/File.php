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
        $temporaryPath = $file['tmp_name'] ?? null;
        $fileSize = $file['size'] ?? null;

        if (!is_string($temporaryPath) || $temporaryPath === '' || !is_uploaded_file($temporaryPath)) {
            return false;
        }
        
        if (!is_numeric($fileSize) || (int)$fileSize > $maxSize) {
            return false;
        }
        
        $imageInfo = @getimagesize($temporaryPath);
        if ($imageInfo === false || !isset($imageInfo['mime'])) {
            return false;
        }
        
        return in_array($imageInfo['mime'], $allowedTypes, true);
    }

    public static function uploadErrorMessage(int $error): ?string
    {
        return match ($error) {
            UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE => null,
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'La imagen supera el tamaño máximo permitido para la carga.',
            UPLOAD_ERR_PARTIAL => 'La imagen se recibió incompleta. Intenta cargarla nuevamente.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene disponible la carpeta temporal de carga.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo escribir el archivo recibido.',
            UPLOAD_ERR_EXTENSION => 'Una extensión del servidor detuvo la carga de la imagen.',
            default => 'No se pudo recibir la imagen (código de carga ' . $error . ').',
        };
    }
    
    /**
     * Genera un nombre único para el archivo
     */
    public static function generateUniqueName(array $file): string
    {
        $temporaryPath = $file['tmp_name'] ?? '';
        $imageInfo = is_string($temporaryPath) ? @getimagesize($temporaryPath) : false;
        $extension = match ($imageInfo['mime'] ?? null) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new \RuntimeException('Unsupported uploaded image type.'),
        };

        return uniqid('', true) . '_' . time() . '.' . $extension;
    }
    
    /**
     * Mueve un archivo subido a su destino
     */
    public static function upload(array $file, string $destination): bool
    {
        $temporaryPath = $file['tmp_name'] ?? null;
        $directory = dirname($destination);

        if (!is_string($temporaryPath) || !is_uploaded_file($temporaryPath)) {
            error_log('SIGA: image upload source is not a valid HTTP upload.');
            return false;
        }

        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            error_log('SIGA: could not create the image upload directory.');
            return false;
        }

        if (!is_writable($directory)) {
            @chmod($directory, 0755);
            clearstatcache(true, $directory);
            if (!is_writable($directory)) {
                error_log('SIGA: image upload directory is not writable by PHP.');
                return false;
            }
        }

        if (@move_uploaded_file($temporaryPath, $destination)) {
            return true;
        }

        // A copy fallback handles hosts where the temporary upload and target
        // directory are on different filesystems.
        if (@copy($temporaryPath, $destination)) {
            @unlink($temporaryPath);
            return true;
        }

        error_log('SIGA: PHP could not move or copy the uploaded image to its destination.');
        return false;
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
