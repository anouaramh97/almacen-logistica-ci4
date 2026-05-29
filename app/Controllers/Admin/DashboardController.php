<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\RouteModel;
use App\Models\StockModel;
use App\Models\UserModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class DashboardController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $orderModel = model(OrderModel::class);
        $stockModel = model(StockModel::class);
        $routeModel = model(RouteModel::class);
        $productModel = model(ProductModel::class);
        $invoiceModel = model(InvoiceModel::class);
        $userModel = model(UserModel::class);

        $recentOrders = $orderModel->listRecentWithCustomer(5);
        $attentionStockItems = $stockModel->lowStockItems(5);
        $outOfStockItems = $stockModel->outOfStockItems(5);
        $pendingUsers = $userModel->listPendingClients(5);

        $routeStatus = [
            'planificada' => $routeModel->countByStatus('planificada'),
            'en_progreso' => $routeModel->countByStatus('en_progreso'),
            'completada' => $routeModel->countByStatus('completada'),
        ];

        $deliveredOrders = $orderModel->countByStatus('entregado');
        $totalOrders = $orderModel->countByStatus();

        $stats = [
            'products' => $productModel->countAllProducts(),
            'quantity_in_hand' => $stockModel->countDistinctProductsInHand(),
            'pending_orders' => $orderModel->countByStatus('pendiente'),
            'active_routes' => $routeModel->whereIn('status', ['planificada', 'en_progreso'])->countAllResults(),
            'pending_invoices' => $invoiceModel->countPending(),
            'pending_users' => $userModel->countPendingClients(),
            'delivered_orders' => $deliveredOrders,
            'low_stock' => $stockModel->countLowStock(),
            'out_of_stock' => $stockModel->countOutOfStock(),
            'completion_rate' => $totalOrders > 0 ? (int) round(($deliveredOrders / $totalOrders) * 100) : 0,
        ];

        return $this->render('admin/dashboard', [
            'title' => 'Dashboard Admin',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'attentionStockItems' => $attentionStockItems,
            'outOfStockItems' => $outOfStockItems,
            'pendingUsers' => $pendingUsers,
            'routeStatus' => $routeStatus,
        ]);
    }
}
