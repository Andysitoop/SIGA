<?php

/**
 * Punto de entrada principal de la aplicación
 * Sistema Académico - Fat-Free Framework
 */

// Iniciar sesión
if (!isset($_SESSION)) {
    session_start();
}

// Cargar dependencias de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración
$appConfig = require __DIR__ . '/../config/app.php';

// Inicializar Fat-Free Framework
$f3 = \Base::instance();

// Configuración básica
$f3->set('DEBUG', $appConfig['debug']);
$f3->set('UI', __DIR__ . '/../app/Views/');
$f3->set('AUTOLOAD', __DIR__ . '/../app/');
$f3->set('TEMP', __DIR__ . '/../tmp/');
$f3->set('BASE', $appConfig['url']);
$f3->set('APP_NAME', $appConfig['name']);

// Configurar directorio de plantillas
$f3->set('UPLOADS', __DIR__ . '/uploads/');

// ===== RUTAS DEL DASHBOARD =====

$f3->route('GET /', 'App\Controllers\DashboardController->index');

// ===== RUTAS DE ALUMNOS =====

$f3->route('GET /alumnos', 'App\Controllers\AlumnoController->index');
$f3->route('GET /alumnos/buscar', 'App\Controllers\AlumnoController->buscar');
$f3->route('GET /alumnos/nuevo', 'App\Controllers\AlumnoController->nuevo');
$f3->route('POST /alumnos/guardar', 'App\Controllers\AlumnoController->guardar');
$f3->route('GET /alumnos/@id', 'App\Controllers\AlumnoController->ver');
$f3->route('GET /alumnos/@id/editar', 'App\Controllers\AlumnoController->editar');
$f3->route('POST /alumnos/@id/actualizar', 'App\Controllers\AlumnoController->actualizar');
$f3->route('GET /alumnos/@id/desactivar', 'App\Controllers\AlumnoController->desactivar');
$f3->route('GET /alumnos/@id/activar', 'App\Controllers\AlumnoController->activar');

// ===== RUTAS DE NOTAS =====

$f3->route('GET /notas', 'App\Controllers\NotaController->index');
$f3->route('GET /notas/buscar', 'App\Controllers\NotaController->buscar');
$f3->route('GET /notas/historial/@id', 'App\Controllers\NotaController->historial');
$f3->route('GET /notas/nuevo', 'App\Controllers\NotaController->nuevo');
$f3->route('POST /notas/guardar', 'App\Controllers\NotaController->guardar');
$f3->route('GET /notas/@id/editar', 'App\Controllers\NotaController->editar');
$f3->route('POST /notas/@id/actualizar', 'App\Controllers\NotaController->actualizar');
$f3->route('GET /notas/@id/desactivar', 'App\Controllers\NotaController->desactivar');

// ===== RUTAS DE CARRERAS =====

$f3->route('GET /carreras', 'App\Controllers\CatalogoController->carreras');
$f3->route('GET /carreras/nuevo', 'App\Controllers\CatalogoController->carrerasNuevo');
$f3->route('POST /carreras/guardar', 'App\Controllers\CatalogoController->carrerasGuardar');
$f3->route('GET /carreras/@id/editar', 'App\Controllers\CatalogoController->carrerasEditar');
$f3->route('POST /carreras/@id/actualizar', 'App\Controllers\CatalogoController->carrerasActualizar');
$f3->route('GET /carreras/@id/desactivar', 'App\Controllers\CatalogoController->carrerasDesactivar');
$f3->route('GET /carreras/@id/activar', 'App\Controllers\CatalogoController->carrerasActivar');

// ===== RUTAS DE CURSOS =====

$f3->route('GET /cursos', 'App\Controllers\CatalogoController->cursos');
$f3->route('GET /cursos/nuevo', 'App\Controllers\CatalogoController->cursosNuevo');
$f3->route('POST /cursos/guardar', 'App\Controllers\CatalogoController->cursosGuardar');
$f3->route('GET /cursos/@id/editar', 'App\Controllers\CatalogoController->cursosEditar');
$f3->route('POST /cursos/@id/actualizar', 'App\Controllers\CatalogoController->cursosActualizar');
$f3->route('GET /cursos/@id/desactivar', 'App\Controllers\CatalogoController->cursosDesactivar');
$f3->route('GET /cursos/@id/activar', 'App\Controllers\CatalogoController->cursosActivar');

// ===== RUTAS DE SEMESTRES =====

$f3->route('GET /semestres', 'App\Controllers\CatalogoController->semestres');
$f3->route('GET /semestres/nuevo', 'App\Controllers\CatalogoController->semestresNuevo');
$f3->route('POST /semestres/guardar', 'App\Controllers\CatalogoController->semestresGuardar');
$f3->route('GET /semestres/@id/editar', 'App\Controllers\CatalogoController->semestresEditar');
$f3->route('POST /semestres/@id/actualizar', 'App\Controllers\CatalogoController->semestresActualizar');
$f3->route('GET /semestres/@id/desactivar', 'App\Controllers\CatalogoController->semestresDesactivar');
$f3->route('GET /semestres/@id/activar', 'App\Controllers\CatalogoController->semestresActivar');

// ===== RUTAS DE SECCIONES =====

$f3->route('GET /secciones', 'App\Controllers\CatalogoController->secciones');
$f3->route('GET /secciones/nuevo', 'App\Controllers\CatalogoController->seccionesNuevo');
$f3->route('POST /secciones/guardar', 'App\Controllers\CatalogoController->seccionesGuardar');
$f3->route('GET /secciones/@id/editar', 'App\Controllers\CatalogoController->seccionesEditar');
$f3->route('POST /secciones/@id/actualizar', 'App\Controllers\CatalogoController->seccionesActualizar');
$f3->route('GET /secciones/@id/desactivar', 'App\Controllers\CatalogoController->seccionesDesactivar');
$f3->route('GET /secciones/@id/activar', 'App\Controllers\CatalogoController->seccionesActivar');

// ===== RUTAS DE REPORTES =====

$f3->route('GET /reportes/alumnos', 'App\Controllers\ReporteController->alumnos');
$f3->route('GET /reportes/notas', 'App\Controllers\ReporteController->notas');
$f3->route('GET /reportes/alumno/@id', 'App\Controllers\ReporteController->alumno');
$f3->route('GET /reportes/notas/exportar-csv', 'App\Controllers\ReporteController->exportarCsv');
$f3->route('GET /reportes/alumno/@id/exportar-csv', 'App\Controllers\ReporteController->exportarAlumnoCsv');

// ===== MANEJO DE ERRORES =====

$f3->set('ONERROR', function($f3) {
    if ($f3->get('DEBUG')) {
        echo '<h1>Error</h1>';
        echo '<pre>' . $f3->get('ERROR.text') . '</pre>';
        echo '<pre>' . $f3->get('ERROR.trace') . '</pre>';
    } else {
        $f3->set('title', 'Error');
        $f3->set('error', 'Ha ocurrido un error. Por favor, contacte al administrador.');
        $f3->set('content', 'layouts/error');
        echo \Template::instance()->render('layouts/main.html');
    }
});

// Ejecutar la aplicación
$f3->run();
