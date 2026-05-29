<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Logistics;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

/**
 * Coordina las pantallas y acciones del modulo de logistica.
 */
class OrderController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $orders = model(OrderModel::class)->listForLogisticsWithInvoice();

        return $this->render('logistics/orders/index', ['orders' => $orders]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $order = model(OrderModel::class)->findForLogisticsWithInvoice((int) $id);

        if (! $order) {
            return redirect()->to(site_url('logistics/orders'))->with('error', 'El pedido todavia no esta confirmado por administracion.');
        }

        $items = model(OrderItemModel::class)->itemsForOrder((int) $id, true);

        return $this->render('logistics/orders/show', ['order' => $order, 'items' => $items]);
    }
}
