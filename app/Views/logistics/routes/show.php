<?php // Vista de logistica: ayuda a coordinar pedidos, rutas y entregas operativas. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$realDepartures = array_filter(array_column($deliveries, 'departed_at'));
$realArrivals = array_filter(array_column($deliveries, 'delivered_at'));
$realDeparture = $realDepartures ? min($realDepartures) : null;
$realArrival = $realArrivals ? max($realArrivals) : null;
?>
<div class="dashboard-header">
    <div>
        <h1>Ruta <?= esc($route['route_code']) ?></h1>
        <p class="muted">Conductor: <?= esc($route['driver_name']) ?></p>
    </div>
</div>

<section class="summary-card">
    <div class="heading"><h3 class="section-title">Resumen</h3></div>
    <div class="summary-line"><span>Estado</span><strong><span class="pill <?= esc(route_status_class($route['status'])) ?>"><?= esc(status_label($route['status'])) ?></span></strong></div>
    <div class="summary-line"><span>Origen</span><strong><?= esc($route['origin']) ?></strong></div>
    <div class="summary-line"><span>Salida real</span><strong><?= esc(format_order_datetime($realDeparture) ?: '-') ?></strong></div>
    <div class="summary-line"><span>Llegada real</span><strong><?= esc(format_order_datetime($realArrival) ?: '-') ?></strong></div>
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
                    <th>Receptor</th>
                    <th>Observaciones</th>
                    <th>Dirección</th>
                    <th>Estado pedido</th>
                    <th>Entrega</th>
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
                        <td><?= esc($delivery['recipient_name'] ?: '-') ?></td>
                        <td><?= esc($delivery['observations'] ?: '-') ?></td>
                        <td><?= esc($delivery['delivery_address']) ?></td>
                        <td><?= esc(status_label($delivery['order_status'])) ?></td>
                        <td><span class="pill"><?= esc(status_label($delivery['status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
