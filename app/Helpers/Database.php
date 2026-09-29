<?php

namespace App\Helpers;

use PDO;
use PDOException;

/**
 * Helper para conexión a base de datos
 */
class Database
{
    private static $instance = null;
    
    /**
     * Obtiene la instancia de conexión PDO (Singleton)
     */
    public static function getConnection()
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            try {
                self::$instance = new PDO(
                    $config['dsn'],
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                // Modo demo: retornar mock PDO con datos de ejemplo
                self::$instance = new class {
                    private $demoData = [
                        'alumnos' => [
                            ['id_alumno' => 1, 'nombres' => 'Juan', 'apellidos' => 'Pérez', 'fecha_nacimiento' => '2000-05-15', 'fotografia' => null, 'id_carrera' => 1, 'nombre_carrera' => 'Ingeniería de Sistemas', 'activo' => 1, 'fecha_registro' => '2024-01-15'],
                            ['id_alumno' => 2, 'nombres' => 'María', 'apellidos' => 'García', 'fecha_nacimiento' => '2001-03-20', 'fotografia' => null, 'id_carrera' => 2, 'nombre_carrera' => 'Administración', 'activo' => 1, 'fecha_registro' => '2024-01-20'],
                            ['id_alumno' => 3, 'nombres' => 'Carlos', 'apellidos' => 'López', 'fecha_nacimiento' => '2000-08-10', 'fotografia' => null, 'id_carrera' => 1, 'nombre_carrera' => 'Ingeniería de Sistemas', 'activo' => 1, 'fecha_registro' => '2024-02-01'],
                        ],
                        'carreras' => [
                            ['id_carrera' => 1, 'nombre' => 'Ingeniería de Sistemas', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_carrera' => 2, 'nombre' => 'Administración de Empresas', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_carrera' => 3, 'nombre' => 'Derecho', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                        ],
                        'cursos' => [
                            ['id_curso' => 1, 'codigo' => 'MAT101', 'nombre' => 'Matemáticas I', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_curso' => 2, 'codigo' => 'FIS101', 'nombre' => 'Física I', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_curso' => 3, 'codigo' => 'PRO101', 'nombre' => 'Programación I', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                        ],
                        'semestres' => [
                            ['id_semestre' => 1, 'nombre' => 'Primer Semestre', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_semestre' => 2, 'nombre' => 'Segundo Semestre', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_semestre' => 3, 'nombre' => 'Tercer Semestre', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                        ],
                        'secciones' => [
                            ['id_seccion' => 1, 'nombre' => 'Sección A', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_seccion' => 2, 'nombre' => 'Sección B', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                            ['id_seccion' => 3, 'nombre' => 'Sección C', 'activo' => 1, 'fecha_registro' => '2024-01-01'],
                        ],
                        'notas' => [
                            ['id_nota' => 1, 'id_alumno' => 1, 'id_semestre' => 1, 'id_seccion' => 1, 'id_curso' => 1, 'nota' => 85, 'nombres' => 'Juan', 'apellidos' => 'Pérez', 'codigo_curso' => 'MAT101', 'nombre_curso' => 'Matemáticas I', 'nombre_semestre' => 'Primer Semestre', 'activo' => 1, 'fecha_registro' => '2024-02-15'],
                            ['id_nota' => 2, 'id_alumno' => 2, 'id_semestre' => 1, 'id_seccion' => 1, 'id_curso' => 1, 'nota' => 92, 'nombres' => 'María', 'apellidos' => 'García', 'codigo_curso' => 'MAT101', 'nombre_curso' => 'Matemáticas I', 'nombre_semestre' => 'Primer Semestre', 'activo' => 1, 'fecha_registro' => '2024-02-15'],
                            ['id_nota' => 3, 'id_alumno' => 3, 'id_semestre' => 1, 'id_seccion' => 2, 'id_curso' => 2, 'nota' => 78, 'nombres' => 'Carlos', 'apellidos' => 'López', 'codigo_curso' => 'FIS101', 'nombre_curso' => 'Física I', 'nombre_semestre' => 'Primer Semestre', 'activo' => 1, 'fecha_registro' => '2024-02-20'],
                        ],
                    ];

                    public function prepare($sql) {
                        return new class($this->demoData, $sql) {
                            private $demoData;
                            private $sql;

                            public function __construct($demoData, $sql) {
                                $this->demoData = $demoData;
                                $this->sql = $sql;
                            }

                            public function execute($params = []) {
                                return true;
                            }

                            public function fetchAll() {
                                if (preg_match('/\bFROM\s+([a-z_]+)/i', $this->sql, $match)) {
                                    $table = strtolower($match[1]);
                                    return $this->demoData[$table] ?? [];
                                }
                                return [];
                            }

                            public function fetch() {
                                $results = $this->fetchAll();
                                return $results[0] ?? null;
                            }
                        };
                    }

                    public function beginTransaction() { return true; }
                    public function commit() { return true; }
                    public function rollBack() { return true; }
                    public function lastInsertId() { return 1; }
                };
            }
        }

        return self::$instance;
    }
    
    /**
     * Ejecuta una consulta SELECT con parámetros
     */
    public static function query(string $sql, array $params = []): array
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Ejecuta una consulta SELECT y retorna una sola fila
     */
    public static function queryOne(string $sql, array $params = []): ?array
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE
     */
    public static function execute(string $sql, array $params = []): bool
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Ejecuta una consulta INSERT y retorna el último ID insertado
     */
    public static function insert(string $sql, array $params = []): int
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$pdo->lastInsertId();
    }
    
    /**
     * Inicia una transacción
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }
    
    /**
     * Confirma una transacción
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }
    
    /**
     * Revierte una transacción
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollBack();
    }
}
