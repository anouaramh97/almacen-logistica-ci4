<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Coordina las pantallas y acciones del modulo de general.
 */
class ProfileController extends BaseController
{
    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit(): string
    {
        return $this->render('profile/edit', ['user' => current_user()]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update()
    {
        if (! $this->validate([
            'name' => 'required',
            'email' => 'required|valid_email',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos del perfil.');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $avatar = $this->request->getFile('avatar');
        $data['avatar_path'] = store_project_media($avatar, 'avatar', current_user()['avatar_path'] ?? null);

        model(UserModel::class)->updateProfileData((int) current_user()['id'], $data);
        refresh_auth_user();

        return redirect()->to(site_url('profile'))->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete()
    {
        $user = current_user();
        if (($user['role_name'] ?? '') === 'administrador') {
            return redirect()->to(site_url('profile'))->with('error', 'La cuenta del administrador principal no se puede eliminar desde el perfil.');
        }

        model(UserModel::class)->deleteOwnAccount((int) $user['id'], $user['avatar_path'] ?? null);
        session()->destroy();

        return redirect()->to(site_url('/'))->with('success', 'Cuenta eliminada correctamente.');
    }
}
