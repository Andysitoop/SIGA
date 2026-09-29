<?php

namespace App\Controllers;

use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Semestre;
use App\Models\Seccion;
use App\Helpers\Csrf;
use App\Helpers\Validation;

/**
 * Controlador para catálogos (Carreras, Cursos, Semestres, Secciones)
 */
class CatalogoController
{
    /**
     * Muestra la lista de carreras
     */
    public function carreras()
    {
        if (!isset($_SESSION)) session_start();
        
        $carreras = Carrera::getAll(false);
        $f3 = \Base::instance();
        
        $f3->set('carreras', $carreras);
        $f3->set('title', 'Carreras');
        $f3->set('content', 'catalogos/carreras/index');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear una carrera
     */
    public function carrerasNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('title', 'Nueva Carrera');
        $f3->set('content', 'catalogos/carreras/form');
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
        $id = $f3->get('Param.id');
        
        $carrera = Carrera::getById($id);
        
        if (!$carrera) {
            $f3->set('error', 'Carrera no encontrada');
            $this->carreras();
            return;
        }
        
        $f3->set('carrera', $carrera);
        $f3->set('title', 'Editar Carrera');
        $f3->set('content', 'catalogos/carreras/form');
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
        try {
            Carrera::activate($id);
            $f3->set('success', 'Carrera activada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar la carrera: ' . $e->getMessage());
        }
        
        $this->carreras();
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
        $f3->set('title', 'Cursos');
        $f3->set('content', 'catalogos/cursos/index');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear un curso
     */
    public function cursosNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('title', 'Nuevo Curso');
        $f3->set('content', 'catalogos/cursos/form');
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
        $id = $f3->get('Param.id');
        
        $curso = Curso::getById($id);
        
        if (!$curso) {
            $f3->set('error', 'Curso no encontrado');
            $this->cursos();
            return;
        }
        
        $f3->set('curso', $curso);
        $f3->set('title', 'Editar Curso');
        $f3->set('content', 'catalogos/cursos/form');
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
        try {
            Curso::activate($id);
            $f3->set('success', 'Curso activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el curso: ' . $e->getMessage());
        }
        
        $this->cursos();
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
        $f3->set('title', 'Semestres');
        $f3->set('content', 'catalogos/semestres/index');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear un semestre
     */
    public function semestresNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('title', 'Nuevo Semestre');
        $f3->set('content', 'catalogos/semestres/form');
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
        $id = $f3->get('Param.id');
        
        $semestre = Semestre::getById($id);
        
        if (!$semestre) {
            $f3->set('error', 'Semestre no encontrado');
            $this->semestres();
            return;
        }
        
        $f3->set('semestre', $semestre);
        $f3->set('title', 'Editar Semestre');
        $f3->set('content', 'catalogos/semestres/form');
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
        try {
            Semestre::activate($id);
            $f3->set('success', 'Semestre activado exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar el semestre: ' . $e->getMessage());
        }
        
        $this->semestres();
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
        $f3->set('title', 'Secciones');
        $f3->set('content', 'catalogos/secciones/index');
        
        echo \Template::instance()->render('layouts/main.html');
    }
    
    /**
     * Muestra el formulario para crear una sección
     */
    public function seccionesNuevo()
    {
        if (!isset($_SESSION)) session_start();
        
        $f3 = \Base::instance();
        $f3->set('title', 'Nueva Sección');
        $f3->set('content', 'catalogos/secciones/form');
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
        $id = $f3->get('Param.id');
        
        $seccion = Seccion::getById($id);
        
        if (!$seccion) {
            $f3->set('error', 'Sección no encontrada');
            $this->secciones();
            return;
        }
        
        $f3->set('seccion', $seccion);
        $f3->set('title', 'Editar Sección');
        $f3->set('content', 'catalogos/secciones/form');
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
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
        $id = $f3->get('Param.id');
        
        try {
            Seccion::activate($id);
            $f3->set('success', 'Sección activada exitosamente');
        } catch (\Exception $e) {
            $f3->set('error', 'Error al activar la sección: ' . $e->getMessage());
        }
        
        $this->secciones();
    }
}
