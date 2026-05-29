<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Pedido #<?= esc($order['id']) ?></h1>
        <p class="muted">Estado actual: <?= esc(status_label($order['status'])) ?></p>
    </div>
    <div class="toolbar">
        <?php if (($order['status'] ?? null) === 'pendiente'): ?>
            <a class="btn btn-primary" href="<?= site_url('client/orders/edit/' . $order['id']) ?>">Modificar pedido</a>
        <?php endif; ?>
        <?php if ($invoice): ?>
            <a class="btn btn-outline" href="<?= site_url('client/invoices/' . $invoice['id']) ?>">Ver factura</a>
            <a class="btn btn-primary" href="<?= site_url('client/invoices/' . $invoice['id'] . '/pdf') ?>">Descargar PDF</a>
        <?php endif; ?>
    </div>
</div>

<div class="grid-2">
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Resumen</h3></div>
        <div class="summary-line"><span>Fecha pedido</span><strong><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></strong></div>
        <div class="summary-line"><span>Estado</span><strong><?= esc(status_label($order['status'])) ?></strong></div>
        <div class="summary-line"><span>Dirección</span><strong><?= esc($order['delivery_address']) ?></strong></div>
        <div class="summary-line"><span>Total</span><strong><?= number_format((float) $order['total'], 2) ?> EUR</strong></div>
        <?php if (! empty($order['notes'])): ?>
            <div style="margin-top:1rem;">
                <strong>Notas</strong>
                <p class="muted" style="margin:.5rem 0 0;"><?= esc($order['notes']) ?></p>
            </div>
        <?php endif; ?>
    </section>

    <section class="table-card">
        <div class="heading"><h3 class="section-title">Productos del pedido</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Producto</th>
                        <th>Unidades</th>
                        <th>Precio unidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="width:84px;">
                                <img src="<?= esc(product_image_url($item['image_path'] ?? null, $item['product_name'])) ?>" alt="<?= esc($item['product_name']) ?>" style="width:56px;height:56px;object-fit:cover;border-radius:14px;border:1px solid rgba(15,23,42,.08);">
                            </td>
                            <td><?= esc($item['product_name']) ?></td>
                            <td><?= esc($item['quantity']) ?></td>
                            <td><?= number_format((float) $item['unit_price'], 2) ?> EUR</td>
                            <td><?= number_format((float) $item['subtotal'], 2) ?> EUR</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
