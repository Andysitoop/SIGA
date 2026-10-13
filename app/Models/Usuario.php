<?php

namespace App\Models;

use App\Helpers\Database;

class Usuario
{
    public static function getById(int $id): ?array
    {
        return Database::queryOne(
            "SELECT id_usuario, rol, activo FROM usuarios WHERE id_usuario = ? LIMIT 1",
            [$id]
        );
    }

    public static function getByEmail(string $email): ?array
    {
        return Database::queryOne(
            "SELECT * FROM usuarios WHERE correo = ? LIMIT 1",
            [strtolower(trim($email))]
        );
    }

    public static function getAll(): array
    {
        return Database::query(
            "SELECT id_usuario, nombre, correo, rol, activo, fecha_registro
             FROM usuarios ORDER BY nombre ASC, correo ASC"
        );
    }

    public static function emailExists(string $email): bool
    {
        $result = Database::queryOne(
            "SELECT COUNT(*) AS total FROM usuarios WHERE correo = ?",
            [strtolower(trim($email))]
        );

        return (int)($result['total'] ?? 0) > 0;
    }

    public static function create(string $name, string $email, string $passwordHash, string $role = 'user'): int
    {
        return Database::insert(
            "INSERT INTO usuarios (nombre, correo, password_hash, rol, activo)
             VALUES (?, ?, ?, ?, 1)",
            [trim($name), strtolower(trim($email)), $passwordHash, $role]
        );
    }

    public static function setActive(int $id, bool $active): bool
    {
        return Database::execute(
            "UPDATE usuarios SET activo = ? WHERE id_usuario = ?",
            [$active ? 1 : 0, $id]
        );
    }

    public static function countActiveAdmins(): int
    {
        $result = Database::queryOne(
            "SELECT COUNT(*) AS total FROM usuarios WHERE rol = 'admin' AND activo = 1"
        );

        return (int)($result['total'] ?? 0);
    }
}