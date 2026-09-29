<?php

namespace App\Models;

use App\Helpers\Database;

/**
 * Modelo para la tabla notas
 */
class Nota
{
    /**
     * Obtiene todas las notas de un alumno
     */
    public static function getByAlumno(int $idAlumno, bool $onlyActive = true): array
    {
        $sql = "SELECT n.*, 
                       s.nombre as nombre_semestre,
                       sec.nombre as nombre_seccion,
                       c.codigo as codigo_curso,
                       c.nombre as nombre_curso
                FROM notas n
                INNER JOIN semestres s ON n.id_semestre = s.id_semestre
                INNER JOIN secciones sec ON n.id_seccion = sec.id_seccion
                INNER JOIN cursos c ON n.id_curso = c.id_curso
                WHERE n.id_alumno = ?";
        
        if ($onlyActive) {
            $sql .= " AND n.activo = 1";
        }
        
        $sql .= " ORDER BY s.nombre ASC, c.codigo ASC";
        
        return Database::query($sql, [$idAlumno]);
    }
    
    /**
     * Obtiene una nota por su ID
     */
    public static function getById(int $id): ?array
    {
        $sql = "SELECT n.*, 
                       a.nombres, a.apellidos,
                       s.nombre as nombre_semestre,
                       sec.nombre as nombre_seccion,
                       c.codigo as codigo_curso,
                       c.nombre as nombre_curso
                FROM notas n
                INNER JOIN alumnos a ON n.id_alumno = a.id_alumno
                INNER JOIN semestres s ON n.id_semestre = s.id_semestre
                INNER JOIN secciones sec ON n.id_seccion = sec.id_seccion
                INNER JOIN cursos c ON n.id_curso = c.id_curso
                WHERE n.id_nota = ?";
        
        return Database::queryOne($sql, [$id]);
    }
    
    /**
     * Verifica si ya existe una nota para el mismo alumno, curso y semestre
     */
    public static function exists(int $idAlumno, int $idCurso, int $idSemestre, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as total FROM notas 
                WHERE id_alumno = ? AND id_curso = ? AND id_semestre = ? AND activo = 1";
        $params = [$idAlumno, $idCurso, $idSemestre];
        
        if ($excludeId !== null) {
            $sql .= " AND id_nota != ?";
            $params[] = $excludeId;
        }
        
        $result = Database::queryOne($sql, $params);
        return (int)($result['total'] ?? 0) > 0;
    }
    
    /**
     * Crea una nueva nota
     */
    public static function create(array $data): int
    {
        $sql = "INSERT INTO notas (id_alumno, id_semestre, id_seccion, id_curso, nota, activo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return Database::insert($sql, [
            $data['id_alumno'],
            $data['id_semestre'],
            $data['id_seccion'],
            $data['id_curso'],
            $data['nota'],
            $data['activo'] ?? 1
        ]);
    }
    
    /**
     * Actualiza una nota
     */
    public static function update(int $id, array $data): bool
    {
        $sql = "UPDATE notas SET id_alumno = ?, id_semestre = ?, id_seccion = ?, id_curso = ?, nota = ?, activo = ? 
                WHERE id_nota = ?";
        
        return Database::execute($sql, [
            $data['id_alumno'],
            $data['id_semestre'],
            $data['id_seccion'],
            $data['id_curso'],
            $data['nota'],
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Desactiva una nota (soft delete)
     */
    public static function deactivate(int $id): bool
    {
        $sql = "UPDATE notas SET activo = 0 WHERE id_nota = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Cuenta el total de notas
     */
    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as total FROM notas WHERE activo = 1";
        $result = Database::queryOne($sql);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Obtiene las últimas notas registradas
     */
    public static function getLatest(int $limit = 5): array
    {
        $sql = "SELECT n.*, 
                       a.nombres, a.apellidos,
                       c.nombre as nombre_curso,
                       s.nombre as nombre_semestre
                FROM notas n
                INNER JOIN alumnos a ON n.id_alumno = a.id_alumno
                INNER JOIN cursos c ON n.id_curso = c.id_curso
                INNER JOIN semestres s ON n.id_semestre = s.id_semestre
                WHERE n.activo = 1 
                ORDER BY n.fecha_registro DESC 
                LIMIT ?";
        
        return Database::query($sql, [$limit]);
    }
    
    /**
     * Obtiene notas con filtros para reportes
     */
    public static function getWithFilters(?int $idCarrera = null, ?int $idCurso = null, ?int $idSeccion = null, ?int $idSemestre = null): array
    {
        $sql = "SELECT n.*, 
                       a.nombres, a.apellidos, a.id_carrera,
                       c.nombre as nombre_curso,
                       s.nombre as nombre_semestre,
                       sec.nombre as nombre_seccion,
                       car.nombre as nombre_carrera
                FROM notas n
                INNER JOIN alumnos a ON n.id_alumno = a.id_alumno
                INNER JOIN cursos c ON n.id_curso = c.id_curso
                INNER JOIN semestres s ON n.id_semestre = s.id_semestre
                INNER JOIN secciones sec ON n.id_seccion = sec.id_seccion
                INNER JOIN carreras car ON a.id_carrera = car.id_carrera
                WHERE n.activo = 1 AND a.activo = 1";
        
        $params = [];
        
        if ($idCarrera !== null) {
            $sql .= " AND a.id_carrera = ?";
            $params[] = $idCarrera;
        }
        
        if ($idCurso !== null) {
            $sql .= " AND n.id_curso = ?";
            $params[] = $idCurso;
        }
        
        if ($idSeccion !== null) {
            $sql .= " AND n.id_seccion = ?";
            $params[] = $idSeccion;
        }
        
        if ($idSemestre !== null) {
            $sql .= " AND n.id_semestre = ?";
            $params[] = $idSemestre;
        }
        
        $sql .= " ORDER BY car.nombre ASC, a.apellidos ASC, a.nombres ASC, s.nombre ASC, c.codigo ASC";
        
        return Database::query($sql, $params);
    }
    
    /**
     * Calcula estadísticas de un alumno
     */
    public static function getStudentStats(int $idAlumno, int $notaMinima): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_cursos,
                    SUM(CASE WHEN nota >= ? THEN 1 ELSE 0 END) as cursos_aprobados,
                    SUM(CASE WHEN nota < ? THEN 1 ELSE 0 END) as cursos_reprobados,
                    AVG(nota) as promedio_general
                FROM notas 
                WHERE id_alumno = ? AND activo = 1";
        
        $result = Database::queryOne($sql, [$notaMinima, $notaMinima, $idAlumno]);
        
        return [
            'total_cursos' => (int)($result['total_cursos'] ?? 0),
            'cursos_aprobados' => (int)($result['cursos_aprobados'] ?? 0),
            'cursos_reprobados' => (int)($result['cursos_reprobados'] ?? 0),
            'promedio_general' => round((float)($result['promedio_general'] ?? 0), 2)
        ];
    }
}
