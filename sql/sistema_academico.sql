-- Sistema Académico - Base de Datos
-- MySQL 8.0+

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_academico 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE sistema_academico;

-- Tabla: carreras
CREATE TABLE carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: cursos
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_codigo (codigo),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: semestres
CREATE TABLE semestres (
    id_semestre INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: secciones
CREATE TABLE secciones (
    id_seccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: alumnos
CREATE TABLE alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    fotografia VARCHAR(255),
    id_carrera INT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_nombres (nombres),
    INDEX idx_apellidos (apellidos),
    INDEX idx_id_carrera (id_carrera),
    INDEX idx_activo (activo),
    CONSTRAINT fk_alumnos_carrera FOREIGN KEY (id_carrera) 
        REFERENCES carreras(id_carrera) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: notas
CREATE TABLE notas (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_semestre INT NOT NULL,
    id_seccion INT NOT NULL,
    id_curso INT NOT NULL,
    nota DECIMAL(5,2) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1,
    INDEX idx_id_alumno (id_alumno),
    INDEX idx_id_curso (id_curso),
    INDEX idx_id_semestre (id_semestre),
    INDEX idx_id_seccion (id_seccion),
    INDEX idx_activo (activo),
    CONSTRAINT fk_notas_alumno FOREIGN KEY (id_alumno) 
        REFERENCES alumnos(id_alumno) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_semestre FOREIGN KEY (id_semestre) 
        REFERENCES semestres(id_semestre) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_seccion FOREIGN KEY (id_seccion) 
        REFERENCES secciones(id_seccion) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_curso FOREIGN KEY (id_curso) 
        REFERENCES cursos(id_curso) ON DELETE RESTRICT,
    CONSTRAINT uk_notas_alumno_curso_semestre UNIQUE (id_alumno, id_curso, id_semestre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos iniciales de prueba

-- Carreras
INSERT INTO carreras (nombre, activo) VALUES
('Bachillerato en Ciencias y Letras', 1),
('Bachillerato en Ciencias y Letras con Orientación en Computación', 1),
('Bachillerato en Ciencias y Letras con Orientación en Biología', 1),
('Bachillerato en Ciencias y Letras con Orientación en Química', 1),
('Bachillerato en Ciencias y Letras con Orientación en Física', 1);

-- Cursos
INSERT INTO cursos (codigo, nombre, activo) VALUES
('MAT101', 'Matemáticas I', 1),
('MAT102', 'Matemáticas II', 1),
('MAT103', 'Matemáticas III', 1),
('QUI101', 'Química I', 1),
('QUI102', 'Química II', 1),
('FIS101', 'Física I', 1),
('FIS102', 'Física II', 1),
('BIO101', 'Biología I', 1),
('BIO102', 'Biología II', 1),
('LEN101', 'Lenguaje y Literatura', 1),
('LEN102', 'Lengua Extranjera Inglés I', 1),
('LEN103', 'Lengua Extranjera Inglés II', 1),
('SOC101', 'Estudios Sociales', 1),
('HIS101', 'Historia de Guatemala', 1),
('CIV101', 'Educación Cívica', 1),
('INF101', 'Informática I', 1),
('INF102', 'Informática II', 1),
('ART101', 'Educación Artística', 1),
('EDF101', 'Educación Física', 1),
('FIL101', 'Filosofía', 1);

-- Semestres
INSERT INTO semestres (nombre, activo) VALUES
('Primer Semestre', 1),
('Segundo Semestre', 1),
('Tercer Semestre', 1),
('Cuarto Semestre', 1),
('Quinto Semestre', 1),
('Sexto Semestre', 1);

-- Secciones
INSERT INTO secciones (nombre, activo) VALUES
('Sección A', 1),
('Sección B', 1),
('Sección C', 1),
('Sección D', 1),
('Sección E', 1);

-- Alumnos de prueba
INSERT INTO alumnos (nombres, apellidos, fecha_nacimiento, fotografia, id_carrera, activo) VALUES
('Juan Carlos', 'Pérez García', '2005-03-15', NULL, 1, 1),
('María Isabel', 'Rodríguez López', '2005-07-22', NULL, 2, 1),
('Carlos Eduardo', 'González Martínez', '2005-11-30', NULL, 3, 1),
('Ana Sofía', 'Hernández Sánchez', '2006-01-10', NULL, 1, 1),
('Luis Fernando', 'Ramírez Castillo', '2005-05-18', NULL, 4, 1);

-- Notas de prueba
INSERT INTO notas (id_alumno, id_semestre, id_seccion, id_curso, nota, activo) VALUES
(1, 1, 1, 1, 85.50, 1),
(1, 1, 1, 10, 78.00, 1),
(1, 1, 1, 12, 92.00, 1),
(2, 1, 1, 1, 90.00, 1),
(2, 1, 1, 16, 88.50, 1),
(2, 1, 1, 10, 95.00, 1),
(3, 1, 2, 7, 82.00, 1),
(3, 1, 2, 8, 76.50, 1),
(3, 1, 2, 1, 89.00, 1),
(4, 1, 1, 1, 91.00, 1),
(4, 1, 1, 10, 87.00, 1),
(4, 1, 1, 13, 94.00, 1),
(5, 1, 3, 5, 79.00, 1),
(5, 1, 3, 6, 83.50, 1),
(5, 1, 3, 1, 86.00, 1);

-- Notas adicionales para segundo semestre
INSERT INTO notas (id_alumno, id_semestre, id_seccion, id_curso, nota, activo) VALUES
(1, 2, 1, 2, 88.00, 1),
(1, 2, 1, 5, 75.50, 1),
(2, 2, 1, 2, 92.00, 1),
(2, 2, 1, 17, 90.00, 1),
(3, 2, 2, 2, 85.00, 1),
(3, 2, 2, 9, 80.00, 1);
