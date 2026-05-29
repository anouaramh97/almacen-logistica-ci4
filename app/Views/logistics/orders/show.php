<?php // Vista de logistica: ayuda a coordinar pedidos, rutas y entregas operativas. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Pedido #<?= esc($order['id']) ?></h1>
        <p class="muted">Detalle operativo del pedido y de los productos que debe gestionar logística.</p>
    </div>
    <?php if (! empty($order['invoice_id'])): ?>
        <a class="btn btn-outline" href="<?= site_url('admin/invoices/' . $order['invoice_id']) ?>">Ver factura</a>
    <?php endif; ?>
</div>

<div class="grid-2">
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Resumen</h3></div>
        <div class="summary-line"><span>Cliente</span><strong><?= esc($order['customer_name']) ?></strong></div>
        <div class="summary-line"><span>Correo</span><strong><?= esc($order['customer_email']) ?></strong></div>
        <div class="summary-line"><span>Fecha pedido</span><strong><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></strong></div>
        <div class="summary-line"><span>Estado</span><strong><?= esc(status_label($order['status'])) ?></strong></div>
        <div class="summary-line"><span>Dirección</span><strong><?= esc($order['delivery_address']) ?></strong></div>
        <div class="summary-line"><span>Total</span><strong><?= number_format((float) $order['total'], 2) ?> EUR</strong></div>
    </section>

    <section class="table-card">
        <div class="heading"><h3 class="section-title">Items</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
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
