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
        $notas = DemoReportData::grades(
            $this->filterId($f3->get('GET.id_carrera')),
            $this->filterId($f3->get('GET.id_curso')),
            $this->filterId($f3->get('GET.id_seccion')),
            $this->filterId($f3->get('GET.id_semestre'))
        );

        $rows = [[
            'Carrera',
            'Alumno',
            'Curso',
            'Sección',
            'Semestre',
            'Nota',
            'Estado'
        ]];
        foreach ($notas as $nota) {
            $rows[] = [
                $nota['nombre_carrera'],
                $nota['apellidos'] . ', ' . $nota['nombres'],
                $nota['codigo_curso'] . ' - ' . $nota['nombre_curso'],
                $nota['nombre_seccion'],
                $nota['nombre_semestre'],
                $nota['nota'],
                $nota['aprobado'] ? 'Aprobado' : 'Reprobado'
            ];
        }

        $this->downloadCsv('reporte_notas_' . date('Y-m-d') . '.csv', $rows);
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
            [],
            ['Nombres:', $alumno['nombres']],
            ['Apellidos:', $alumno['apellidos']],
            ['Carrera:', $alumno['nombre_carrera']],
            ['Fecha de Nacimiento:', $alumno['fecha_nacimiento']],
            ['Fecha de Registro:', $alumno['fecha_registro']],
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

    public function exportarAlumnosCsv(): void
    {
        $f3 = \Base::instance();
        $careerId = $this->filterId($f3->get('GET.id_carrera'));
        $rows = [[
            'Apellidos',
            'Nombres',
            'Fecha de Nacimiento',
            'Carrera',
            'Fecha de Registro',
        ]];

        foreach (DemoReportData::students($careerId) as $student) {
            $rows[] = [
                $student['apellidos'],
                $student['nombres'],
                $student['fecha_nacimiento'],
                $student['nombre_carrera'],
                $student['fecha_registro'],
            ];
        }

        $this->downloadCsv('reporte_alumnos_' . date('Y-m-d') . '.csv', $rows);
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
}
