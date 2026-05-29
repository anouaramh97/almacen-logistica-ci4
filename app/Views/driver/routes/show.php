<?php // Vista de repartidor: muestra rutas y entregas asignadas al conductor. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Ruta <?= esc($route['route_code']) ?></h1>
        <p class="muted">Entrega operativa del conductor con accesos directos a cada entrega asignada.</p>
    </div>
</div>

<section class="summary-card">
    <div class="heading"><h3 class="section-title">Resumen</h3></div>
    <div class="summary-line"><span>Estado</span><strong><span class="pill <?= esc(route_status_class($route['status'])) ?>"><?= esc(status_label($route['status'])) ?></span></strong></div>
    <div class="summary-line"><span>Salida</span><strong><?= esc($route['departure_date']) ?></strong></div>
    <div class="summary-line"><span>Origen</span><strong><?= esc($route['origin']) ?></strong></div>
    <div class="summary-line"><span>Entregas</span><strong><?= count($deliveries) ?></strong></div>
</section>

<section class="table-card" style="margin-top:1rem;">
    <div class="heading"><h3 class="section-title">Entregas</h3></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Pedido</th>
                    <th>Fecha pedido</th>
                    <th>Salida real</th>
                    <th>Hora estimada de entrega</th>
                    <th>Entrega real</th>
                    <th>Dirección</th>
                    <th>Estado pedido</th>
                    <th>Entrega</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deliveries as $delivery): ?>
                    <tr>
                        <td><?= esc($delivery['customer_name']) ?></td>
                        <td><strong>#<?= esc($delivery['order_id']) ?></strong></td>
                        <td><?= esc(format_order_datetime($delivery['order_date'] ?? null)) ?></td>
                        <td><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></td>
                        <td><?= esc(format_order_datetime($delivery['estimated_delivery_at'] ?? null) ?: '-') ?></td>
                        <td><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></td>
                        <td><?= esc($delivery['delivery_address']) ?></td>
                        <td><span class="pill <?= esc(order_status_class($delivery['order_status'])) ?>"><?= esc(status_label($delivery['order_status'])) ?></span></td>
                        <td><span class="pill <?= esc(delivery_status_class($delivery['status'])) ?>"><?= esc(status_label($delivery['status'])) ?></span></td>
                        <td><a class="btn btn-outline" href="<?= site_url('driver/deliveries/' . $delivery['id']) ?>">Abrir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
