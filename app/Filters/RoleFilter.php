<?php

// Filtro: controla el acceso antes o despues de ejecutar una ruta.

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controla el acceso a rutas antes o despues de ejecutar el controlador.
 */
class RoleFilter implements FilterInterface
{
    /**
     * Evalua la peticion antes de que llegue al controlador protegido.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('auth');

        if (! is_logged_in()) {
            return redirect()->to(site_url('login'))->with('error', 'Debes iniciar sesión para continuar.');
        }

        $role = current_user()['role_name'] ?? null;
        $allowed = is_array($arguments) ? $arguments : [];

        if (! in_array($role, $allowed, true)) {
            return redirect()->to(site_url('dashboard'))->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        return null;
    }

    /**
     * Permite actuar despues de ejecutar el controlador cuando el filtro lo requiere.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
