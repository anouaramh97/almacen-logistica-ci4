<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\BrevoMailer;
use App\Models\UserModel;
use Throwable;

/**
 * Coordina las pantallas y acciones del modulo de autenticacion.
 */
class AuthController extends BaseController
{
    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function login(): string
    {
        if (is_logged_in()) {
            return redirect()->to(site_url('dashboard'));
        }

        return $this->render('auth/login', ['guestLayout' => true]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function attemptLogin()
    {
        if (! $this->validate(['email' => 'required|valid_email', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa el correo y la contraseña.');
        }

        $user = model(UserModel::class)->findByEmail((string) $this->request->getPost('email'));

        if (! $user || ! password_verify((string) $this->request->getPost('password'), (string) $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa el correo y la contraseña.');
        }

        if (($user['status'] ?? 'activo') !== 'activo') {
            return redirect()->back()->withInput()->with('error', 'Tu cuenta esta pendiente de aprobacion por el administrador.');
        }

        session()->set('auth_user', [
            'id' => $user['id'],
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar_path' => $user['avatar_path'] ?? null,
        ]);

        return redirect()->to(site_url('dashboard'))->with('success', 'Sesión iniciada correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function register(): string
    {
        if (is_logged_in()) {
            return redirect()->to(site_url('dashboard'));
        }

        return $this->render('auth/register', ['guestLayout' => true]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function attemptRegister()
    {
        if (! $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos del registro.');
        }

        $now = date('Y-m-d H:i:s');

        model(UserModel::class)->createClient([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->to(site_url('login'))->with('success', 'Cuenta creada correctamente. Queda pendiente de aprobacion por el administrador antes de iniciar sesion.');
    }

    /**
     * Obtiene datos vinculados a una entidad concreta.
     */
    public function forgotPassword(): string
    {
        if (is_logged_in()) {
            return redirect()->to(site_url('dashboard'));
        }

        return $this->render('auth/forgot_password', ['guestLayout' => true]);
    }

    /**
     * Envia una notificacion o mensaje externo desde el sistema.
     */
    public function sendResetLink()
    {
        if (! $this->validate(['email' => 'required|valid_email'])) {
            return redirect()->back()->withInput()->with('error', 'Introduce un correo electronico valido.');
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $user = model(UserModel::class)->findByEmail($email);
        $message = 'Te hemos enviado un enlace para cambiar la contrasena.';

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Este correo electronico no esta registrado.');
        }

        if (($user['status'] ?? 'activo') !== 'activo') {
            return redirect()->back()->withInput()->with('error', 'Esta cuenta no esta activa. Contacta con el administrador.');
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);
        $userModel = model(UserModel::class);

        $userModel->update((int) $user['id'], [
            'remember_token' => 'reset:' . $tokenHash . ':' . strtotime('+60 minutes'),
        ]);

        try {
            (new BrevoMailer())->sendPasswordReset(
                $email,
                (string) ($user['name'] ?? ''),
                site_url('reset-password/' . $plainToken)
            );
        } catch (Throwable $exception) {
            log_message('error', 'Password reset email failed: {message}', ['message' => $exception->getMessage()]);

            $userModel->update((int) $user['id'], [
                'remember_token' => null,
            ]);

            return redirect()->back()->withInput()->with('error', 'No se pudo enviar el correo de recuperacion. Revisa la configuracion de Brevo.');
        }

        return redirect()->to(site_url('forgot-password'))->with('success', $message);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function resetPassword(string $token)
    {
        if (! $this->isValidResetToken($token)) {
            return redirect()->to(site_url('forgot-password'))->with('error', 'El enlace de recuperacion no es valido o ha caducado.');
        }

        return $this->render('auth/reset_password', [
            'guestLayout' => true,
            'token' => $token,
        ]);
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updatePassword()
    {
        if (! $this->validate([
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[password]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Revisa la nueva contrasena.');
        }

        $token = (string) $this->request->getPost('token');
        $user = $this->findUserByValidResetToken((string) $token);

        if (! $user) {
            return redirect()->to(site_url('forgot-password'))->with('error', 'El enlace de recuperacion no es valido o ha caducado.');
        }

        model(UserModel::class)->update((int) $user['id'], [
            'password' => $this->request->getPost('password'),
            'remember_token' => null,
        ]);

        return redirect()->to(site_url('login'))->with('success', 'Contrasena actualizada correctamente. Ya puedes iniciar sesion.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function logout()
    {
        session()->remove('auth_user');

        return redirect()->to(site_url('login'))->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function isValidResetToken(string $token): bool
    {
        return (bool) $this->findUserByValidResetToken($token);
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    private function findUserByValidResetToken(string $token): ?array
    {
        if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $user = model(UserModel::class)
            ->like('remember_token', 'reset:' . hash('sha256', $token) . ':', 'after')
            ->first();

        if (! $user || empty($user['remember_token'])) {
            return null;
        }

        $parts = explode(':', (string) $user['remember_token']);

        if (count($parts) !== 3 || $parts[0] !== 'reset') {
            return null;
        }

        if (! hash_equals($parts[1], hash('sha256', $token))) {
            return null;
        }

        if ((int) $parts[2] < time()) {
            model(UserModel::class)->update((int) $user['id'], ['remember_token' => null]);

            return null;
        }

        return $user;
    }
}
