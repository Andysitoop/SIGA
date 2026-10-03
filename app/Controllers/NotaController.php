<?php

namespace App\Controllers;

use App\Models\Nota;
use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Semestre;
use App\Models\Seccion;
use App\Helpers\Csrf;
use App\Helpers\Validation;

/**
 * Controlador para gestión de notas
 */
class NotaController
{
    /**
     * Muestra la lista de notas
     */
    public function index()
    {
        if (!isset($_SESSION)) session_start();
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /login');
            exit;
        }
        
        $f3 = \Base::instance();
        $notas = Nota::getLatest(20);
        
        $f3->set('notas', $notas);
        $f3->set('title', 'Notas');
        $f3->set('content', 'notas/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Busca un alumno para registrar notas
     */
    public function buscar()
    {
        if (!isset($_SESSION)) session_start();
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /login');
            exit;
        }
        
        $f3 = \Base::instance();
        $query = $f3->get('GET.q') ?? '';
        
        $alumnos = [];
        if (!empty($query)) {
            $alumnos = Alumno::search($query, 10);
        }
        
        $f3->set('alumnos', $alumnos);
        $f3->set('query', $query);
        $f3->set('title', 'Buscar Alumno para Notas');
        $f3->set('content', 'notas/buscar.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el historial de notas de un alumno
     */
    public function historial()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        $alumno = Alumno::getById($id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->buscar();
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $notas = Nota::getByAlumno($id);
        $stats = Nota::getStudentStats($id, $config['nota_minima_aprobacion']);
        
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        $f3->set('alumno', $alumno);
        $f3->set('notas', $notas);
        $f3->set('stats', $stats);
        $f3->set('nota_minima', $config['nota_minima_aprobacion']);
        $f3->set('title', 'Historial de Notas');
        $f3->set('content', 'notas/historial.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para registrar una nota
     */
    public function nuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $idAlumno = $f3->get('GET.id_alumno');
        
        if (!$idAlumno) {
            $f3->set('error', 'Debe seleccionar un alumno primero');
            $this->buscar();
            return;
        }
        
        $alumno = Alumno::getById($idAlumno);
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->buscar();
            return;
        }
        
        $cursos = Curso::getAll();
        $semestres = Semestre::getAll();
        $secciones = Seccion::getAll();
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        $f3->set('alumno', $alumno);
        $f3->set('cursos', $cursos);
        $f3->set('semestres', $semestres);
        $f3->set('secciones', $secciones);
        $f3->set('nota_minima', $config['nota_minima_aprobacion']);
        $f3->set('nota_maxima', $config['nota_maxima']);
        $f3->set('title', 'Registrar Nota');
        $f3->set('content', 'notas/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda una nueva nota
     */
    public function guardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $config = require __DIR__ . '/../../config/app.php';
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->nuevo();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['id_alumno'] ?? '')) {
            $errors[] = 'El alumno es obligatorio';
        }
        
        if (!Validation::required($_POST['id_semestre'] ?? '')) {
            $errors[] = 'El semestre es obligatorio';
        }
        
        if (!Validation::required($_POST['id_seccion'] ?? '')) {
            $errors[] = 'La sección es obligatoria';
        }
        
        if (!Validation::required($_POST['id_curso'] ?? '')) {
            $errors[] = 'El curso es obligatorio';
        }
        
        if (!Validation::required($_POST['nota'] ?? '')) {
            $errors[] = 'La nota es obligatoria';
        } elseif (!Validation::numeric($_POST['nota'])) {
            $errors[] = 'La nota debe ser numérica';
        } elseif (!Validation::between($_POST['nota'], 0, $config['nota_maxima'])) {
            $errors[] = "La nota debe estar entre 0 y {$config['nota_maxima']}";
        }
        
        // Verificar que el alumno existe
        $alumno = Alumno::getById((int)$_POST['id_alumno']);
        if (!$alumno) {
            $errors[] = 'El alumno no existe';
        }
        
        // Verificar duplicados
        if (Nota::exists(
            (int)$_POST['id_alumno'],
            (int)$_POST['id_curso'],
            (int)$_POST['id_semestre']
        )) {
            $errors[] = 'Ya existe una nota para este alumno en el mismo curso y semestre';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->nuevo();
            return;
        }
        
        try {
            Nota::create([
                'id_alumno' => (int)$_POST['id_alumno'],
                'id_semestre' => (int)$_POST['id_semestre'],
                'id_seccion' => (int)$_POST['id_seccion'],
                'id_curso' => (int)$_POST['id_curso'],
                'nota' => (float)$_POST['nota'],
                'activo' => 1
            ]);
            
            $f3->set('success', 'Nota registrada exitosamente');
            $this->historial((int)$_POST['id_alumno']);
        } catch (\Exception $e) {
            $f3->set('error', 'Error al registrar la nota: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->nuevo();
        }
    }
    
    /**
     * Muestra el formulario para editar una nota
     */
    public function editar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        $nota = Nota::getById($id);
        
        if (!$nota) {
            $f3->set('error', 'Nota no encontrada');
            $this->index();
            return;
        }
        
        $alumno = Alumno::getById($nota['id_alumno']);
        $cursos = Curso::getAll();
        $semestres = Semestre::getAll();
        $secciones = Seccion::getAll();
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        $f3->set('nota', $nota);
        $f3->set('alumno', $alumno);
        $f3->set('cursos', $cursos);
        $f3->set('semestres', $semestres);
        $f3->set('secciones', $secciones);
        $f3->set('nota_minima', $config['nota_minima_aprobacion']);
        $f3->set('nota_maxima', $config['nota_maxima']);
        $f3->set('title', 'Editar Nota');
        $f3->set('content', 'notas/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza una nota
     */
    public function actualizar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        $config = require __DIR__ . '/../../config/app.php';
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->editar();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['id_alumno'] ?? '')) {
            $errors[] = 'El alumno es obligatorio';
        }
        
        if (!Validation::required($_POST['id_semestre'] ?? '')) {
            $errors[] = 'El semestre es obligatorio';
        }
        
        if (!Validation::required($_POST['id_seccion'] ?? '')) {
            $errors[] = 'La sección es obligatoria';
        }
        
        if (!Validation::required($_POST['id_curso'] ?? '')) {
            $errors[] = 'El curso es obligatorio';
        }
        
        if (!Validation::required($_POST['nota'] ?? '')) {
            $errors[] = 'La nota es obligatoria';
        } elseif (!Validation::numeric($_POST['nota'])) {
            $errors[] = 'La nota debe ser numérica';
        } elseif (!Validation::between($_POST['nota'], 0, $config['nota_maxima'])) {
            $errors[] = "La nota debe estar entre 0 y {$config['nota_maxima']}";
        }
        
        // Verificar duplicados (excluyendo la nota actual)
        if (Nota::exists(
            (int)$_POST['id_alumno'],
            (int)$_POST['id_curso'],
            (int)$_POST['id_semestre'],
            $id
        )) {
            $errors[] = 'Ya existe una nota para este alumno en el mismo curso y semestre';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->editar();
            return;
        }
        
        try {
            Nota::update($id, [
                'id_alumno' => (int)$_POST['id_alumno'],
                'id_semestre' => (int)$_POST['id_semestre'],
                'id_seccion' => (int)$_POST['id_seccion'],
                'id_curso' => (int)$_POST['id_curso'],
                'nota' => (float)$_POST['nota'],
                'activo' => 1
            ]);
            
            $f3->set('success', 'Nota actualizada exitosamente');
            $this->historial((int)$_POST['id_alumno']);
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar la nota: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->editar();
        }
    }
    
    /**
     * Desactiva una nota
     */
    public function desactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        try {
            $nota = Nota::getById($id);
            Nota::deactivate($id);
            $f3->set('success', 'Nota desactivada exitosamente');
            
            if ($nota) {
                $this->historial($nota['id_alumno']);
            } else {
                $this->index();
            }
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar la nota: ' . $e->getMessage());
            $this->index();
        }
    }
}
