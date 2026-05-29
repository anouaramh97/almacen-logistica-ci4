<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

/**
 * Agrupa logica reutilizable del proyecto.
 */
class Home extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index()
    {
        if (is_logged_in()) {
            return redirect()->to(site_url('dashboard'));
        }

        return $this->render('home');
    }
}
