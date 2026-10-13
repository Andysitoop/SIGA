<?php

namespace App\Helpers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Nota;
use App\Models\Seccion;
use App\Models\Semestre;

class DemoReportData
{
    private static ?int $passingGrade = null;

    public static function careers(): array
    {
        return Carrera::getAll();
    }

    public static function courses(): array
    {
        return Curso::getAll();
    }

    public static function semesters(): array
    {
        return Semestre::getAll();
    }

    public static function sections(): array
    {
        return Seccion::getAll();
    }

    public static function students(?int $careerId = null): array
    {
        return Alumno::getForReport($careerId);
    }

    public static function student(int $studentId): ?array
    {
        return Alumno::getById($studentId);
    }

    public static function grades(?int $careerId = null, ?int $courseId = null, ?int $sectionId = null, ?int $semesterId = null): array
    {
        return self::withPassingStatus(Nota::getWithFilters($careerId, $courseId, $sectionId, $semesterId));
    }

    public static function gradesForStudent(int $studentId): array
    {
        return self::withPassingStatus(Nota::getByAlumno($studentId));
    }

    public static function studentStats(int $studentId, ?int $passingGrade = null): array
    {
        return Nota::getStudentStats($studentId, $passingGrade ?? self::configuredPassingGrade());
    }

    private static function withPassingStatus(array $grades): array
    {
        $passingGrade = self::configuredPassingGrade();

        foreach ($grades as &$grade) {
            $grade['aprobado'] = (float)$grade['nota'] >= $passingGrade;
        }
        unset($grade);

        return $grades;
    }

    private static function configuredPassingGrade(): int
    {
        return self::$passingGrade ??= (int)(require __DIR__ . '/../../config/app.php')['nota_minima_aprobacion'];
    }
}