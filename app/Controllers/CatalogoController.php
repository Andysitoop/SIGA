<?php

namespace App\Controllers;

use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Semestre;
use App\Models\Seccion;
use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Helpers\Access;
use App\Helpers\File;
use App\Models\Alumno;

/**
 * Controlador para catálogos (Carreras, Cursos, Semestres, Secciones)
 */
class CatalogoController
{
    public function __construct()
    {
        Access::requireAdmin();
    }

    /**
     * Muestra la lista de carreras
     */
    public function carreras()
    {
        if (!isset($_SESSION)) session_start();
        
        $carreras = Carrera::getAll(false);
        $f3 = \Base::instance();
        
        $f3->set('carreras', $carreras);
        $f3->set('csrf_token', Csrf::generateToken());
        $f3->set('title', 'Carreras');
        $f3->set('content', 'catalogos/carreras/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear una carrera
     */
    public function carrerasNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('carrera', null);
        $submittedData = $f3->get('data');
        $submittedData = is_array($submittedData) ? $submittedData : [];
        $f3->set('data', [
            'nombre' => $submittedData['nombre'] ?? '',
            'activo' => array_key_exists('csrf_token', $submittedData)
                ? isset($submittedData['activo'])
                : true
        ]);
        $f3->set('form_action', '/carreras/guardar');
        $f3->set('title', 'Nueva Carrera');
        $f3->set('content', 'catalogos/carreras/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda una nueva carrera
     */
    public function carrerasGuardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->carrerasNuevo();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->carrerasNuevo();
            return;
        }
        
        try {
            Carrera::create([
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Carrera creada exitosamente');
            $this->carreras();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al crear la carrera: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->carrerasNuevo();
        }
    }
    
    /**
     * Muestra el formulario para editar una carrera
     */
    public function carrerasEditar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        $carrera = Carrera::getById($id);
        
        if (!$carrera) {
            $f3->set('error', 'Carrera no encontrada');
            $this->carreras();
            return;
        }
        
        $f3->set('carrera', $carrera);
        $submittedData = $f3->get('data');
        $submittedData = is_array($submittedData) ? $submittedData : [];
        $f3->set('data', [
            'nombre' => $submittedData['nombre'] ?? $carrera['nombre'],
            'activo' => array_key_exists('csrf_token', $submittedData)
                ? isset($submittedData['activo'])
                : $carrera['activo']
        ]);
        $f3->set('form_action', '/carreras/' . $carrera['id_carrera'] . '/actualizar');
        $f3->set('title', 'Editar Carrera');
        $f3->set('content', 'catalogos/carreras/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza una carrera
     */
    public function carrerasActualizar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->carrerasEditar();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->carrerasEditar();
            return;
        }
        
        try {
            Carrera::update($id, [
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Carrera actualizada exitosamente');
            $this->carreras();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar la carrera: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->carrerasEditar();
        }
    }
    
    /**
     * Desactiva una carrera
     */
    public function carrerasDesactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Carrera::deactivate($id);
            $f3->set('success', 'Carrera desactivada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar la carrera: ' . $e->getMessage());
        }
        
        $this->carreras();
    }
    
    /**
     * Activa una carrera
     */
    public function carrerasActivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Carrera::activate($id);
            $f3->set('success', 'Carrera activada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar la carrera: ' . $e->getMessage());
        }
        
        $this->carreras();
    }

    public function carrerasEliminar()
    {
        $this->eliminarCatalogo(Carrera::class, 'carrera', 'carreras');
    }
    
    // ===== MÉTODOS PARA CURSOS =====
    
    /**
     * Muestra la lista de cursos
     */
    public function cursos()
    {
        if (!isset($_SESSION)) session_start();
        
        $cursos = Curso::getAll(false);
        $f3 = \Base::instance();
        
        $f3->set('cursos', $cursos);
        $f3->set('csrf_token', Csrf::generateToken());
        $f3->set('title', 'Cursos');
        $f3->set('content', 'catalogos/cursos/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear un curso
     */
    public function cursosNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('curso', null);
        $f3->set('data', []);
        $f3->set('form_action', '/cursos/guardar');
        $f3->set('title', 'Nuevo Curso');
        $f3->set('content', 'catalogos/cursos/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda un nuevo curso
     */
    public function cursosGuardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->cursosNuevo();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['codigo'] ?? '')) {
            $errors[] = 'El código es obligatorio';
        }
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (Curso::codeExists($_POST['codigo'])) {
            $errors[] = 'El código ya existe';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->cursosNuevo();
            return;
        }
        
        try {
            Curso::create([
                'codigo' => Validation::escape($_POST['codigo']),
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Curso creado exitosamente');
            $this->cursos();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al crear el curso: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->cursosNuevo();
        }
    }
    
    /**
     * Muestra el formulario para editar un curso
     */
    public function cursosEditar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        $curso = Curso::getById($id);
        
        if (!$curso) {
            $f3->set('error', 'Curso no encontrado');
            $this->cursos();
            return;
        }
        
        $f3->set('curso', $curso);
        $f3->set('form_action', '/cursos/' . $curso['id_curso'] . '/actualizar');
        $f3->set('title', 'Editar Curso');
        $f3->set('content', 'catalogos/cursos/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza un curso
     */
    public function cursosActualizar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->cursosEditar();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['codigo'] ?? '')) {
            $errors[] = 'El código es obligatorio';
        }
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (Curso::codeExists($_POST['codigo'], $id)) {
            $errors[] = 'El código ya existe';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->cursosEditar();
            return;
        }
        
        try {
            Curso::update($id, [
                'codigo' => Validation::escape($_POST['codigo']),
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Curso actualizado exitosamente');
            $this->cursos();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar el curso: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->cursosEditar();
        }
    }
    
    /**
     * Desactiva un curso
     */
    public function cursosDesactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Curso::deactivate($id);
            $f3->set('success', 'Curso desactivado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar el curso: ' . $e->getMessage());
        }
        
        $this->cursos();
    }
    
    /**
     * Activa un curso
     */
    public function cursosActivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Curso::activate($id);
            $f3->set('success', 'Curso activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el curso: ' . $e->getMessage());
        }
        
        $this->cursos();
    }

    public function cursosEliminar()
    {
        $this->eliminarCatalogo(Curso::class, 'curso', 'cursos');
    }
    
    // ===== MÉTODOS PARA SEMESTRES =====
    
    /**
     * Muestra la lista de semestres
     */
    public function semestres()
    {
        if (!isset($_SESSION)) session_start();
        
        $semestres = Semestre::getAll(false);
        $f3 = \Base::instance();
        
        $f3->set('semestres', $semestres);
        $f3->set('csrf_token', Csrf::generateToken());
        $f3->set('title', 'Semestres');
        $f3->set('content', 'catalogos/semestres/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear un semestre
     */
    public function semestresNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('semestre', null);
        $f3->set('data', []);
        $f3->set('form_action', '/semestres/guardar');
        $f3->set('title', 'Nuevo Semestre');
        $f3->set('content', 'catalogos/semestres/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda un nuevo semestre
     */
    public function semestresGuardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->semestresNuevo();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->semestresNuevo();
            return;
        }
        
        try {
            Semestre::create([
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Semestre creado exitosamente');
            $this->semestres();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al crear el semestre: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->semestresNuevo();
        }
    }
    
    /**
     * Muestra el formulario para editar un semestre
     */
    public function semestresEditar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        $semestre = Semestre::getById($id);
        
        if (!$semestre) {
            $f3->set('error', 'Semestre no encontrado');
            $this->semestres();
            return;
        }
        
        $f3->set('semestre', $semestre);
        $f3->set('form_action', '/semestres/' . $semestre['id_semestre'] . '/actualizar');
        $f3->set('title', 'Editar Semestre');
        $f3->set('content', 'catalogos/semestres/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza un semestre
     */
    public function semestresActualizar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->semestresEditar();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->semestresEditar();
            return;
        }
        
        try {
            Semestre::update($id, [
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Semestre actualizado exitosamente');
            $this->semestres();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar el semestre: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->semestresEditar();
        }
    }
    
    /**
     * Desactiva un semestre
     */
    public function semestresDesactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Semestre::deactivate($id);
            $f3->set('success', 'Semestre desactivado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar el semestre: ' . $e->getMessage());
        }
        
        $this->semestres();
    }
    
    /**
     * Activa un semestre
     */
    public function semestresActivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Semestre::activate($id);
            $f3->set('success', 'Semestre activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el semestre: ' . $e->getMessage());
        }
        
        $this->semestres();
    }

    public function semestresEliminar()
    {
        $this->eliminarCatalogo(Semestre::class, 'semestre', 'semestres');
    }
    
    // ===== MÉTODOS PARA SECCIONES =====
    
    /**
     * Muestra la lista de secciones
     */
    public function secciones()
    {
        if (!isset($_SESSION)) session_start();
        
        $secciones = Seccion::getAll(false);
        $f3 = \Base::instance();
        
        $f3->set('secciones', $secciones);
        $f3->set('csrf_token', Csrf::generateToken());
        $f3->set('title', 'Secciones');
        $f3->set('content', 'catalogos/secciones/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear una sección
     */
    public function seccionesNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('seccion', null);
        $f3->set('data', []);
        $f3->set('form_action', '/secciones/guardar');
        $f3->set('title', 'Nueva Sección');
        $f3->set('content', 'catalogos/secciones/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Guarda una nueva sección
     */
    public function seccionesGuardar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->seccionesNuevo();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->seccionesNuevo();
            return;
        }
        
        try {
            Seccion::create([
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Sección creada exitosamente');
            $this->secciones();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al crear la sección: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->seccionesNuevo();
        }
    }
    
    /**
     * Muestra el formulario para editar una sección
     */
    public function seccionesEditar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        $seccion = Seccion::getById($id);
        
        if (!$seccion) {
            $f3->set('error', 'Sección no encontrada');
            $this->secciones();
            return;
        }
        
        $f3->set('seccion', $seccion);
        $f3->set('form_action', '/secciones/' . $seccion['id_seccion'] . '/actualizar');
        $f3->set('title', 'Editar Sección');
        $f3->set('content', 'catalogos/secciones/form.html');
        $f3->set('csrf_token', Csrf::generateToken());
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Actualiza una sección
     */
    public function seccionesActualizar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        // Validar CSRF
        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF');
            $this->seccionesEditar();
            return;
        }
        
        // Validaciones
        $errors = [];
        
        if (!Validation::required($_POST['nombre'] ?? '')) {
            $errors[] = 'El nombre es obligatorio';
        }
        
        if (!empty($errors)) {
            $f3->set('error', implode('<br>', $errors));
            $f3->set('data', $_POST);
            $this->seccionesEditar();
            return;
        }
        
        try {
            Seccion::update($id, [
                'nombre' => Validation::escape($_POST['nombre']),
                'activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            
            $f3->set('success', 'Sección actualizada exitosamente');
            $this->secciones();
        } catch (\Exception $e) {
            $f3->set('error', 'Error al actualizar la sección: ' . $e->getMessage());
            $f3->set('data', $_POST);
            $this->seccionesEditar();
        }
    }
    
    /**
     * Desactiva una sección
     */
    public function seccionesDesactivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Seccion::deactivate($id);
            $f3->set('success', 'Sección desactivada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al desactivar la sección: ' . $e->getMessage());
        }
        
        $this->secciones();
    }
    
    /**
     * Activa una sección
     */
    public function seccionesActivar()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $id = $f3->get('PARAMS.id');
        
        try {
            Seccion::activate($id);
            $f3->set('success', 'Sección activada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar la sección: ' . $e->getMessage());
        }
        
        $this->secciones();
    }

    public function seccionesEliminar()
    {
        $this->eliminarCatalogo(Seccion::class, 'sección', 'secciones');
    }

    private function eliminarCatalogo(string $model, string $entity, string $listMethod): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $f3 = \Base::instance();
        $id = filter_var($f3->get('PARAMS.id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);

        if ($id === false) {
            $f3->set('error', 'El identificador del registro no es válido.');
            $this->{$listMethod}();
            return;
        }

        if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            $f3->set('error', 'Error de validación CSRF. No se eliminó el registro.');
            $this->{$listMethod}();
            return;
        }

        if ($model::getById($id) === null) {
            $f3->set('error', ucfirst($entity) . ' no encontrada.');
            $this->{$listMethod}();
            return;
        }

        $mode = (string)($_POST['delete_mode'] ?? 'restrict');
        if (!in_array($mode, ['restrict', 'cascade'], true)) {
            $f3->set('error', 'La opción para registros asociados no es válida.');
            $this->{$listMethod}();
            return;
        }

        try {
            $photos = $model === Carrera::class && $mode === 'cascade'
                ? Alumno::getPhotosByCareer($id)
                : [];
            $deleted = $model::delete($id, $mode === 'cascade');
            if ($deleted && $photos !== []) {
                $uploadPath = (require __DIR__ . '/../../config/app.php')['upload_path'];
                foreach ($photos as $photo) {
                    File::delete($uploadPath . $photo['fotografia']);
                }
            }
            $f3->set('success', ucfirst($entity) . ' eliminada permanentemente.');
        } catch (\PDOException $error) {
            if ((string)$error->getCode() === '23000') {
                $f3->set(
                    'error',
                    'La ' . $entity . ' tiene registros relacionados. Elige borrar también esos registros para continuar.'
                );
            } elseif (($error->errorInfo[1] ?? null) === 1142) {
                $f3->set(
                    'error',
                    'La cuenta MySQL de la aplicación necesita permiso DELETE. Ejecute sql/habilitar_eliminacion.sql con un administrador MySQL.'
                );
            } else {
                $f3->set('error', 'Error de base de datos al eliminar la ' . $entity . '.');
            }
        }

        $this->{$listMethod}();
    }
}
