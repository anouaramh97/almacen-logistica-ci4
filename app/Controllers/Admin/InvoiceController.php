<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class InvoiceController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $invoices = model(InvoiceModel::class)->listForAdmin();

        return $this->render('admin/invoices/index', ['invoices' => $invoices]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $invoice = model(InvoiceModel::class)->findDetailed((int) $id);

        if (! $invoice) {
            return redirect()->to(site_url('admin/invoices'))->with('error', 'Factura no encontrada.');
        }

        return $this->render('admin/invoices/show', ['invoice' => $invoice]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function pdf($id)
    {
        $invoice = model(InvoiceModel::class)->findDetailed((int) $id);

        if (! $invoice) {
            return redirect()->to(site_url('admin/invoices'))->with('error', 'Factura no encontrada.');
        }

        $items = model(OrderItemModel::class)->itemsForOrder((int) ($invoice['order_id'] ?? 0));

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $html = view('admin/invoices/pdf', ['invoice' => $invoice, 'items' => $items]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="factura-' . $invoice['invoice_number'] . '.pdf"')
            ->setBody($dompdf->output());
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store($orderId)
    {
        $invoiceModel = model(InvoiceModel::class);
        $order = model(OrderModel::class)->find((int) $orderId);

        if (! $order) {
            return redirect()->to(site_url('admin/orders'))->with('error', 'Pedido no encontrado.');
        }

        $result = $invoiceModel->ensureForOrder($order);

        if (! ($result['success'] ?? false)) {
            return redirect()->to(site_url('admin/orders/' . $orderId))->with('error', $result['message'] ?? 'No se pudo generar la factura.');
        }

        return redirect()->to(site_url('admin/invoices/' . $result['invoice_id']))->with('success', $result['message'] ?? 'Factura generada correctamente.');
    }
}
