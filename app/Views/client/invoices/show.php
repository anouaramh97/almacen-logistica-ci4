<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Factura <?= esc($invoice['invoice_number']) ?></h1>
        <p class="muted">Consulta el detalle de tu factura y descárgala en PDF.</p>
    </div>
    <div class="toolbar">
        <a class="btn btn-outline" href="<?= site_url('client/orders/' . $invoice['order_id']) ?>">Volver al pedido</a>
        <a class="btn btn-primary" href="<?= site_url('client/invoices/' . $invoice['id'] . '/pdf') ?>">Descargar PDF</a>
    </div>
</div>

<div class="grid-2">
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Datos generales</h3></div>
        <div class="summary-line"><span>Cliente</span><strong><?= esc($invoice['customer_name']) ?></strong></div>
        <div class="summary-line"><span>Correo</span><strong><?= esc($invoice['customer_email']) ?></strong></div>
        <div class="summary-line"><span>Fecha</span><strong><?= esc($invoice['issue_date']) ?></strong></div>
        <div class="summary-line"><span>Pedido</span><strong>#<?= esc($invoice['order_id']) ?></strong></div>
        <div class="summary-line"><span>Dirección</span><strong><?= esc($invoice['delivery_address']) ?></strong></div>
    </section>

    <section class="table-card">
        <div class="heading"><h3 class="section-title">Conceptos facturados</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>IVA</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= esc($item['product_name']) ?></td>
                            <td><?= esc($item['quantity']) ?></td>
                            <td><?= number_format((float) $item['unit_price'], 2) ?> EUR</td>
                            <td><?= number_format((float) $item['tax_rate'], 2) ?>%</td>
                            <td><?= number_format((float) $item['subtotal'], 2) ?> EUR</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="max-width:340px; margin:1.25rem 0 0 auto;">
            <div class="summary-line"><span>Subtotal</span><strong><?= number_format((float) $invoice['subtotal'], 2) ?> EUR</strong></div>
            <div class="summary-line"><span>IVA</span><strong><?= number_format((float) $invoice['tax'], 2) ?> EUR</strong></div>
            <div class="summary-line"><span>Total</span><strong><?= number_format((float) $invoice['total'], 2) ?> EUR</strong></div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
