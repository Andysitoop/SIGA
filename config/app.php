<?php

/**
 * Configuración general de la aplicación
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

return [
    'name' => $_ENV['APP_NAME'] ?? 'SIGA',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    
    // Configuración de notas
    'nota_minima_aprobacion' => (int)($_ENV['NOTA_MINIMA_APROBACION'] ?? 60),
    'nota_maxima' => (int)($_ENV['NOTA_MAXIMA'] ?? 100),
    
    // Configuración de archivos
    'max_file_size' => (int)($_ENV['MAX_FILE_SIZE'] ?? 2097152), // 2MB
    'allowed_image_types' => explode(',', $_ENV['ALLOWED_IMAGE_TYPES'] ?? 'image/jpeg,image/jpg,image/png'),
    'upload_path' => __DIR__ . '/..//public/uploads/',
    'upload_url' => ($_ENV['APP_URL'] ?? 'http://localhost:8000') . '/uploads/',
    
    // Configuración de paginación
    'pagination_limit' => (int)($_ENV['PAGINATION_LIMIT'] ?? 20),
];
