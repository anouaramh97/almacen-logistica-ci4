<?php // Vista de logistica: ayuda a coordinar pedidos, rutas y entregas operativas. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Pedidos logísticos</h1>
        <p class="muted">Vista operativa para logística con acceso rápido al estado, total y documento de factura.</p>
    </div>
</div>

<section class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha pedido</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Factura</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= esc($order['id']) ?></td>
                        <td><?= esc($order['customer_name']) ?></td>
                        <td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td>
                        <td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td>
                        <td><?= number_format((float) $order['total'], 2) ?> EUR</td>
                        <td><?= ! empty($order['invoice_id']) ? 'Disponible' : 'Pendiente' ?></td>
                        <td><a class="btn btn-outline" href="<?= site_url('logistics/orders/' . $order['id']) ?>">Ver detalle</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
