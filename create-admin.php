<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Usuario;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

if (Usuario::countActiveAdmins() > 0) {
    fwrite(STDERR, "Ya existe un administrador activo.\n");
    exit(1);
}

fwrite(STDOUT, 'Nombre del administrador: ');
$name = trim((string)fgets(STDIN));
fwrite(STDOUT, 'Correo de acceso: ');
$email = strtolower(trim((string)fgets(STDIN)));

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Nombre o correo no válido.\n");
    exit(1);
}

fwrite(STDOUT, 'Contraseña (mínimo 12 caracteres): ');
if (PHP_OS_FAMILY !== 'Windows') {
    system('stty -echo');
}
$password = trim((string)fgets(STDIN));
if (PHP_OS_FAMILY !== 'Windows') {
    system('stty echo');
    fwrite(STDOUT, "\n");
}

if (mb_strlen($password, 'UTF-8') < 12) {
    fwrite(STDERR, "La contraseña debe tener al menos 12 caracteres.\n");
    exit(1);
}

if (Usuario::emailExists($email)) {
    fwrite(STDERR, "Ese correo ya está registrado.\n");
    exit(1);
}

try {
    Usuario::create($name, $email, password_hash($password, PASSWORD_DEFAULT), 'admin');
    fwrite(STDOUT, "Administrador creado. Ya puedes iniciar sesión.\n");
} catch (Throwable $error) {
    fwrite(STDERR, "No se pudo crear el administrador: {$error->getMessage()}\n");
    exit(1);
}