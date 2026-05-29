<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

/**
 * Coordina las pantallas y acciones del modulo de general.
 */
class LocaleController extends BaseController
{
    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update()
    {
        $locale = (string) $this->request->getPost('locale');

        if (! in_array($locale, ['es', 'en'], true)) {
            return redirect()->back()->with('error', 'Idioma no valido.');
        }

        session()->set('locale', $locale);

        return redirect()->back();
    }
}
