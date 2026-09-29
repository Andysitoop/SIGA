<?php

namespace App\Helpers;

use PDO;
use PDOException;

/**
 * Helper para conexión a base de datos
 */
class Database
{
    private static ?PDO $instance = null;
    
    /**
     * Obtiene la instancia de conexión PDO (Singleton)
     */
    public static function getConnection(): PDO
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
                die('Error de conexión a la base de datos: ' . $e->getMessage());
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
