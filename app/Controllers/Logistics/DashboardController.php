<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Logistics;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;
use App\Models\OrderModel;
use App\Models\RouteModel;
use App\Models\UserModel;

/**
 * Coordina las pantallas y acciones del modulo de logistica.
 */
class DashboardController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $userModel = model(UserModel::class);
        $routeModel = model(RouteModel::class);
        $deliveryModel = model(DeliveryModel::class);
        $orderModel = model(OrderModel::class);

        $stats = [
            'available_drivers' => $userModel->countActiveDrivers(),
            'planned_routes' => $routeModel->countByStatus('planificada'),
            'in_transit_deliveries' => $deliveryModel->countByStatus('en_transito'),
            'ready_orders' => $orderModel->countReadyOrders(),
        ];

        $recentRoutes = $routeModel->listRecentWithDriver(5);
        $pendingOrders = $orderModel->listPendingForLogistics(4);

        return $this->render('logistics/dashboard', [
            'stats' => $stats,
            'recentRoutes' => $recentRoutes,
            'pendingOrders' => $pendingOrders,
        ]);
    }
}
