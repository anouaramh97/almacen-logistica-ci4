<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderItemModel;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Coordina las pantallas y acciones del modulo de cliente.
 */
class InvoiceController extends BaseController
{
    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $invoice = model(InvoiceModel::class)->findDetailedForCustomer((int) $id, (int) current_user()['id']);

        if (! $invoice) {
            return redirect()->to(site_url('client/orders'))->with('error', 'Factura no encontrada.');
        }

        $items = model(OrderItemModel::class)->itemsForOrder((int) ($invoice['order_id'] ?? 0), true);

        return $this->render('client/invoices/show', ['invoice' => $invoice, 'items' => $items]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function pdf($id)
    {
        $invoice = model(InvoiceModel::class)->findDetailedForCustomer((int) $id, (int) current_user()['id']);

        if (! $invoice) {
            return redirect()->to(site_url('client/orders'))->with('error', 'Factura no encontrada.');
        }

        $items = model(OrderItemModel::class)->itemsForOrder((int) $invoice['order_id']);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('client/invoices/pdf', ['invoice' => $invoice, 'items' => $items]));
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="factura-cliente-' . $invoice['invoice_number'] . '.pdf"')
            ->setBody($dompdf->output());
    }
}
