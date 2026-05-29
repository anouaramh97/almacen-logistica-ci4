<?php // Vista de cliente: muestra el seguimiento detallado de una entrega. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Seguimiento pedido #<?= esc($delivery['order_id']) ?></h1>
        <p class="muted">Ruta <?= esc($delivery['route_code'] ?? '-') ?> con estado <?= esc(status_label($delivery['status'])) ?>.</p>
    </div>
    <a class="btn btn-outline" href="<?= site_url('client/deliveries') ?>">Volver a mis entregas</a>
</div>

<div class="grid-2">
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Estado de la entrega</h3></div>
        <div class="summary-line"><span>Estado pedido</span><strong><span class="pill <?= esc(order_status_class($delivery['order_status'])) ?>"><?= esc(status_label($delivery['order_status'])) ?></span></strong></div>
        <div class="summary-line"><span>Estado entrega</span><strong><span class="pill <?= esc(delivery_status_class($delivery['status'])) ?>"><?= esc(status_label($delivery['status'])) ?></span></strong></div>
        <div class="summary-line"><span>Salida estimada</span><strong><?= esc(format_order_datetime($delivery['departure_date'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Salida real</span><strong><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Entrega estimada</span><strong><?= esc(format_order_datetime($delivery['estimated_delivery_at'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Entrega real</span><strong><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></strong></div>
    </section>

    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Conductor y datos finales</h3></div>
        <div class="summary-line"><span>Conductor</span><strong><?= esc($delivery['driver_name'] ?: '-') ?></strong></div>
        <div class="summary-line"><span>Teléfono</span><strong><?= esc($delivery['driver_phone'] ?: '-') ?></strong></div>
        <div class="summary-line"><span>Origen</span><strong><?= esc($delivery['origin'] ?: '-') ?></strong></div>
        <div class="summary-line"><span>Destino</span><strong><?= esc($delivery['delivery_address'] ?: '-') ?></strong></div>
        <div class="summary-line"><span>Receptor</span><strong><?= esc($delivery['recipient_name'] ?: '-') ?></strong></div>
        <div style="margin-top:1rem;">
            <strong>Observaciones</strong>
            <p class="muted" style="margin:.5rem 0 0;line-height:1.7;"><?= esc($delivery['observations'] ?: 'Sin observaciones registradas.') ?></p>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
