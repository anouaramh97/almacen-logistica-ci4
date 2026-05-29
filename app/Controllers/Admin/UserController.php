<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\UserModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class UserController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $userModel = model(UserModel::class);
        $users = $userModel->listWithRoles();
        $adminCount = $userModel->countAdmins();

        return $this->render('admin/users/index', ['users' => $users, 'adminCount' => $adminCount]);
    }

    /**
     * Prepara el formulario de alta con los datos auxiliares necesarios.
     */
    public function create(): string
    {
        return $this->render('admin/users/form', ['user' => null, 'roles' => model(RoleModel::class)->listAllByName()]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate(['name' => 'required', 'email' => 'required|valid_email', 'password' => 'required|min_length[8]', 'role_id' => 'required|integer'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos del usuario.');
        }

        model(UserModel::class)->createBackofficeUser([
            'role_id' => $this->request->getPost('role_id'),
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'status' => $this->request->getPost('status') ?: 'activo',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/users'))->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        return $this->render('admin/users/form', ['user' => model(UserModel::class)->find((int) $id), 'roles' => model(RoleModel::class)->listAllByName()]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['name' => 'required', 'email' => 'required|valid_email', 'role_id' => 'required|integer'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos del usuario.');
        }

        $data = [
            'role_id' => $this->request->getPost('role_id'),
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'status' => $this->request->getPost('status') ?: 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        model(UserModel::class)->updateBackofficeUser((int) $id, $data);

        return redirect()->to(site_url('admin/users'))->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function activate($id)
    {
        $userModel = model(UserModel::class);
        $user = $userModel->findWithRoleById((int) $id);

        if (! $user) {
            return redirect()->to(site_url('admin/dashboard'))->with('error', 'Usuario no encontrado.');
        }

        if (($user['status'] ?? '') === 'activo') {
            return redirect()->to(site_url('admin/dashboard'))->with('success', 'El usuario ya estaba activo.');
        }

        $userModel->activateUser((int) $id);

        return redirect()->to(site_url('admin/dashboard'))->with('success', 'Usuario activado correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete($id)
    {
        $userModel = model(UserModel::class);
        $user = $userModel->findWithRoleById((int) $id);
        if (! $user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Usuario no encontrado.');
        }
        if ((int) $id === (int) (current_user()['id'] ?? 0)) {
            return redirect()->to(site_url('admin/users'))->with('error', 'No puedes eliminar tu propio usuario mientras estás conectado.');
        }

        $adminCount = $userModel->countAdmins();
        if (($user['role_name'] ?? '') === 'administrador' && $adminCount <= 1) {
            return redirect()->to(site_url('admin/users'))->with('error', 'No se puede eliminar el único administrador del sistema.');
        }

        $userModel->deleteWithRelations((int) $id);

        return redirect()->to(site_url('admin/users'))->with('success', 'Usuario eliminado correctamente.');
    }
}
