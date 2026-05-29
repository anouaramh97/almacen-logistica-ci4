<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class OrderController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $orderModel = model(OrderModel::class);
        $orders = $orderModel->listAllWithCustomer();

        $orderIds = array_column($orders, 'id');
        $itemsByOrder = [];

        if (! empty($orderIds)) {
            $items = model(OrderItemModel::class)->itemsForOrdersSummary($orderIds);

            foreach ($items as $item) {
                $itemsByOrder[$item['order_id']][] = $item;
            }
        }

        return $this->render('admin/orders/index', ['orders' => $orders, 'itemsByOrder' => $itemsByOrder]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $order = model(OrderModel::class)->findWithCustomer((int) $id);
        $items = model(OrderItemModel::class)->itemsForOrder((int) $id, true);
        $invoice = model(InvoiceModel::class)->where('order_id', $id)->first();
        return $this->render('admin/orders/show', ['order' => $order, 'items' => $items, 'invoice' => $invoice]);
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $order = model(OrderModel::class)->find((int) $id);
        return $this->render('admin/orders/edit', ['order' => $order]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['status' => 'required'])) return redirect()->back()->withInput()->with('error', 'Selecciona un estado valido.');

        $result = model(OrderModel::class)->updateStatusFromAdmin((int) $id, (string) $this->request->getPost('status'));

        if (! ($result['success'] ?? false)) {
            return redirect()->back()->withInput()->with('error', $result['message'] ?? 'No se pudo actualizar el pedido.');
        }

        $redirectTo = (string) ($this->request->getPost('redirect_to') ?? '');
        if ($redirectTo !== '') {
            return redirect()->to($redirectTo)->with('success', $result['message'] ?? 'Estado del pedido actualizado correctamente.');
        }

        return redirect()->to(site_url('admin/orders'))->with('success', $result['message'] ?? 'Estado del pedido actualizado correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete($id)
    {
        $result = model(OrderModel::class)->deleteFromAdmin((int) $id);

        if (! ($result['success'] ?? false)) {
            return redirect()->back()->with('error', $result['message'] ?? 'No se pudo eliminar el pedido.');
        }

        return redirect()->to(site_url('admin/orders'))->with('success', $result['message'] ?? 'Pedido eliminado correctamente.');
    }
}
