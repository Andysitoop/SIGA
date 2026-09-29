<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla cursos
 */
class Curso
{
    /**
     * Obtiene todos los cursos
     */
    public static function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM cursos";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY codigo ASC";
        
        return Database::query($sql);
    }
    
    /**
     * Obtiene un curso por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT * FROM cursos WHERE id_curso = ?";
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Obtiene un curso por su código
     */
    public static function getByCode(string $codigo): ?array
    {
        $sql = "SELECT * FROM cursos WHERE codigo = ?";
        return Database::queryOne($sql, [$codigo]);
    }
    
    /**
     * Crea un nuevo curso
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO cursos (codigo, nombre, activo) VALUES (?, ?, ?)";
        return Database::insert($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza un curso
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE cursos SET codigo = ?, nombre = ?, activo = ? WHERE id_curso = ?";
        return Database::execute($sql, [
            $data['codigo'],
            $data['nombre'],
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Desactiva un curso (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE cursos SET activo = 0 WHERE id_curso = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Activa un curso
     */
    public static function activate(int $id): bool
    {
        $sql = "UPDATE cursos SET activo = 1 WHERE id_curso = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Verifica si el código ya existe
     */
    public static function codeExists(string $codigo, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM cursos WHERE codigo = ?";
        $params = [$codigo];
        
        if ($excludeId !== null) {
            $sql .= " AND id_curso != ?";
            $params[] = $excludeId;
        }
        
        $result = Database::queryOne($sql, $params);
        return (int)($result['total'] ?? 0) > 0;
    }
    
    /**
     * Cuenta el total de cursos
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM cursos WHERE activo = 1";
        $result = Database::queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
}
