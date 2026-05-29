<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Driver;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;
use App\Models\RouteModel;

/**
 * Coordina las pantallas y acciones del modulo de repartidor.
 */
class DashboardController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $userId = current_user()['id'] ?? 0;
        $routeModel = model(RouteModel::class);
        $deliveryModel = model(DeliveryModel::class);

        $stats = [
            'assigned_routes' => $routeModel->countForDriver($userId),
            'active_routes' => $routeModel->countForDriver($userId, 'en_progreso'),
            'pending_deliveries' => $deliveryModel->countPendingByDriver($userId),
            'completed_deliveries' => $deliveryModel->countDeliveredByDriver($userId),
        ];

        $todayRoutes = $routeModel->listRecentForDriver($userId, 5);
        $routeIds = array_column($todayRoutes, 'id');
        $deliveryCounts = $deliveryModel->countsByRouteIds($routeIds);

        return $this->render('driver/dashboard', [
            'stats' => $stats,
            'todayRoutes' => $todayRoutes,
            'deliveryCounts' => $deliveryCounts,
        ]);
    }
}
