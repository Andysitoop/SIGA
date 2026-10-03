<?php

namespace App\Controllers;

use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Nota;

/**
 * Controlador para el dashboard principal
 */
class DashboardController
{
    /**
     * Muestra el dashboard con estadísticas
     */
    public function index()
    {
        if (!isset($_SESSION)) session_start();
        
        // Verificar autenticación
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: /login');
            exit;
        }
        
        $f3 = \Base::instance();
        $config = require __DIR__ . '/../../config/app.php';
        
        // Estadísticas generales
        $totalAlumnos = Alumno::count();
        $totalCarreras = Carrera::count();
        $totalCursos = Curso::count();
        $totalNotas = Nota::count();
        
        // Últimos alumnos registrados
        $ultimosAlumnos = Alumno::getLatest(5);
        
        // Últimas notas ingresadas
        $ultimasNotas = Nota::getLatest(5);
        
        $f3->set('total_alumnos', $totalAlumnos);
        $f3->set('total_carreras', $totalCarreras);
        $f3->set('total_cursos', $totalCursos);
        $f3->set('total_notas', $totalNotas);
        $f3->set('ultimos_alumnos', $ultimosAlumnos);
        $f3->set('ultimas_notas', $ultimasNotas);
        $f3->set('APP_NAME', $config['name']);
        $f3->set('title', 'Dashboard');
        $f3->set('content', 'dashboard/index.html');
        
        echo \Template::instance()->render('layouts/main.html');
    }
}
