<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'auth'];
    protected array $viewData = [];

    /**
     * Inicializa servicios compartidos por todos los controladores.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $locale = session('locale') ?: 'es';
        service('request')->setLocale($locale);
        service('language')->setLocale($locale);
        $this->viewData['currentLocale'] = $locale;
        $this->viewData['currentUser'] = refresh_auth_user();
    }

    /**
     * Renderiza una vista dentro del layout comun del proyecto.
     */
    protected function render(string $view, array $data = []): string
    {
        return view($view, array_merge($this->viewData, $data));
    }
}
