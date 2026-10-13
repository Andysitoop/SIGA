<?php

namespace App\Controllers;

use App\Helpers\Csrf;
use App\Helpers\Validation;
use App\Models\Usuario;

class AjustesController
{
    public function index(): void
    {
        if (!$this->requireLogin()) {
            return;
        }

        $f3 = \Base::instance();
        $canManageUsers = ($_SESSION['user_role'] ?? '') === 'admin';
        $users = $canManageUsers ? Usuario::getAll() : [];
        $f3->set('usuarios', $users);
        $f3->set('account_count', count($users));
        $f3->set('can_manage_users', $canManageUsers);
        $f3->set('current_user_id', (int)$_SESSION['user_id']);
        $f3->set('csrf_token', Csrf::generateToken());
        $f3->set('success', $_SESSION['flash_success'] ?? '');
        $f3->set('error', $_SESSION['flash_error'] ?? '');
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);
        $f3->set('title', 'Ajustes');
        $f3->set('content', 'ajustes/index.html');
        echo \Template::instance()->render('layouts/main.html');
    }

    public function createUser(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        if (!Csrf::validateToken((string)($_POST['csrf_token'] ?? ''))) {
            $this->redirectWithError('La sesión del formulario venció. Inténtalo de nuevo.');
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $role = (string)($_POST['role'] ?? 'user');

        if (!Validation::required($name) || mb_strlen($name, 'UTF-8') > 80) {
            $this->redirectWithError('El nombre es obligatorio y no puede superar 80 caracteres.');
        }
        if (!Validation::email($email) || !Validation::maxLength($email, 190)) {
            $this->redirectWithError('Ingresa un correo válido.');
        }
        if (mb_strlen($password, 'UTF-8') < 12) {
            $this->redirectWithError('La contraseña debe tener al menos 12 caracteres.');
        }
        if (!in_array($role, ['admin', 'user'], true)) {
            $this->redirectWithError('El rol seleccionado no es válido.');
        }
        if (Usuario::emailExists($email)) {
            $this->redirectWithError('Esta dirección de correo ya está registrada.');
        }

        try {
            Usuario::create($name, $email, password_hash($password, PASSWORD_DEFAULT), $role);
            $_SESSION['flash_success'] = 'Cuenta creada correctamente.';
            header('Location: /ajustes');
            exit;
        } catch (\Throwable $error) {
            $this->redirectWithError('No se pudo crear la cuenta. Comprueba el correo e inténtalo de nuevo.');
        }
    }

    public function setUserStatus(): void
    {
        if (!$this->requireAdmin()) {
            return;
        }

        $f3 = \Base::instance();
        $id = (int)$f3->get('PARAMS.id');
        $active = $f3->get('PARAMS.estado') === 'activar';

        if (!Csrf::validateToken((string)($_POST['csrf_token'] ?? ''))) {
            $this->redirectWithError('La sesión del formulario venció. Inténtalo de nuevo.');
        }

        $targetUser = null;
        foreach (Usuario::getAll() as $user) {
            if ((int)$user['id_usuario'] === $id) {
                $targetUser = $user;
                break;
            }
        }

        if (!$targetUser) {
            $this->redirectWithError('No se encontró la cuenta.');
        }
        if (!$active && $id === (int)$_SESSION['user_id']) {
            $this->redirectWithError('No puedes desactivar tu propia cuenta.');
        }
        if (!$active && $targetUser['rol'] === 'admin' && Usuario::countActiveAdmins() <= 1) {
            $this->redirectWithError('Debe quedar al menos un administrador activo.');
        }

        Usuario::setActive($id, $active);
        $_SESSION['flash_success'] = $active ? 'Cuenta activada.' : 'Cuenta desactivada.';
        header('Location: /ajustes');
        exit;
    }

    private function requireAdmin(): bool
    {
        if (!$this->requireLogin()) {
            return false;
        }
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: /');
            return false;
        }

        return true;
    }

    private function requireLogin(): bool
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (($_SESSION['logged_in'] ?? false) !== true) {
            header('Location: /login');
            return false;
        }

        return true;
    }

    private function redirectWithError(string $message): never
    {
        $_SESSION['flash_error'] = $message;
        header('Location: /ajustes');
        exit;
    }
}
