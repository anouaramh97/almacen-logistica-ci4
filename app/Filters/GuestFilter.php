<?php

// Filtro: controla el acceso antes o despues de ejecutar una ruta.
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
/**
 * Controla el acceso a rutas antes o despues de ejecutar el controlador.
 */
class GuestFilter implements FilterInterface {
    /**
     * Evalua la peticion antes de que llegue al controlador protegido.
     */
    public function before(RequestInterface $request, $arguments = null) {
        helper('auth');
        if (is_logged_in()) return redirect()->to(site_url('dashboard'));
        return null;
    }
    /**
     * Permite actuar despues de ejecutar el controlador cuando el filtro lo requiere.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) { return null; }
}