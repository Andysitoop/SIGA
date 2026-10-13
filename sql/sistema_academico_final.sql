
-- No cambié nombres de tablas ni de columnas respecto al script original,
-- por eso el código del proyecto no necesita ningún ajuste.
-- ---------------------------------------------------------------------

-- Los archivos están en UTF-8. Sin esto, desde la terminal de Windows las
-- tildes y la ñ se guardan mal ("SecciÃ³n").
SET NAMES utf8mb4;


-- ---------------------------------------------------------------------
-- 1. Base de datos
-- ---------------------------------------------------------------------
DROP DATABASE IF EXISTS sistema_academico;

CREATE DATABASE sistema_academico
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_academico;


-- ---------------------------------------------------------------------
-- 2. Tablas
-- ---------------------------------------------------------------------
-- Cosas que cambié respecto al script original, para que sepan dónde
-- mirar si algo no cuadra:
--
--  * activo y fecha_registro ahora son NOT NULL. Antes aceptaban NULL y
--    un registro con activo en NULL desaparecía de todos los listados
--    que filtran WHERE activo = 1, sin mostrar ningún error.
--
--  * activo solo acepta 0 o 1.
--
--  * Los nombres de catálogo no se pueden repetir, pero solo entre
--    registros activos (ver la nota de abajo, es lo más raro del script).
--
--  * La nota tiene CHECK entre 0 y 100. La app ya valida eso, pero la
--    base no debería depender de que el código siempre lo haga bien.
--
--  * Quité los índices sobre "activo". Solo tiene dos valores y MySQL
--    casi nunca los usa, lo único que hacen es frenar los INSERT.
--
-- Nota sobre los UNIQUE "solo entre activos":
--   El sistema no borra, desactiva (activo = 0), parecido al campo active
--   de Odoo. Con un UNIQUE normal sobre el nombre, si desactivas "Sección
--   A" y después creas otra "Sección A", MySQL tira Duplicate entry porque
--   la fila vieja sigue ahí. Lo mismo pasaba con las notas.
--   Para evitarlo uso un índice funcional: UNIQUE (nombre, IF(activo=1,1,NULL)).
--   Una fila activa tiene la clave (nombre, 1) y no se puede repetir.
CREATE TABLE carreras (
    id_carrera INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_carreras_nombre_activo (nombre, (IF(activo = 1, 1, NULL))),
    CONSTRAINT chk_carreras_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_carreras_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- En cursos el código sigue siendo UNIQUE normal, igual que en el
-- original. Un código identifica al curso para siempre: si se desactivó,
-- lo correcto es reactivarlo (/cursos/{id}/activar) y no crear otro igual.
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cursos_nombre_activo (nombre, (IF(activo = 1, 1, NULL))),
    CONSTRAINT chk_cursos_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_cursos_texto CHECK (CHAR_LENGTH(TRIM(nombre)) > 0 AND CHAR_LENGTH(TRIM(codigo)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE semestres (
    id_semestre INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_semestres_nombre_activo (nombre, (IF(activo = 1, 1, NULL))),
    CONSTRAINT chk_semestres_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_semestres_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE secciones (
    id_seccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_secciones_nombre_activo (nombre, (IF(activo = 1, 1, NULL))),
    CONSTRAINT chk_secciones_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_secciones_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    correo VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_correo (correo),
    CONSTRAINT chk_usuarios_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_usuarios_nombre CHECK (CHAR_LENGTH(TRIM(nombre)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices de alumnos:
--   idx_nombres / idx_apellidos          -> buscador
--   idx_alumnos_carrera_apellidos        -> reporte de alumnos por carrera,
--                                           ya ordenado por apellido. También
--                                           lo usa la llave foránea de carrera.
-- Para el buscador usar LIKE 'texto%' (sin % al inicio). Con '%texto%'
-- MySQL no puede usar el índice y recorre toda la tabla.
CREATE TABLE alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    fotografia VARCHAR(255),
    id_carrera INT NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_nombres (nombres),
    INDEX idx_apellidos (apellidos),
    INDEX idx_alumnos_carrera_apellidos (id_carrera, apellidos, nombres),
    CONSTRAINT fk_alumnos_carrera FOREIGN KEY (id_carrera)
        REFERENCES carreras(id_carrera) ON DELETE RESTRICT,
    CONSTRAINT chk_alumnos_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_alumnos_nombres CHECK (CHAR_LENGTH(TRIM(nombres)) > 0 AND CHAR_LENGTH(TRIM(apellidos)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices de notas:
--   idx_id_alumno       -> lo necesita la llave foránea de alumno (los
--                          índices funcionales no sirven para una FK, por
--                          eso no lo cubre el UNIQUE de abajo).
--   idx_notas_reporte   -> reporte de notas por curso, sección y semestre.
--                          También le sirve a la FK de curso.
--   uk_notas_..._activa -> no repetir el mismo curso para el mismo alumno
--                          y semestre, solo entre notas activas.
-- Si el grupo decide guardar únicamente cursos aprobados (el enunciado
-- habla de "cursos aprobados"), cambiar el CHECK por BETWEEN 60 AND 100.
-- El 60 es el NOTA_MINIMA_APROBACION del .env.
CREATE TABLE notas (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_semestre INT NOT NULL,
    id_seccion INT NOT NULL,
    id_curso INT NOT NULL,
    nota DECIMAL(5,2) NOT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_id_alumno (id_alumno),
    INDEX idx_id_semestre (id_semestre),
    INDEX idx_id_seccion (id_seccion),
    INDEX idx_notas_reporte (id_curso, id_seccion, id_semestre),
    CONSTRAINT fk_notas_alumno FOREIGN KEY (id_alumno)
        REFERENCES alumnos(id_alumno) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_semestre FOREIGN KEY (id_semestre)
        REFERENCES semestres(id_semestre) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_seccion FOREIGN KEY (id_seccion)
        REFERENCES secciones(id_seccion) ON DELETE RESTRICT,
    CONSTRAINT fk_notas_curso FOREIGN KEY (id_curso)
        REFERENCES cursos(id_curso) ON DELETE RESTRICT,
    UNIQUE KEY uk_notas_alumno_curso_semestre_activa (id_alumno, id_curso, id_semestre, (IF(activo = 1, 1, NULL))),
    CONSTRAINT chk_notas_activo CHECK (activo IN (0,1)),
    CONSTRAINT chk_notas_rango CHECK (nota BETWEEN 0 AND 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 3. Catálogos
-- ---------------------------------------------------------------------
INSERT INTO carreras (nombre, activo) VALUES
('Bachillerato en Ciencias y Letras', 1),
('Bachillerato en Ciencias y Letras con Orientación en Computación', 1),
('Bachillerato en Ciencias y Letras con Orientación en Biología', 1),
('Bachillerato en Ciencias y Letras con Orientación en Química', 1),
('Bachillerato en Ciencias y Letras con Orientación en Física', 1);

-- El orden importa: el pensum de la sección 5 usa estos id (1 = MAT101 ...
-- 20 = FIL101). Si se agrega un curso, ponerlo al final.
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

-- Ojo al ordenar semestres en la app: hay que hacerlo por id_semestre.
-- Por nombre salen alfabéticos y "Cuarto" queda antes que "Primero".
INSERT INTO semestres (nombre, activo) VALUES
('Primer Semestre', 1),
('Segundo Semestre', 1),
('Tercer Semestre', 1),
('Cuarto Semestre', 1),
('Quinto Semestre', 1),
('Sexto Semestre', 1);

INSERT INTO secciones (nombre, activo) VALUES
('Sección A', 1),
('Sección B', 1),
('Sección C', 1),
('Sección D', 1),
('Sección E', 1);


-- ---------------------------------------------------------------------
-- 4. Alumnos y notas del seed original
-- ---------------------------------------------------------------------
-- Son los mismos 5 alumnos y 21 notas del script anterior. Solo corregí
-- cuatro notas que tenían cursos "II" sin haber llevado el "I":
--   alumno 1, sem 1: Inglés II -> Inglés I
--   alumno 3, sem 1: Física II -> Física I
--   alumno 5, sem 1: Química II -> Química I
--   alumno 1, sem 2: Química II -> Química I
INSERT INTO alumnos (nombres, apellidos, fecha_nacimiento, fotografia, id_carrera, activo) VALUES
('Juan Carlos', 'Pérez García', '2005-03-15', NULL, 1, 1),
('María Isabel', 'Rodríguez López', '2005-07-22', NULL, 2, 1),
('Carlos Eduardo', 'González Martínez', '2005-11-30', NULL, 3, 1),
('Ana Sofía', 'Hernández Sánchez', '2006-01-10', NULL, 1, 1),
('Luis Fernando', 'Ramírez Castillo', '2005-05-18', NULL, 4, 1);

-- (alumno, semestre, sección, curso, nota)
INSERT INTO notas (id_alumno, id_semestre, id_seccion, id_curso, nota, activo) VALUES
(1, 1, 1, 1, 85.50, 1),
(1, 1, 1, 10, 78.00, 1),
(1, 1, 1, 11, 92.00, 1),
(2, 1, 1, 1, 90.00, 1),
(2, 1, 1, 16, 88.50, 1),
(2, 1, 1, 10, 95.00, 1),
(3, 1, 2, 6, 82.00, 1),
(3, 1, 2, 8, 76.50, 1),
(3, 1, 2, 1, 89.00, 1),
(4, 1, 1, 1, 91.00, 1),
(4, 1, 1, 10, 87.00, 1),
(4, 1, 1, 13, 94.00, 1),
(5, 1, 3, 4, 79.00, 1),
(5, 1, 3, 6, 83.50, 1),
(5, 1, 3, 1, 86.00, 1),
(1, 2, 1, 2, 88.00, 1),
(1, 2, 1, 4, 75.50, 1),
(2, 2, 1, 2, 92.00, 1),
(2, 2, 1, 17, 90.00, 1),
(3, 2, 2, 2, 85.00, 1),
(3, 2, 2, 9, 80.00, 1);


-- ---------------------------------------------------------------------
-- 5. Datos de demostración
-- ---------------------------------------------------------------------
-- Con 5 alumnos los reportes se veían vacíos (la carrera de Física ni
-- tenía alumnos y las secciones D y E no tenían notas). Agrego 26
-- alumnos más, con edades de 15 a 18 años como en un bachillerato.
--
-- Para no escribir 270 notas a mano uso dos tablas temporales que se
-- borran solas al cerrar la sesión:
--   tmp_demo_alumnos -> la lista de alumnos, con su carrera, sección y
--                       cuántos semestres lleva aprobados (1 a 3).
--   tmp_plan         -> qué cursos se llevan en cada semestre.
-- Si hay que agregar o quitar alumnos, se edita solo la primera lista.
--
-- Las fotos quedan en NULL, se suben desde la app durante la demo.
-- ---------------------------------------------------------------------
CREATE TEMPORARY TABLE tmp_demo_alumnos (
    nombres            VARCHAR(100) NOT NULL,
    apellidos          VARCHAR(100) NOT NULL,
    fecha_nacimiento   DATE NOT NULL,
    id_carrera         INT NOT NULL,
    id_seccion         INT NOT NULL,
    semestres_cursados TINYINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tmp_demo_alumnos VALUES
-- Ciencias y Letras
('Brandon Josué',      'Cumes Tzoc',        '2009-02-14', 1, 1, 3),
('Ashley Nicole',      'Méndez Roca',       '2008-11-03', 1, 1, 3),
('Josué David',        'Batz Ajú',          '2009-06-21', 1, 2, 2),
('Mariela Fernanda',   'López Xiloj',       '2009-09-08', 1, 2, 2),
('Kimberly Daniela',   'Ortiz Estrada',     '2010-01-17', 1, 3, 1),
('Wilson Estuardo',    'Ixcoy Pérez',       '2009-04-30', 1, 3, 1),
-- Computación
('Cristian Alexander', 'Morales Girón',     '2008-08-12', 2, 1, 3),
('Dulce María',        'Cifuentes Lemus',   '2009-03-05', 2, 1, 3),
('Erick Rolando',      'Tojín Gómez',       '2008-12-19', 2, 4, 2),
('Gabriela Alejandra', 'Barrios Sandoval',  '2009-07-27', 2, 4, 2),
('Pablo Andrés',       'Aguilar Ruiz',      '2010-02-09', 2, 5, 1),
('Lucía Fernanda',     'Coc Caal',          '2009-10-15', 2, 5, 1),
-- Biología
('Henry Alexis',       'Velásquez Juárez',  '2008-05-23', 3, 2, 3),
('Paola Andrea',       'Salazar Monzón',    '2009-01-31', 3, 2, 3),
('Jorge Luis',         'Chután Quej',       '2009-05-11', 3, 3, 2),
('Andrea Lucrecia',    'Reyes Gálvez',      '2009-12-02', 3, 3, 2),
('Elías Samuel',       'Tzul Xicay',        '2010-03-26', 3, 5, 1),
-- Química
('Fátima Alejandra',   'Pop Choc',          '2008-09-18', 4, 3, 3),
('Mynor Estuardo',     'Hernández Lux',     '2008-07-06', 4, 3, 3),
('Jennifer Lizeth',    'Castañeda Orozco',  '2009-08-24', 4, 4, 2),
('Oscar Giovanni',     'Raxón Ajpop',       '2009-11-13', 4, 4, 2),
('Rebeca Sofía',       'Linares Arriaza',   '2010-04-04', 4, 2, 1),
-- Física
('Daniel Esteban',     'Sicán Cumatz',      '2008-10-29', 5, 5, 3),
('Nathalie Michelle',  'Orellana Paz',      '2009-02-22', 5, 5, 3),
('Alan Josué',         'Mejía Calel',       '2009-06-09', 5, 1, 2),
('Brenda Yaneth',      'Ajanel Tuy',        '2009-09-30', 5, 1, 2);

INSERT INTO alumnos (nombres, apellidos, fecha_nacimiento, fotografia, id_carrera, activo)
SELECT nombres, apellidos, fecha_nacimiento, NULL, id_carrera, 1
FROM tmp_demo_alumnos;

-- Pensum por semestre, para que el historial tenga lógica (Matemáticas I
-- antes que II, Física I antes que II, etc.).
--   id_carrera NULL -> lo llevan todas las carreras (tronco común)
--   id_carrera = N  -> curso propio de esa orientación
-- Los id de curso son los de la sección 3.
CREATE TEMPORARY TABLE tmp_plan (
    id_carrera  INT NULL,
    id_semestre INT NOT NULL,
    id_curso    INT NOT NULL
) ENGINE=InnoDB;

INSERT INTO tmp_plan (id_carrera, id_semestre, id_curso) VALUES
-- Tronco común
(NULL, 1, 1), (NULL, 1, 10), (NULL, 1, 11), (NULL, 1, 13), (NULL, 1, 19),
(NULL, 2, 2), (NULL, 2, 12), (NULL, 2, 14), (NULL, 2, 15), (NULL, 2, 18),
(NULL, 3, 3), (NULL, 3, 20),
-- Ciencias y Letras: Informática I, Biología I, Química I
(1, 1, 16), (1, 2, 8), (1, 3, 4),
-- Computación: Informática I y II, Física I
(2, 1, 16), (2, 2, 17), (2, 3, 6),
-- Biología: Biología I y II, Química I
(3, 1, 8), (3, 2, 9), (3, 3, 4),
-- Química: Química I y II, Biología I
(4, 1, 4), (4, 2, 5), (4, 3, 8),
-- Física: Física I y II, Química I
(5, 1, 6), (5, 2, 7), (5, 3, 4);

-- Generación de las notas. Cada alumno recibe los cursos de los semestres
-- que lleva cursados, en la sección que tiene asignada.
--  * La nota va de 60 a 99 en saltos de 0.5. Uso CRC32 del alumno y el
--    curso para que parezcan al azar pero salgan iguales cada vez que
--    corra el script.
--  * Al que le toque MOD(...) = 0 (más o menos 1 de cada 12 cursos) no se
--    le crea nota. La tabla guarda cursos aprobados, así que eso simula
--    un curso pendiente. Si se quiere que todos tengan todo, quitar esa
--    línea del WHERE.
INSERT INTO notas (id_alumno, id_semestre, id_seccion, id_curso, nota, activo)
SELECT a.id_alumno,
       p.id_semestre,
       d.id_seccion,
       p.id_curso,
       ROUND((60 + MOD(CRC32(CONCAT('n-', a.id_alumno, '-', p.id_curso)), 3900) / 100) * 2) / 2,
       1
FROM tmp_demo_alumnos d
JOIN alumnos a  ON a.nombres = d.nombres AND a.apellidos = d.apellidos
JOIN tmp_plan p ON p.id_semestre <= d.semestres_cursados
               AND (p.id_carrera IS NULL OR p.id_carrera = d.id_carrera)
WHERE MOD(CRC32(CONCAT('x-', a.id_alumno, '-', p.id_curso)), 12) <> 0;

DROP TEMPORARY TABLE tmp_demo_alumnos;
DROP TEMPORARY TABLE tmp_plan;


-- ---------------------------------------------------------------------
-- 6. Usuario de la aplicación
-- ---------------------------------------------------------------------
-- La app no debería conectarse como root: si alguien logra meter SQL,
-- tendría control de todo el servidor. Este usuario solo lee, inserta y
-- actualiza dentro de sistema_academico.
-- No tiene DELETE porque el sistema desactiva, no borra. Si alguna ruta
-- falla con "DELETE command denied", es que el código hace un DELETE
-- real y hay que revisarlo antes de darle ese permiso.
--
-- Cambiar la contraseña aquí y en el .env:
--   DB_USER=sistema_app
--   DB_PASS=la_misma_contraseña
-- ---------------------------------------------------------------------
CREATE USER IF NOT EXISTS 'sistema_app'@'localhost' IDENTIFIED BY 'CambiaEstaClave_2026!';
ALTER USER 'sistema_app'@'localhost' IDENTIFIED BY 'CambiaEstaClave_2026!';
GRANT SELECT, INSERT, UPDATE ON sistema_academico.* TO 'sistema_app'@'localhost';
FLUSH PRIVILEGES;


-- ---------------------------------------------------------------------
-- 7. Resumen (solo para confirmar que quedó todo)
-- ---------------------------------------------------------------------
SELECT c.nombre AS carrera,
       COUNT(DISTINCT a.id_alumno) AS alumnos,
       COUNT(n.id_nota) AS notas
FROM carreras c
LEFT JOIN alumnos a ON a.id_carrera = c.id_carrera AND a.activo = 1
LEFT JOIN notas n   ON n.id_alumno = a.id_alumno AND n.activo = 1
GROUP BY c.id_carrera, c.nombre
ORDER BY c.id_carrera;

SELECT s.nombre AS seccion, COUNT(n.id_nota) AS notas
FROM secciones s
LEFT JOIN notas n ON n.id_seccion = s.id_seccion AND n.activo = 1
GROUP BY s.id_seccion, s.nombre
ORDER BY s.id_seccion;
