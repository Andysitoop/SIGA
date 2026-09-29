<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla alumnos
 */
class Alumno
{
    /**
     * Obtiene todos los alumnos con paginación
     */
    public static function getAll(int $page = 1, int $limit = 20, bool $onlyActive = true): array
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT a.*, c.nombre as nombre_carrera 
                FROM alumnos a 
                INNER JOIN carreras c ON a.id_carrera = c.id_carrera";
        
        if ($onlyActive) {
            $sql .= " WHERE a.activo = 1";
        }
        
        $sql .= " ORDER BY a.apellidos ASC, a.nombres ASC 
                 LIMIT ? OFFSET ?";
        
        return Database::query($sql, [$limit, $offset]);
    }
    
    /**
     * Obtiene un alumno por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT a.*, c.nombre as nombre_carrera 
                FROM alumnos a 
                INNER JOIN carreras c ON a.id_carrera = c.id_carrera 
                WHERE a.id_alumno = ?";
        
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Busca alumnos por nombre o apellido
     */
    public static function search(string $query, int $limit = 20): array
    {
        $sql = "SELECT a.*, c.nombre as nombre_carrera 
                FROM alumnos a 
                INNER JOIN carreras c ON a.id_carrera = c.id_carrera 
                WHERE a.activo = 1 
                AND (a.nombres LIKE ? OR a.apellidos LIKE ? OR CONCAT(a.nombres, ' ', a.apellidos) LIKE ?)
                ORDER BY a.apellidos ASC, a.nombres ASC 
                LIMIT ?";
        
        $searchTerm = "%$query%";
        return Database::query($sql, [$searchTerm, $searchTerm, $searchTerm, $limit]);
    }
    
    /**
     * Crea un nuevo alumno
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO alumnos (nombres, apellidos, fecha_nacimiento, fotografia, id_carrera, activo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return Database::insert($sql, [
            $data['nombres'],
            $data['apellidos'],
            $data['fecha_nacimiento'],
            $data['fotografia'] ?? null,
            $data['id_carrera'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza un alumno
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE alumnos SET nombres = ?, apellidos = ?, fecha_nacimiento = ?, id_carrera = ?";
        $params = [
            $data['nombres'],
            $data['apellidos'],
            $data['fecha_nacimiento'],
            $data['id_carrera']
        ];
        
        if (isset($data['fotografia'])) {
            $sql .= ", fotografia = ?";
            $params[] = $data['fotografia'];
        }
        
        $sql .= " WHERE id_alumno = ?";
        $params[] = $id;
        
        return Database::execute($sql, $params);
    }
    
    /**
     * Desactiva un alumno (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE alumnos SET activo = 0 WHERE id_alumno = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Activa un alumno
     */
    public static function activate(int $id): bool
    {
        $sql = "UPDATE alumnos SET activo = 1 WHERE id_alumno = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Cuenta el total de alumnos
     */
    public static function count(bool $onlyActive = true): int
    {
        $sql = "SELECT COUNT(*) as total FROM alumnos";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $result = Database::queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Obtiene los últimos alumnos registrados
     */
    public static function getLatest(int $limit = 5): array
    {
        $sql = "SELECT a.*, c.nombre as nombre_carrera 
                FROM alumnos a 
                INNER JOIN carreras c ON a.id_carrera = c.id_carrera 
                WHERE a.activo = 1 
                ORDER BY a.fecha_registro DESC 
                LIMIT ?";
        
        return Database::query($sql, [$limit]);
    }
    
    /**
     * Obtiene alumnos por carrera
     */
    public static function getByCareer(int $idCarrera): array
    {
        $sql = "SELECT a.*, c.nombre as nombre_carrera 
                FROM alumnos a 
                INNER JOIN carreras c ON a.id_carrera = c.id_carrera 
                WHERE a.id_carrera = ? AND a.activo = 1 
                ORDER BY a.apellidos ASC, a.nombres ASC";
        
        return Database::query($sql, [$idCarrera]);
    }
}
