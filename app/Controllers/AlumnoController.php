<?php

namespace App\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Helpers\File;

/**
 * Controlador para gestión de alumnos
 */
class AlumnoController
{
    /**
     * Muestra la lista de alumnos
     */
    public function index()
    {
        if (!isset($_SESSION)) session_start();
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /login');
            exit;
        }
        
        $f3 = \Base::instance();
        $page = $f3->get('GET.page') ?? 1;
        $limit = 20;
        
        $alumnos = Alumno::getAll($page, $limit);
        $total = Alumno::count();
        $totalPages = ceil($total / $limit);
        
        $f3->set('alumnos', $alumnos);
        $f3->set('page', $page);
        $f3->set('totalPages', $totalPages);
        $f3->set('total', $total);
        $f3->set('title', 'Alumnos');
        $f3->set('content', 'alumnos/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Busca alumnos
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
            $alumnos = Alumno::search($query, 20);
        }
        
        $f3->set('alumnos', $alumnos);
        $f3->set('query', $query);
        $f3->set('title', 'Buscar Alumnos');
        $f3->set('content', 'alumnos/buscar.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear un alumno
     */
    public function nuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $carreras = Carrera::getAll();
        
        $f3->set('carreras', $carreras);
        $f3->set('title', 'Nuevo Alumno');
        $f3->set('content', 'alumnos/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda un nuevo alumno
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
        
        if (!Validation::required($_POST['nombres'] ?? '')) {
            $errors[] = 'Los nombres son obligatorios';
        }
        
        if (!Validation::required($_POST['apellidos'] ?? '')) {
            $errors[] = 'Los apellidos son obligatorios';
        }
        
        if (!Validation::required($_POST['fecha_nacimiento'] ?? '')) {
            $errors[] = 'La fecha de nacimiento es obligatoria';
        } elseif (!Validation::date($_POST['fecha_nacimiento'])) {
            $errors[] = 'La fecha de nacimiento no es válida';
        }
        
        if (!Validation::required($_POST['id_carrera'] ?? '')) {
            $errors[] = 'La carrera es obligatoria';
        }
        
        // Validar y procesar imagen
        $fotoNombre = null;
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
            if (!File::isValidImage($_FILES['fotografia'], $config['allowed_image_types'], $config['max_file_size'])) {
                $errors[] = 'La imagen no es válida. Debe ser JPG, JPEG o PNG y no superar 2MB';
            } else {
                $fotoNombre = File::generateUniqueName($_FILES['fotografia']['name']);
                $uploadPath = $config['upload_path'] . $fotoNombre;
                
                if (!File::upload($_FILES['fotografia'], $uploadPath)) {
                    $errors[] = 'Error al subir la imagen';
                    $fotoNombre = null;
                }
            }
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->nuevo();
            return;
        }
        
        try {
            Alumno::create([
                'nombres' => Validation::escape($_POST['nombres']),
                'apellidos' => Validation::escape($_POST['apellidos']),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'fotografia' => $fotoNombre,
                'id_carrera' => (int)$_POST['id_carrera'],
                'activo' => 1
            ]);
            
            $f3->set('success', 'Alumno creado exitosamente');
            $this->index();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al crear el alumno: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->nuevo();
        }
    }
    
    /**
     * Muestra los detalles de un alumno
     */
    public function ver()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        $alumno = Alumno::getById($id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->index();
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        $f3->set('alumno', $alumno);
        $f3->set('title', 'Detalle del Alumno');
        $f3->set('content', 'alumnos/ver.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para editar un alumno
     */
    public function editar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        $alumno = Alumno::getById($id);
        $carreras = Carrera::getAll();
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->index();
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        
        $f3->set('alumno', $alumno);
        $f3->set('carreras', $carreras);
        $f3->set('title', 'Editar Alumno');
        $f3->set('content', 'alumnos/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza un alumno
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
        
        if (!Validation::required($_POST['nombres'] ?? '')) {
            $errors[] = 'Los nombres son obligatorios';
        }
        
        if (!Validation::required($_POST['apellidos'] ?? '')) {
            $errors[] = 'Los apellidos son obligatorios';
        }
        
        if (!Validation::required($_POST['fecha_nacimiento'] ?? '')) {
            $errors[] = 'La fecha de nacimiento es obligatoria';
        } elseif (!Validation::date($_POST['fecha_nacimiento'])) {
            $errors[] = 'La fecha de nacimiento no es válida';
        }
        
        if (!Validation::required($_POST['id_carrera'] ?? '')) {
            $errors[] = 'La carrera es obligatoria';
        }
        
        // Validar y procesar nueva imagen
        $fotoNombre = null;
        if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] === UPLOAD_ERR_OK) {
            if (!File::isValidImage($_FILES['fotografia'], $config['allowed_image_types'], $config['max_file_size'])) {
                $errors[] = 'La imagen no es válida. Debe ser JPG, JPEG o PNG y no superar 2MB';
            } else {
                // Eliminar foto anterior si existe
                $alumnoActual = Alumno::getById($id);
                if ($alumnoActual && $alumnoActual['fotografia']) {
                    File::delete($config['upload_path'] . $alumnoActual['fotografia']);
                }
                
                $fotoNombre = File::generateUniqueName($_FILES['fotografia']['name']);
                $uploadPath = $config['upload_path'] . $fotoNombre;
                
                if (!File::upload($_FILES['fotografia'], $uploadPath)) {
                    $errors[] = 'Error al subir la imagen';
                    $fotoNombre = null;
                }
            }
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->editar();
            return;
        }
        
        try {
            $data = [
                'nombres' => Validation::escape($_POST['nombres']),
                'apellidos' => Validation::escape($_POST['apellidos']),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'],
                'id_carrera' => (int)$_POST['id_carrera']
            ];
            
            if ($fotoNombre) {
                $data['fotografia'] = $fotoNombre;
            }
            
            Alumno::update($id, $data);
            
            $f3->set('success', 'Alumno actualizado exitosamente');
            $this->index();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar el alumno: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->editar();
        }
    }
    
    /**
     * Desactiva un alumno
     */
    public function desactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        try {
            Alumno::deactivate($id);
            $f3->set('success', 'Alumno desactivado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar el alumno: ' . $e->getMessage());
        }
        
        $this->index();
    }
    
    /**
     * Activa un alumno
     */
    public function activar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('Param.id');
        
        try {
            Alumno::activate($id);
            $f3->set('success', 'Alumno activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el alumno: ' . $e->getMessage());
        }
        
        $this->index();
    }
}
