<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private function requireAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }

    public function index(): void
    {
        $this->requireAuth();

        $this->view('admin/usuarios/index', [
            'title' => 'Administrar Usuarios',
            'users' => User::getAll(),
        ], 'admin');
    }

    public function create(): void
    {
        $this->requireAuth();

        $this->view('admin/usuarios/create', [
            'title'  => 'Nuevo Usuario',
            'errors' => [],
            'old'    => [],
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireAuth();

        $data = $this->sanitizeInput();
        $errors = $this->validate($data, true);

        if (!empty($errors)) {
            $this->view('admin/usuarios/create', [
                'title'  => 'Nuevo Usuario',
                'errors' => $errors,
                'old'    => $data,
            ], 'admin');
            return;
        }

        User::createUser($data);
        $_SESSION['flash_message'] = 'Usuario creado correctamente.';
        $this->redirect(BASE_URL . '/admin/usuarios');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();

        $user = User::findById($id);
        if (!$user) {
            $this->redirect(BASE_URL . '/admin/usuarios');
        }

        $this->view('admin/usuarios/edit', [
            'title'  => 'Editar Usuario',
            'user'   => $user,
            'errors' => [],
            'old'    => $user,
        ], 'admin');
    }

    public function update(int $id): void
    {
        $this->requireAuth();

        $user = User::findById($id);
        if (!$user) {
            $this->redirect(BASE_URL . '/admin/usuarios');
        }

        $data = $this->sanitizeInput();
        $errors = $this->validate($data, false, $id);

        if (!empty($errors)) {
            $this->view('admin/usuarios/edit', [
                'title'  => 'Editar Usuario',
                'user'   => $user,
                'errors' => $errors,
                'old'    => array_merge($user, $data),
            ], 'admin');
            return;
        }

        User::updateUser($id, $data);

        if ((int) ($_SESSION['admin_id'] ?? 0) === $id) {
            $_SESSION['admin_user'] = $data['username'];
        }

        $_SESSION['flash_message'] = 'Usuario actualizado correctamente.';
        $this->redirect(BASE_URL . '/admin/usuarios');
    }

    public function destroy(int $id): void
    {
        $this->requireAuth();

        if ((int) ($_SESSION['admin_id'] ?? 0) === $id) {
            $_SESSION['flash_error'] = 'No puedes eliminar tu propio usuario mientras tienes la sesi&oacute;n activa.';
            $this->redirect(BASE_URL . '/admin/usuarios');
        }

        if (User::countAll() <= 1) {
            $_SESSION['flash_error'] = 'Debe existir al menos un usuario administrador.';
            $this->redirect(BASE_URL . '/admin/usuarios');
        }

        User::deleteUser($id);
        $_SESSION['flash_message'] = 'Usuario eliminado correctamente.';
        $this->redirect(BASE_URL . '/admin/usuarios');
    }

    private function sanitizeInput(): array
    {
        return [
            'username'              => trim($_POST['username'] ?? ''),
            'email'                 => trim($_POST['email'] ?? ''),
            'password'              => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
        ];
    }

    private function validate(array $data, bool $passwordRequired, ?int $ignoreId = null): array
    {
        $errors = [];

        if ($data['username'] === '') {
            $errors[] = 'El usuario es obligatorio.';
        } elseif (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $data['username'])) {
            $errors[] = 'El usuario debe tener 3 a 50 caracteres y solo puede usar letras, n&uacute;meros, punto, guion y guion bajo.';
        } else {
            $existing = User::findByUsername($data['username']);
            if ($existing && (int) $existing['id'] !== (int) $ignoreId) {
                $errors[] = 'Ese usuario ya existe.';
            }
        }

        if ($data['email'] === '') {
            $errors[] = 'El correo es obligatorio.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ingresa un correo v&aacute;lido.';
        } else {
            $existing = User::findByEmail($data['email']);
            if ($existing && (int) $existing['id'] !== (int) $ignoreId) {
                $errors[] = 'Ese correo ya est&aacute; registrado.';
            }
        }

        $password = $data['password'];
        $confirmation = $data['password_confirmation'];
        if ($passwordRequired || $password !== '' || $confirmation !== '') {
            if (strlen($password) < 8) {
                $errors[] = 'La contrase&ntilde;a debe tener al menos 8 caracteres.';
            }

            if ($password !== $confirmation) {
                $errors[] = 'La confirmaci&oacute;n de contrase&ntilde;a no coincide.';
            }
        }

        return $errors;
    }
}
