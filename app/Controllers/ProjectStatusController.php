<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

/**
 * Coordina las pantallas y acciones del modulo de general.
 */
class ProjectStatusController extends BaseController
{
    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function unavailable()
    {
        return $this->render('project/unavailable', [
            'title' => 'Módulo en desarrollo',
        ]);
    }
}
