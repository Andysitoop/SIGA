<?php

namespace App\Controllers;

/**
 * Controlador para autenticación
 */
class LoginController
{
    /**
     * Muestra el formulario de login
     */
    public function index()
    {
        if (!isset($_SESSION)) session_start();

        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: /');
            exit;
        }

        $f3 = \Base::instance();
        $f3->set('title', 'Login - SIGA');
        $f3->set('content', 'login/index.html');
        $f3->set('error', '');

        echo \Template::instance()->render('layouts/auth.html');
    }

    /**
     * Procesa el login
     */
    public function authenticate()
    {
        if (!isset($_SESSION)) session_start();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Login demo: cualquier usuario/password funciona
        if (!empty($username) && !empty($password)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['login_time'] = time();

            header('Location: /');
            exit;
        } else {
            $f3 = \Base::instance();
            $f3->set('error', 'Por favor ingrese usuario y contraseña');
            $f3->set('title', 'Login - SIGA');
            $f3->set('content', 'login/index.html');

            echo \Template::instance()->render('layouts/auth.html');
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        if (!isset($_SESSION)) session_start();

        session_destroy();

        header('Location: /login');
        exit;
    }
}
