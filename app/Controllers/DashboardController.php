<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

/**
 * Coordina las pantallas y acciones del modulo de general.
 */
class DashboardController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index()
    {
        $role = current_user()['role_name'] ?? null;

        if ($role === 'administrador') {
            return redirect()->to(site_url('admin/dashboard'));
        }

        if ($role === 'logistica') {
            return redirect()->to(site_url('logistics/dashboard'));
        }

        if ($role === 'cliente') {
            return redirect()->to(site_url('client/orders'));
        }

        if ($role === 'conductor') {
            return redirect()->to(site_url('driver/dashboard'));
        }

        return redirect()->to(site_url('login'))->with('error', 'No se pudo determinar tu rol.');
    }
}
