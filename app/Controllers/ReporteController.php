<?php

namespace App\Controllers;

use App\Models\Alumno;
use App\Models\Nota;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Semestre;
use App\Models\Seccion;

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
        $config = require __DIR__ . '/../../config/app.php';
        
        $idCarrera = $f3->get('GET.id_carrera') ?? null;
        $carreras = Carrera::getAll();
        
        $alumnos = [];
        if ($idCarrera) {
            $alumnos = Alumno::getByCareer((int)$idCarrera);
            
            // Agregar URL de foto
            foreach ($alumnos as &$alumno) {
                $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
            }
        }
        
        $f3->set('carreras', $carreras);
        $f3->set('alumnos', $alumnos);
        $f3->set('id_carrera', $idCarrera);
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
        $config = require __DIR__ . '/../../config/app.php';
        
        $idCarrera = $f3->get('GET.id_carrera') ?? null;
        $idCurso = $f3->get('GET.id_curso') ?? null;
        $idSeccion = $f3->get('GET.id_seccion') ?? null;
        $idSemestre = $f3->get('GET.id_semestre') ?? null;
        
        $carreras = Carrera::getAll();
        $cursos = Curso::getAll();
        $secciones = Seccion::getAll();
        $semestres = Semestre::getAll();
        
        $notas = Nota::getWithFilters(
            $idCarrera ? (int)$idCarrera : null,
            $idCurso ? (int)$idCurso : null,
            $idSeccion ? (int)$idSeccion : null,
            $idSemestre ? (int)$idSemestre : null
        );
        
        // Agregar estado de aprobación
        foreach ($notas as &$nota) {
            $nota['aprobado'] = $nota['nota'] >= $config['nota_minima_aprobacion'];
        }
        
        $f3->set('carreras', $carreras);
        $f3->set('cursos', $cursos);
        $f3->set('secciones', $secciones);
        $f3->set('semestres', $semestres);
        $f3->set('notas', $notas);
        $f3->set('id_carrera', $idCarrera);
        $f3->set('id_curso', $idCurso);
        $f3->set('id_seccion', $idSeccion);
        $f3->set('id_semestre', $idSemestre);
        $f3->set('nota_minima', $config['nota_minima_aprobacion']);
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
        $id = $f3->get('Param.id');
        $config = require __DIR__ . '/../../config/app.php';
        
        $alumno = Alumno::getById($id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->alumnos();
            return;
        }
        
        $notas = Nota::getByAlumno($id);
        $stats = Nota::getStudentStats($id, $config['nota_minima_aprobacion']);
        
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        // Agregar estado de aprobación a cada nota
        foreach ($notas as &$nota) {
            $nota['aprobado'] = $nota['nota'] >= $config['nota_minima_aprobacion'];
        }
        
        $f3->set('alumno', $alumno);
        $f3->set('notas', $notas);
        $f3->set('stats', $stats);
        $f3->set('nota_minima', $config['nota_minima_aprobacion']);
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
        $config = require __DIR__ . '/../../config/app.php';
        
        $idCarrera = $f3->get('GET.id_carrera') ?? null;
        $idCurso = $f3->get('GET.id_curso') ?? null;
        $idSeccion = $f3->get('GET.id_seccion') ?? null;
        $idSemestre = $f3->get('GET.id_semestre') ?? null;
        
        $notas = Nota::getWithFilters(
            $idCarrera ? (int)$idCarrera : null,
            $idCurso ? (int)$idCurso : null,
            $idSeccion ? (int)$idSeccion : null,
            $idSemestre ? (int)$idSemestre : null
        );
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reporte_notas_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Encabezados
        fputcsv($output, [
            'Carrera',
            'Alumno',
            'Curso',
            'Sección',
            'Semestre',
            'Nota',
            'Estado'
        ]);
        
        // Datos
        foreach ($notas as $nota) {
            fputcsv($output, [
                $nota['nombre_carrera'],
                $nota['apellidos'] . ', ' . $nota['nombres'],
                $nota['codigo_curso'] . ' - ' . $nota['nombre_curso'],
                $nota['nombre_seccion'],
                $nota['nombre_semestre'],
                $nota['nota'],
                $nota['nota'] >= $config['nota_minima_aprobacion'] ? 'Aprobado' : 'Reprobado'
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Exporta el reporte individual de un alumno a CSV
     */
    public function exportarAlumnoCsv()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        $config = require __DIR__ . '/../../config/app.php';
        
        $alumno = Alumno::getById($id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->alumnos();
            return;
        }
        
        $notas = Nota::getByAlumno($id);
        $stats = Nota::getStudentStats($id, $config['nota_minima_aprobacion']);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reporte_alumno_' . $alumno['apellidos'] . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Información del alumno
        fputcsv($output, ['REPORTE INDIVIDUAL DEL ALUMNO']);
        fputcsv($output, []);
        fputcsv($output, ['Nombres:', $alumno['nombres']]);
        fputcsv($output, ['Apellidos:', $alumno['apellidos']]);
        fputcsv($output, ['Carrera:', $alumno['nombre_carrera']]);
        fputcsv($output, ['Fecha de Nacimiento:', $alumno['fecha_nacimiento']]);
        fputcsv($output, ['Fecha de Registro:', $alumno['fecha_registro']]);
        fputcsv($output, []);
        fputcsv($output, ['ESTADÍSTICAS']);
        fputcsv($output, ['Promedio General:', $stats['promedio_general']]);
        fputcsv($output, ['Cursos Aprobados:', $stats['cursos_aprobados']]);
        fputcsv($output, ['Cursos Reprobados:', $stats['cursos_reprobados']]);
        fputcsv($output, ['Total de Cursos:', $stats['total_cursos']]);
        fputcsv($output, []);
        
        // Historial de notas
        fputcsv($output, ['HISTORIAL DE NOTAS']);
        fputcsv($output, [
            'Semestre',
            'Sección',
            'Código Curso',
            'Nombre Curso',
            'Nota',
            'Estado'
        ]);
        
        foreach ($notas as $nota) {
            fputcsv($output, [
                $nota['nombre_semestre'],
                $nota['nombre_seccion'],
                $nota['codigo_curso'],
                $nota['nombre_curso'],
                $nota['nota'],
                $nota['nota'] >= $config['nota_minima_aprobacion'] ? 'Aprobado' : 'Reprobado'
            ]);
        }
        
        fclose($output);
        exit;
    }
}
