<?php

namespace App\Controllers;

use App\Helpers\DemoReportData;

/**
 * Controlador para generación de reportes
 */
class ReporteController
{
    /**
     * Muestra el reporte general de alumnos por carrera
     */
    public function alumnos()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $idCarrera = $this->filterId($f3->get('GET.id_carrera'));
        $carreras = DemoReportData::careers();
        $alumnos = DemoReportData::students($idCarrera);
        
        $f3->set('carreras', $carreras);
        $f3->set('alumnos', $alumnos);
        $f3->set('alumnos_count', count($alumnos));
        $f3->set('id_carrera', $idCarrera ?? '');
        $f3->set('title', 'Reporte de Alumnos por Carrera');
        $f3->set('content', 'reportes/alumnos.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el reporte de notas con filtros
     */
    public function notas()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $idCarrera = $this->filterId($f3->get('GET.id_carrera'));
        $idCurso = $this->filterId($f3->get('GET.id_curso'));
        $idSeccion = $this->filterId($f3->get('GET.id_seccion'));
        $idSemestre = $this->filterId($f3->get('GET.id_semestre'));
        $notas = DemoReportData::grades($idCarrera, $idCurso, $idSeccion, $idSemestre);

        $f3->set('carreras', DemoReportData::careers());
        $f3->set('cursos', DemoReportData::courses());
        $f3->set('secciones', DemoReportData::sections());
        $f3->set('semestres', DemoReportData::semesters());
        $f3->set('notas', $notas);
        $f3->set('id_carrera', $idCarrera ?? '');
        $f3->set('id_curso', $idCurso ?? '');
        $f3->set('id_seccion', $idSeccion ?? '');
        $f3->set('id_semestre', $idSemestre ?? '');
        $f3->set('nota_minima', 60);
        $f3->set('title', 'Reporte de Notas');
        $f3->set('content', 'reportes/notas.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el reporte individual de un alumno
     */
    public function alumno()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        $alumno = DemoReportData::student((int)$id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->alumnos();
            return;
        }
        
        $notas = DemoReportData::gradesForStudent((int)$id);
        $stats = DemoReportData::studentStats((int)$id);
        
        $f3->set('alumno', $alumno);
        $f3->set('notas', $notas);
        $f3->set('stats', $stats);
        $f3->set('nota_minima', 60);
        $f3->set('title', 'Reporte Individual del Alumno');
        $f3->set('content', 'reportes/alumno.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Exporta el reporte de notas a CSV
     */
    public function exportarCsv()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        [$notas, $filters] = $this->filteredGrades($f3);

        $rows = [
            ['REPORTE DE NOTAS'],
            ['Generado el:', date('d/m/Y H:i')],
            ['Registros:', count($notas)],
            ...$this->filterRows($filters),
            [],
            ['DETALLE DE NOTAS'],
            [
            'Carrera',
            'Alumno',
            'Curso',
            'Sección',
            'Semestre',
            'Nota',
            'Estado'
            ],
        ];
        $tableRows = [];
        foreach ($notas as $nota) {
            $tableRows[] = [
                $nota['nombre_carrera'],
                $nota['apellidos'] . ', ' . $nota['nombres'],
                $nota['codigo_curso'] . ' - ' . $nota['nombre_curso'],
                $nota['nombre_seccion'],
                $nota['nombre_semestre'],
                $nota['nota'],
                $nota['aprobado'] ? 'Aprobado' : 'Reprobado'
            ];
        }
        $rows = array_merge($rows, $tableRows);

        $this->downloadCsv('reporte_notas_' . date('Y-m-d') . '.csv', $rows);
    }

    public function exportarDoc(): void
    {
        $f3 = \Base::instance();
        [$notas, $filters] = $this->filteredGrades($f3);
        $rows = array_map(static fn(array $nota): array => [
            $nota['nombre_carrera'],
            $nota['apellidos'] . ', ' . $nota['nombres'],
            $nota['codigo_curso'] . ' - ' . $nota['nombre_curso'],
            $nota['nombre_seccion'],
            $nota['nombre_semestre'],
            $nota['nota'],
            $nota['aprobado'] ? 'Aprobado' : 'Reprobado',
        ], $notas);

        $this->downloadDoc(
            'reporte_notas_' . date('Y-m-d') . '.doc',
            'Reporte de Notas',
            array_merge([['Generado el', date('d/m/Y H:i')], ['Total de registros', count($notas)]], $filters),
            ['Carrera', 'Alumno', 'Curso', 'Sección', 'Semestre', 'Nota', 'Estado'],
            $rows
        );
    }
    
    /**
     * Exporta el reporte individual de un alumno a CSV
     */
    public function exportarAlumnoCsv()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        $alumno = DemoReportData::student((int)$id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->alumnos();
            return;
        }
        
        $notas = DemoReportData::gradesForStudent((int)$id);
        $stats = DemoReportData::studentStats((int)$id);
        $rows = [
            ['REPORTE INDIVIDUAL DEL ALUMNO'],
            ['Generado el:', date('d/m/Y H:i')],
            [],
            ['Nombres:', $alumno['nombres']],
            ['Apellidos:', $alumno['apellidos']],
            ['Carrera:', $alumno['nombre_carrera']],
            ['Fecha de Nacimiento:', $this->formatDate($alumno['fecha_nacimiento'])],
            ['Fecha de Registro:', $this->formatDate($alumno['fecha_registro'], true)],
            [],
            ['ESTADÍSTICAS'],
            ['Promedio General:', $stats['promedio_general']],
            ['Cursos Aprobados:', $stats['cursos_aprobados']],
            ['Cursos Reprobados:', $stats['cursos_reprobados']],
            ['Total de Cursos:', $stats['total_cursos']],
            [],
            ['HISTORIAL DE NOTAS'],
            [
            'Semestre',
            'Sección',
            'Código Curso',
            'Nombre Curso',
            'Nota',
            'Estado'
            ],
        ];
        foreach ($notas as $nota) {
            $rows[] = [
                $nota['nombre_semestre'],
                $nota['nombre_seccion'],
                $nota['codigo_curso'],
                $nota['nombre_curso'],
                $nota['nota'],
                $nota['aprobado'] ? 'Aprobado' : 'Reprobado'
            ];
        }

        $filename = 'reporte_alumno_' . preg_replace('/[^a-z0-9_-]/i', '_', $alumno['apellidos']) . '_' . date('Y-m-d') . '.csv';
        $this->downloadCsv($filename, $rows);
    }

    public function exportarAlumnoDoc(): void
    {
        $f3 = \Base::instance();
        $id = (int)$f3->get('PARAMS.id');
        $alumno = DemoReportData::student($id);
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->alumnos();
            return;
        }

        $notas = DemoReportData::gradesForStudent($id);
        $stats = DemoReportData::studentStats($id);
        $rows = array_map(static fn(array $nota): array => [
            $nota['nombre_semestre'],
            $nota['nombre_seccion'],
            $nota['codigo_curso'],
            $nota['nombre_curso'],
            $nota['nota'],
            $nota['aprobado'] ? 'Aprobado' : 'Reprobado',
            $nota['fecha_registro'],
        ], $notas);
        $filename = 'reporte_alumno_' . preg_replace('/[^a-z0-9_-]/i', '_', $alumno['apellidos']) . '_' . date('Y-m-d') . '.doc';

        $this->downloadDoc(
            $filename,
            'Reporte Individual del Alumno',
            [
                ['Generado el', date('d/m/Y H:i')],
                ['Alumno', $alumno['apellidos'] . ', ' . $alumno['nombres']],
                ['Carrera', $alumno['nombre_carrera']],
                ['Fecha de nacimiento', $this->formatDate($alumno['fecha_nacimiento'])],
                ['Fecha de registro', $this->formatDate($alumno['fecha_registro'], true)],
                ['Promedio general', $stats['promedio_general']],
                ['Cursos aprobados', $stats['cursos_aprobados']],
                ['Cursos reprobados', $stats['cursos_reprobados']],
                ['Total de cursos', $stats['total_cursos']],
            ],
            ['Semestre', 'Sección', 'Código', 'Curso', 'Nota', 'Estado', 'Fecha'],
            $rows
        );
    }

    public function exportarAlumnosCsv(): void
    {
        $f3 = \Base::instance();
        $careerId = $this->filterId($f3->get('GET.id_carrera'));
        $students = DemoReportData::students($careerId);
        $career = $this->labelForId(DemoReportData::careers(), 'id_carrera', 'nombre', $careerId, 'Todas las carreras');
        $rows = [
            ['REPORTE DE ALUMNOS POR CARRERA'],
            ['Generado el:', date('d/m/Y H:i')],
            ['Carrera:', $career],
            ['Total de alumnos:', count($students)],
            [],
            ['DETALLE DE ALUMNOS'],
            [
            'Apellidos',
            'Nombres',
            'Fecha de Nacimiento',
            'Carrera',
            'Fecha de Registro',
            ],
        ];

        foreach ($students as $student) {
            $rows[] = [
                $student['apellidos'],
                $student['nombres'],
                $this->formatDate($student['fecha_nacimiento']),
                $student['nombre_carrera'],
                $this->formatDate($student['fecha_registro'], true),
            ];
        }

        $this->downloadCsv('reporte_alumnos_' . date('Y-m-d') . '.csv', $rows);
    }

    public function exportarAlumnosDoc(): void
    {
        $f3 = \Base::instance();
        $careerId = $this->filterId($f3->get('GET.id_carrera'));
        $students = DemoReportData::students($careerId);
        $career = $this->labelForId(DemoReportData::careers(), 'id_carrera', 'nombre', $careerId, 'Todas las carreras');
        $rows = array_map(fn(array $student): array => [
            $student['apellidos'],
            $student['nombres'],
            $this->formatDate($student['fecha_nacimiento']),
            $student['nombre_carrera'],
            $this->formatDate($student['fecha_registro'], true),
        ], $students);

        $this->downloadDoc(
            'reporte_alumnos_' . date('Y-m-d') . '.doc',
            'Reporte de Alumnos por Carrera',
            [['Generado el', date('d/m/Y H:i')], ['Carrera', $career], ['Total de alumnos', count($students)]],
            ['Apellidos', 'Nombres', 'Fecha de nacimiento', 'Carrera', 'Fecha de registro'],
            $rows
        );
    }

    private function filteredGrades(object $f3): array
    {
        $careerId = $this->filterId($f3->get('GET.id_carrera'));
        $courseId = $this->filterId($f3->get('GET.id_curso'));
        $sectionId = $this->filterId($f3->get('GET.id_seccion'));
        $semesterId = $this->filterId($f3->get('GET.id_semestre'));
        $filters = [
            ['Carrera', $this->labelForId(DemoReportData::careers(), 'id_carrera', 'nombre', $careerId, 'Todas')],
            ['Curso', $this->labelForId(DemoReportData::courses(), 'id_curso', 'codigo', $courseId, 'Todos')],
            ['Sección', $this->labelForId(DemoReportData::sections(), 'id_seccion', 'nombre', $sectionId, 'Todas')],
            ['Semestre', $this->labelForId(DemoReportData::semesters(), 'id_semestre', 'nombre', $semesterId, 'Todos')],
        ];

        if ($courseId !== null) {
            $course = $this->labelForId(DemoReportData::courses(), 'id_curso', 'nombre', $courseId, '');
            $filters[1][1] .= ' - ' . $course;
        }

        return [DemoReportData::grades($careerId, $courseId, $sectionId, $semesterId), $filters];
    }

    private function filterRows(array $filters): array
    {
        return array_map(static fn(array $filter): array => [$filter[0] . ':', $filter[1]], $filters);
    }

    private function labelForId(array $items, string $idKey, string $labelKey, ?int $id, string $fallback): string
    {
        if ($id === null) {
            return $fallback;
        }

        foreach ($items as $item) {
            if ($item[$idKey] === $id) {
                return $item[$labelKey];
            }
        }

        return $fallback;
    }

    private function formatDate(?string $date, bool $includeTime = false): string
    {
        if (!$date) {
            return '';
        }

        $timestamp = strtotime($date);
        return $timestamp === false ? $date : date($includeTime ? 'd/m/Y H:i' : 'd/m/Y', $timestamp);
    }

    private function filterId(mixed $value): ?int
    {
        return is_scalar($value) && ctype_digit((string)$value) && (int)$value > 0
            ? (int)$value
            : null;
    }

    private function downloadCsv(string $filename, array $rows): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($output, $row, ',', '"', '');
        }
        fclose($output);
        exit;
    }

    private function downloadDoc(string $filename, string $title, array $details, array $headers, array $rows): void
    {
        header('Content-Type: application/msword; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $escape = static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $escape($title) . '</title>';
        echo '<style>body{font-family:Arial,sans-serif;color:#222;font-size:10pt}h1{color:#174a6e;font-size:18pt}table{border-collapse:collapse;width:100%;margin:12px 0 22px}th,td{border:1px solid #b8c5ce;padding:6px;text-align:left}th{background:#174a6e;color:#fff}.details th{background:#e8eef2;color:#222;width:25%}.muted{color:#666}</style>';
        echo '</head><body><h1>' . $escape($title) . '</h1>';
        echo '<p class="muted">Sistema Académico</p><table class="details"><tbody>';
        foreach ($details as [$label, $value]) {
            echo '<tr><th>' . $escape($label) . '</th><td>' . $escape($value) . '</td></tr>';
        }
        echo '</tbody></table><table><thead><tr>';
        foreach ($headers as $header) {
            echo '<th>' . $escape($header) . '</th>';
        }
        echo '</tr></thead><tbody>';
        if ($rows === []) {
            echo '<tr><td colspan="' . count($headers) . '">No hay registros para mostrar.</td></tr>';
        } else {
            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($row as $value) {
                    echo '<td>' . $escape($value) . '</td>';
                }
                echo '</tr>';
            }
        }
        echo '</tbody></table></body></html>';
        exit;
    }
}
