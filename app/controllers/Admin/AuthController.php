<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/dashboard');
        }

        $this->view('admin/login', [
            'title' => 'Iniciar Sesión',
        ], 'admin');
    }

    public function login(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $error    = '';

        if (empty($username) || empty($password)) {
            $error = 'Por favor ingresa tu usuario y contraseña.';
        } else {
            $user = User::findByUsername($username);

            if ($user && User::verifyPassword($password, $user['password'])) {
                $_SESSION['admin_id']  = $user['id'];
                $_SESSION['admin_user'] = $user['username'];
                $this->redirect(BASE_URL . '/admin/dashboard');
            } else {
                $error = 'Usuario o contraseña incorrectos.';
            }
        }

        $this->view('admin/login', [
            'title' => 'Iniciar Sesión',
            'error' => $error,
        ], 'admin');
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_user']);
        session_destroy();
        $this->redirect(BASE_URL . '/admin/login');
    }
}
