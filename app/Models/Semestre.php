<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla semestres
 */
class Semestre
{
    /**
     * Obtiene todos los semestres
     */
    public static function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM semestres";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        return Database::query($sql);
    }
    
    /**
     * Obtiene un semestre por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT * FROM semestres WHERE id_semestre = ?";
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo semestre
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO semestres (nombre, activo) VALUES (?, ?)";
        return Database::insert($sql, [
            $data['nombre'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza un semestre
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE semestres SET nombre = ?, activo = ? WHERE id_semestre = ?";
        return Database::execute($sql, [
            $data['nombre'],
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Desactiva un semestre (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE semestres SET activo = 0 WHERE id_semestre = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Activa un semestre
     */
    public static function activate(int $id): bool
    {
        $sql = "UPDATE semestres SET activo = 1 WHERE id_semestre = ?";
        return Database::execute($sql, [$id]);
    }
}
