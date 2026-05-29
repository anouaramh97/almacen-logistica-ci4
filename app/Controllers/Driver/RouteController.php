<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Driver;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;
use App\Models\RouteModel;

/**
 * Coordina las pantallas y acciones del modulo de repartidor.
 */
class RouteController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $routes = model(RouteModel::class)->listForDriver((int) current_user()['id']);

        return $this->render('driver/routes/index', ['routes' => $routes]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $route = model(RouteModel::class)->findForDriver((int) $id, (int) current_user()['id']);
        $deliveries = model(DeliveryModel::class)->forRouteDetailed((int) $id);
        return $this->render('driver/routes/show', ['route' => $route, 'deliveries' => $deliveries]);
    }
}
