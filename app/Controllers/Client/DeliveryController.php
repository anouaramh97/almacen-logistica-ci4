<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;

/**
 * Coordina las pantallas de seguimiento de entregas para clientes.
 */
class DeliveryController extends BaseController
{
    /**
     * Lista las entregas del cliente autenticado.
     */
    public function index(): string
    {
        $deliveries = model(DeliveryModel::class)->listForCustomer((int) current_user()['id']);

        return $this->render('client/deliveries/index', ['deliveries' => $deliveries]);
    }

    /**
     * Muestra el seguimiento detallado de una entrega.
     */
    public function show($id): string
    {
        $delivery = model(DeliveryModel::class)->findForCustomerDetail((int) $id, (int) current_user()['id']);

        if (! $delivery) {
            return redirect()->to(site_url('client/deliveries'))->with('error', 'Entrega no encontrada.');
        }

        return $this->render('client/deliveries/show', ['delivery' => $delivery]);
    }
}
