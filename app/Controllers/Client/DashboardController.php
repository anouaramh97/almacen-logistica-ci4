<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderModel;

/**
 * Coordina las pantallas y acciones del modulo de cliente.
 */
class DashboardController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $userId = current_user()['id'] ?? 0;
        $orderModel = model(OrderModel::class);
        $invoiceModel = model(InvoiceModel::class);

        $stats = [
            'orders' => $orderModel->countForCustomer($userId),
            'pending_orders' => $orderModel->countForCustomer($userId, 'pendiente'),
            'delivered_orders' => $orderModel->countForCustomer($userId, 'entregado'),
            'invoices' => $invoiceModel->countForCustomer($userId),
        ];

        $recentOrders = $orderModel->listRecentForCustomer($userId, 5);
        $recentInvoices = $invoiceModel->listRecentForCustomer($userId, 4);

        return $this->render('client/dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentInvoices' => $recentInvoices,
        ]);
    }
}
