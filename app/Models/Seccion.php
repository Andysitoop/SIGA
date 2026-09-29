<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla secciones
 */
class Seccion
{
    /**
     * Obtiene todas las secciones
     */
    public static function getAll(bool $onlyActive = true): array
    {
        $sql = "SELECT * FROM secciones";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY nombre ASC";
        
        return Database::query($sql);
    }
    
    /**
     * Obtiene una sección por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT * FROM secciones WHERE id_seccion = ?";
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Crea una nueva sección
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO secciones (nombre, activo) VALUES (?, ?)";
        return Database::insert($sql, [
            $data['nombre'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza una sección
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE secciones SET nombre = ?, activo = ? WHERE id_seccion = ?";
        return Database::execute($sql, [
            $data['nombre'],
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Desactiva una sección (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE secciones SET activo = 0 WHERE id_seccion = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Activa una sección
     */
    public static function activate(int $id): bool
    {
        $sql = "UPDATE secciones SET activo = 1 WHERE id_seccion = ?";
        return Database::execute($sql, [$id]);
    }
}
