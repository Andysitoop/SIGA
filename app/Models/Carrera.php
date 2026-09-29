<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla carreras
 */
class Carrera
{
    /**
     * Obtiene todas las carreras activas
     */
    public static function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM carreras";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        return Database::query($sql);
    }
    
    /**
     * Obtiene una carrera por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT * FROM carreras WHERE id_carrera = ?";
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Crea una nueva carrera
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO carreras (nombre, activo) VALUES (?, ?)";
        return Database::insert($sql, [
            $data['nombre'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza una carrera
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE carreras SET nombre = ?, activo = ? WHERE id_carrera = ?";
        return Database::execute($sql, [
            $data['nombre'],
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Desactiva una carrera (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE carreras SET activo = 0 WHERE id_carrera = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Activa una carrera
     */
    public static function activate(int $id): bool
    {
        $sql = "UPDATE carreras SET activo = 1 WHERE id_carrera = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Cuenta el total de carreras
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM carreras WHERE activo = 1";
        $result = Database::queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
}
