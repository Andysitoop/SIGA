<?php

namespace App\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Nota;
use App\Helpers\Access;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Helpers\File;

/**
 * Controlador para gestión de alumnos
 */
class AlumnoController
{
    public function __construct()
    {
        Access::requireAdmin();
    }

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
        
        $alumnos = $this->withPhotoUrls(Alumno::getAll($page, $limit));
        $total = Alumno::count();
        $totalPages = ceil($total / $limit);
        
        $f3->set('alumnos', $alumnos);
        $f3->set('page', $page);
        $f3->set('totalPages', $totalPages);
        $f3->set('total', $total);
        $f3->set('query', '');
        $f3->set('csrf_token', Csrf::generateToken());
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
            $alumnos = $this->withPhotoUrls(Alumno::search($query, 20));
        }
        
        $f3->set('alumnos', $alumnos);
        $f3->set('query', $query);
        $f3->set('title', 'Buscar Alumnos');
        $f3->set('content', 'alumnos/buscar.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }

    private function withPhotoUrls(array $alumnos): array
    {
        $uploadUrl = (require __DIR__ . '/../../config/app.php')['upload_url'];

        foreach ($alumnos as &$alumno) {
            $alumno['foto_url'] = !empty($alumno['fotografia'])
                ? $uploadUrl . $alumno['fotografia']
                : '';
        }
        unset($alumno);

        return $alumnos;
    }

    private function storeUploadedPhoto(array $foto, array $config, array &$errors): ?string
    {
        $uploadError = (int)($foto['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadError === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($uploadError !== UPLOAD_ERR_OK) {
            $errors[] = File::uploadErrorMessage($uploadError)
                ?? 'No se pudo recibir la imagen.';
            return null;
        }

        try {
            if (!File::isValidImage($foto, $config['allowed_image_types'], $config['max_file_size'])) {
                $errors[] = 'La imagen no es válida. Debe ser JPG, JPEG o PNG y no superar 2MB';
                return null;
            }

            $filename = File::generateUniqueName($foto);
            if (!File::upload($foto, $config['upload_path'] . $filename)) {
                $errors[] = 'No se pudo guardar la imagen. La aplicación crea public/uploads/ si falta; si el problema continúa, configura esa carpeta con permisos de escritura para PHP desde el Administrador de archivos de InfinityFree.';
                return null;
            }

            return $filename;
        } catch (\Throwable $error) {
            error_log('SIGA: fallo al procesar fotografía: ' . $error);
            $errors[] = 'No se pudo procesar la imagen. Intenta con una imagen JPG o PNG de hasta 2MB. Si el problema continúa, contacta al administrador.';
            return null;
        }
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
        $f3->set('alumno', null);
        $f3->set('foto_url', '');
        $f3->set('data', []);
        $f3->set('form_action', '/alumnos/guardar');
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
        if (isset($_FILES['fotografia'])) {
            $fotoNombre = $this->storeUploadedPhoto($_FILES['fotografia'], $config, $errors);
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
        $id = $f3->get('PARAMS.id');
        
        $alumno = Alumno::getById($id);
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->index();
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        $notas = Nota::getByAlumno((int)$id);
        
        $f3->set('alumno', $alumno);
        $f3->set('notas', $notas);
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
        $id = $f3->get('PARAMS.id');
        
        $alumno = Alumno::getById($id);
        $carreras = Carrera::getAll();
        
        if (!$alumno) {
            $f3->set('error', 'Alumno no encontrado');
            $this->index();
            return;
        }
        
        $config = require __DIR__ . '/../../config/app.php';
        $alumno['foto_url'] = $alumno['fotografia'] ? $config['upload_url'] . $alumno['fotografia'] : null;
        $submittedData = $f3->get('data');
        $submittedData = is_array($submittedData) ? $submittedData : [];
        
        $f3->set('alumno', $alumno);
        $f3->set('foto_url', $alumno['foto_url'] ?? '');
        $f3->set('data', [
            'nombres' => $submittedData['nombres'] ?? $alumno['nombres'],
            'apellidos' => $submittedData['apellidos'] ?? $alumno['apellidos'],
            'fecha_nacimiento' => $submittedData['fecha_nacimiento'] ?? $alumno['fecha_nacimiento'],
            'id_carrera' => $submittedData['id_carrera'] ?? $alumno['id_carrera'],
            'remove_photo' => $submittedData['remove_photo'] ?? ''
        ]);
        $f3->set('carreras', $carreras);
        $f3->set('form_action', '/alumnos/' . $alumno['id_alumno'] . '/actualizar');
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
        $id = $f3->get('PARAMS.id');
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
        
        $alumnoActual = Alumno::getById((int)$id);
        $fotoAnterior = $alumnoActual['fotografia'] ?? null;
        $quitarFoto = isset($_POST['remove_photo']) && $_POST['remove_photo'] === '1';
        $fotoNombre = null;
        if (isset($_FILES['fotografia'])) {
            $fotoNombre = $this->storeUploadedPhoto($_FILES['fotografia'], $config, $errors);
        }
        
        if (!empty($errors)) {
            if ($fotoNombre !== null) {
                File::delete($config['upload_path'] . $fotoNombre);
            }
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
            
            if ($fotoNombre !== null) {
                $data['fotografia'] = $fotoNombre;
            } elseif ($quitarFoto) {
                $data['fotografia'] = null;
            }
            
            Alumno::update($id, $data);

            if (($fotoNombre !== null || $quitarFoto) && $fotoAnterior) {
                File::delete($config['upload_path'] . $fotoAnterior);
            }
            
            $f3->set('success', 'Alumno actualizado exitosamente');
            $this->index();
        } catch (\Exception $e) {
            if ($fotoNombre !== null) {
                File::delete($config['upload_path'] . $fotoNombre);
            }
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
        $id = $f3->get('PARAMS.id');
        
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
        $id = $f3->get('PARAMS.id');
        
        try {
            Alumno::activate($id);
            $f3->set('success', 'Alumno activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el alumno: ' . $e->getMessage());
        }
        
        $this->index();
    }

    public function eliminar()
    {
        $f3 = \Base::instance();
        $id = filter_var($f3->get('PARAMS.id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        $mode = (string)($_POST['delete_mode'] ?? 'restrict');

        if ($id === false || !Csrf::validateToken((string)($_POST['csrf_token'] ?? ''))
            || !in_array($mode, ['restrict', 'cascade'], true)) {
            $f3->set('error', 'La solicitud de eliminación no es válida.');
            $this->index();
            return;
        }

        $alumno = Alumno::getById($id);
        if (!$alumno) {
            $f3->set('error', 'No se encontró el alumno.');
            $this->index();
            return;
        }

        try {
            Alumno::delete($id, $mode === 'cascade');
            if (!empty($alumno['fotografia'])) {
                $config = require __DIR__ . '/../../config/app.php';
                File::delete($config['upload_path'] . $alumno['fotografia']);
            }
            $f3->set('success', 'Alumno eliminado permanentemente.');
        } catch (\PDOException $error) {
            if ((string)$error->getCode() === '23000') {
                $f3->set('error', 'El alumno tiene notas asociadas. Elige borrar también las notas para continuar.');
            } elseif (($error->errorInfo[1] ?? null) === 1142) {
                $f3->set('error', 'La cuenta MySQL de la aplicación necesita permiso DELETE. Ejecuta sql/habilitar_eliminacion.sql con un administrador MySQL.');
            } else {
                $f3->set('error', 'No se pudo eliminar el alumno por un error de base de datos.');
            }
        }

        $this->index();
    }
}
