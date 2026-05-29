<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Mis pedidos</h1>
        <p class="muted">Historial completo de pedidos, con acceso directo a la factura cuando ya existe.</p>
    </div>
    <a class="btn btn-primary" href="<?= site_url('client/orders/create') ?>">Nuevo pedido</a>
</div>

<section class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha pedido</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Factura</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= esc($order['id']) ?></td>
                            <td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td>
                            <td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td>
                            <td><?= number_format((float) $order['total'], 2) ?> EUR</td>
                            <td>
                                <?php if (! empty($order['invoice_id'])): ?>
                                    <div class="toolbar">
                                        <a class="btn btn-outline" href="<?= site_url('client/invoices/' . $order['invoice_id']) ?>">Ver factura</a>
                                        <a class="btn btn-primary" href="<?= site_url('client/invoices/' . $order['invoice_id'] . '/pdf') ?>">PDF</a>
                                    </div>
                                <?php else: ?>
                                    <span class="muted">Sin factura</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="toolbar">
                                    <a class="btn btn-outline" href="<?= site_url('client/orders/' . $order['id']) ?>">Ver detalle</a>
                                    <?php if (! empty($order['delivery_id'])): ?>
                                        <a class="btn btn-primary" href="<?= site_url('client/deliveries/' . $order['delivery_id']) ?>">Seguir</a>
                                    <?php endif; ?>
                                    <?php if (($order['status'] ?? null) === 'pendiente'): ?>
                                        <a class="btn btn-primary" href="<?= site_url('client/orders/edit/' . $order['id']) ?>">Modificar</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="muted">No tienes pedidos registrados todavía.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
