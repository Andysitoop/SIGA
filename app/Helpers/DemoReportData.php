<?php

namespace App\Helpers;

class DemoReportData
{
    private const CAREERS = [
        ['id_carrera' => 1, 'nombre' => 'Bachillerato en Ciencias'],
        ['id_carrera' => 2, 'nombre' => 'Bachillerato en Informática'],
        ['id_carrera' => 3, 'nombre' => 'Bachillerato en Administración'],
    ];

    private const COURSES = [
        ['id_curso' => 1, 'codigo' => 'MAT-101', 'nombre' => 'Matemática'],
        ['id_curso' => 2, 'codigo' => 'COM-101', 'nombre' => 'Comunicación'],
        ['id_curso' => 3, 'codigo' => 'CIE-101', 'nombre' => 'Ciencias Naturales'],
        ['id_curso' => 4, 'codigo' => 'INF-101', 'nombre' => 'Informática Aplicada'],
    ];

    private const SEMESTERS = [
        ['id_semestre' => 1, 'nombre' => '2025 - I'],
        ['id_semestre' => 2, 'nombre' => '2025 - II'],
    ];

    private const SECTIONS = [
        ['id_seccion' => 1, 'nombre' => 'A'],
        ['id_seccion' => 2, 'nombre' => 'B'],
        ['id_seccion' => 3, 'nombre' => 'C'],
    ];

    private const STUDENTS = [
        ['id_alumno' => 1, 'nombres' => 'Valeria Isabel', 'apellidos' => 'Mendoza Ruiz', 'fecha_nacimiento' => '2008-03-14', 'id_carrera' => 1, 'fecha_registro' => '2024-01-15 09:20:00'],
        ['id_alumno' => 2, 'nombres' => 'Mateo Andrés', 'apellidos' => 'Castillo León', 'fecha_nacimiento' => '2007-11-02', 'id_carrera' => 2, 'fecha_registro' => '2024-01-17 10:15:00'],
        ['id_alumno' => 3, 'nombres' => 'Camila Fernanda', 'apellidos' => 'López Vega', 'fecha_nacimiento' => '2008-07-26', 'id_carrera' => 3, 'fecha_registro' => '2024-01-20 08:45:00'],
        ['id_alumno' => 4, 'nombres' => 'Diego Sebastián', 'apellidos' => 'Ramírez Soto', 'fecha_nacimiento' => '2007-05-09', 'id_carrera' => 1, 'fecha_registro' => '2024-02-02 11:05:00'],
        ['id_alumno' => 5, 'nombres' => 'Sofía Alejandra', 'apellidos' => 'Herrera Cruz', 'fecha_nacimiento' => '2008-01-18', 'id_carrera' => 2, 'fecha_registro' => '2024-02-08 13:30:00'],
        ['id_alumno' => 6, 'nombres' => 'Gabriel Emilio', 'apellidos' => 'Pineda Flores', 'fecha_nacimiento' => '2007-09-30', 'id_carrera' => 3, 'fecha_registro' => '2024-02-12 07:55:00'],
    ];

    private const RAW_GRADES = [
        [1, 1, 1, 1, 92, '2025-05-20'], [1, 1, 1, 2, 88, '2025-05-21'], [1, 2, 1, 4, 96, '2025-10-28'],
        [2, 1, 2, 1, 78, '2025-05-20'], [2, 1, 2, 4, 94, '2025-05-22'], [2, 2, 2, 2, 83, '2025-10-28'],
        [3, 1, 3, 2, 91, '2025-05-21'], [3, 1, 3, 3, 86, '2025-05-23'], [3, 2, 3, 1, 89, '2025-10-29'],
        [4, 1, 1, 1, 67, '2025-05-20'], [4, 1, 1, 3, 73, '2025-05-23'], [4, 2, 1, 2, 58, '2025-10-29'],
        [5, 1, 2, 4, 99, '2025-05-22'], [5, 1, 2, 1, 84, '2025-05-24'], [5, 2, 2, 3, 90, '2025-10-30'],
        [6, 1, 3, 3, 76, '2025-05-23'], [6, 1, 3, 2, 81, '2025-05-24'], [6, 2, 3, 4, 87, '2025-10-30'],
    ];

    public static function careers(): array
    {
        return self::CAREERS;
    }

    public static function courses(): array
    {
        return self::COURSES;
    }

    public static function semesters(): array
    {
        return self::SEMESTERS;
    }

    public static function sections(): array
    {
        return self::SECTIONS;
    }

    public static function students(?int $careerId = null): array
    {
        $careers = self::indexBy(self::CAREERS, 'id_carrera');
        $students = [];

        foreach (self::STUDENTS as $student) {
            if ($careerId !== null && $student['id_carrera'] !== $careerId) {
                continue;
            }

            $student['nombre_carrera'] = $careers[$student['id_carrera']]['nombre'];
            $student['fotografia'] = null;
            $student['foto_url'] = null;
            $student['activo'] = 1;
            $students[] = $student;
        }

        usort($students, static fn(array $left, array $right): int => [$left['apellidos'], $left['nombres']] <=> [$right['apellidos'], $right['nombres']]);
        return $students;
    }

    public static function student(int $studentId): ?array
    {
        foreach (self::students() as $student) {
            if ($student['id_alumno'] === $studentId) {
                return $student;
            }
        }

        return null;
    }

    public static function grades(?int $careerId = null, ?int $courseId = null, ?int $sectionId = null, ?int $semesterId = null): array
    {
        $students = self::indexBy(self::students(), 'id_alumno');
        $careers = self::indexBy(self::CAREERS, 'id_carrera');
        $courses = self::indexBy(self::COURSES, 'id_curso');
        $sections = self::indexBy(self::SECTIONS, 'id_seccion');
        $semesters = self::indexBy(self::SEMESTERS, 'id_semestre');
        $grades = [];

        foreach (self::RAW_GRADES as $index => [$studentId, $gradeSemesterId, $sectionIdValue, $gradeCourseId, $score, $date]) {
            $student = $students[$studentId];
            if (($careerId !== null && $student['id_carrera'] !== $careerId)
                || ($courseId !== null && $gradeCourseId !== $courseId)
                || ($sectionId !== null && $sectionIdValue !== $sectionId)
                || ($semesterId !== null && $gradeSemesterId !== $semesterId)) {
                continue;
            }

            $course = $courses[$gradeCourseId];
            $grades[] = [
                'id_nota' => $index + 1,
                'id_alumno' => $studentId,
                'id_carrera' => $student['id_carrera'],
                'id_curso' => $gradeCourseId,
                'id_seccion' => $sectionIdValue,
                'id_semestre' => $gradeSemesterId,
                'nombres' => $student['nombres'],
                'apellidos' => $student['apellidos'],
                'nombre_carrera' => $careers[$student['id_carrera']]['nombre'],
                'codigo_curso' => $course['codigo'],
                'nombre_curso' => $course['nombre'],
                'nombre_seccion' => $sections[$sectionIdValue]['nombre'],
                'nombre_semestre' => $semesters[$gradeSemesterId]['nombre'],
                'nota' => $score,
                'aprobado' => $score >= 60,
                'fecha_registro' => $date,
            ];
        }

        return $grades;
    }

    public static function gradesForStudent(int $studentId): array
    {
        return array_values(array_filter(
            self::grades(),
            static fn(array $grade): bool => $grade['id_alumno'] === $studentId
        ));
    }

    public static function studentStats(int $studentId, int $passingGrade = 60): array
    {
        $grades = self::gradesForStudent($studentId);
        $passed = count(array_filter($grades, static fn(array $grade): bool => $grade['nota'] >= $passingGrade));
        $total = count($grades);

        return [
            'total_cursos' => $total,
            'cursos_aprobados' => $passed,
            'cursos_reprobados' => $total - $passed,
            'promedio_general' => $total === 0
                ? 0
                : round(array_sum(array_column($grades, 'nota')) / $total, 2),
        ];
    }

    private static function indexBy(array $items, string $key): array
    {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item[$key]] = $item;
        }
        return $indexed;
    }
}