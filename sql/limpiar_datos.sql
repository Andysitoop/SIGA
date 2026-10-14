-- Script para eliminar todos los datos de las tablas
-- Manteniendo la estructura de la base de datos

USE sistema_academico;

-- Desactivar restricciones de foreign key
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar datos en orden correcto (hijos primero, padres después)
DELETE FROM notas;
DELETE FROM alumnos;
DELETE FROM secciones;
DELETE FROM cursos;
DELETE FROM semestres;
DELETE FROM carreras;

-- Reactivar restricciones de foreign key
SET FOREIGN_KEY_CHECKS = 1;

-- Reiniciar los auto_increment
ALTER TABLE alumnos AUTO_INCREMENT = 1;
ALTER TABLE carreras AUTO_INCREMENT = 1;
ALTER TABLE cursos AUTO_INCREMENT = 1;
ALTER TABLE semestres AUTO_INCREMENT = 1;
ALTER TABLE secciones AUTO_INCREMENT = 1;
ALTER TABLE notas AUTO_INCREMENT = 1;

SELECT 'Datos eliminados correctamente. La base de datos está vacía.' AS mensaje;
