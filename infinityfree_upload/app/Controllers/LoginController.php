<?php

namespace App\Controllers;

use App\Helpers\Csrf;
use App\Models\Usuario;

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
        $f3->set('csrf_token', Csrf::generateToken());

        echo \Template::instance()->render('layouts/auth.html');
    }

    /**
     * Procesa el login
     */
    public function authenticate()
    {
        if (!isset($_SESSION)) session_start();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $user = null;
        if (Csrf::validateToken((string)($_POST['csrf_token'] ?? '')) && $email !== '' && $password !== '') {
            $user = Usuario::getByEmail($email);
        }

        if ($user && (int)$user['activo'] === 1 && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = (int)$user['id_usuario'];
            $_SESSION['username'] = $user['correo'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            $_SESSION['login_time'] = time();

            header('Location: /');
            exit;
        } else {
            $f3 = \Base::instance();
            $f3->set('error', 'Correo o contraseña incorrectos.');
            $f3->set('title', 'Login - SIGA');
            $f3->set('content', 'login/index.html');
            $f3->set('csrf_token', Csrf::generateToken());

            echo \Template::instance()->render('layouts/auth.html');
        }
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        if (!isset($_SESSION)) session_start();

        $_SESSION = [];
        session_destroy();

        header('Location: /login');
        exit;
    }
}
