<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Driver;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;
use App\Models\OrderModel;
use App\Models\RouteModel;

/**
 * Coordina las pantallas y acciones del modulo de repartidor.
 */
class DeliveryController extends BaseController
{
    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $delivery = model(DeliveryModel::class)->findForDriverDetail((int) $id, (int) current_user()['id']);

        return $this->render('driver/deliveries/show', ['delivery' => $delivery]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        $deliveryModel = model(DeliveryModel::class);
        $orderModel = model(OrderModel::class);
        $delivery = $deliveryModel->findForDriverDetail((int) $id, (int) current_user()['id']);

        if (! $delivery) {
            return redirect()->to(site_url('driver/dashboard'))->with('error', 'Entrega no encontrada.');
        }

        $requestedStatus = (string) ($this->request->getPost('transition_status') ?? '');
        $status = $requestedStatus !== '' ? $requestedStatus : (string) ($delivery['status'] ?? 'pendiente');

        if (! $this->isAllowedTransition((string) $delivery['status'], $status)) {
            return redirect()->back()->withInput()->with('error', 'No puedes volver a un estado anterior ni hacer un cambio no permitido.');
        }

        $data = [
            'status' => $status,
            'recipient_name' => $this->request->getPost('recipient_name'),
            'observations' => $this->request->getPost('observations'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($status === 'en_transito' && empty($delivery['departed_at'])) {
            $data['departed_at'] = date('Y-m-d H:i:s');
        }

        if ($status === 'entregada') {
            $data['delivered_at'] = $delivery['delivered_at'] ?: date('Y-m-d H:i:s');
        } else {
            $data['delivered_at'] = null;
        }

        $deliveryModel->updateDeliveryProgress((int) $id, $data);

        $orderStatus = $this->mapDeliveryStatusToOrderStatus($status);
        $orderModel->updateStatus((int) $delivery['order_id'], $orderStatus);
        model(RouteModel::class)->syncStatusFromDeliveries((int) $delivery['route_id']);

        return redirect()->to(site_url('driver/deliveries/' . $id))->with('success', 'Entrega actualizada correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function isAllowedTransition(string $currentStatus, string $nextStatus): bool
    {
        if ($currentStatus === $nextStatus) {
            return in_array($currentStatus, ['pendiente', 'en_transito', 'entregada', 'fallida'], true);
        }

        $allowedTransitions = [
            'pendiente' => ['en_transito', 'fallida'],
            'en_transito' => ['entregada', 'fallida'],
            'entregada' => [],
            'fallida' => [],
        ];

        return in_array($nextStatus, $allowedTransitions[$currentStatus] ?? [], true);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function mapDeliveryStatusToOrderStatus(string $deliveryStatus): string
    {
        if ($deliveryStatus === 'en_transito') {
            return 'en_ruta';
        }

        if ($deliveryStatus === 'entregada') {
            return 'entregado';
        }

        if ($deliveryStatus === 'fallida') {
            return 'cancelado';
        }

        return 'pendiente';
    }
}
