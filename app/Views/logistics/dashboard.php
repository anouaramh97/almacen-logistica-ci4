<?php // Vista de logistica: ayuda a coordinar pedidos, rutas y entregas operativas.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid-4">
    <div class="feature-card"><strong>Conductores activos</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['available_drivers']) ?></div></div>
    <div class="feature-card"><strong>Rutas planificadas</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['planned_routes']) ?></div></div>
    <div class="feature-card"><strong>Entregas en transito</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['in_transit_deliveries']) ?></div></div>
    <div class="feature-card"><strong>Pedidos listos</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['ready_orders']) ?></div></div>
</div>
<div class="dashboard-grid" style="margin-top:1rem;">
    <section class="table-card"><div class="heading"><h2>Centro logistico</h2><p class="section-copy">Coordina conductores, organiza rutas y transforma pedidos preparados en entregas reales.</p></div><div class="table-wrap"><table><thead><tr><th>Codigo</th><th>Conductor</th><th>Salida</th><th>Pedidos y clientes</th><th>Estado</th></tr></thead><tbody><?php foreach ($recentRoutes as $route): ?><tr><td><?= esc($route['route_code']) ?></td><td><?= esc($route['driver_name'] ?: 'Sin asignar') ?></td><td><?= esc($route['departure_date']) ?></td><td><?php if (! empty($route['order_summaries'])): ?><?php foreach ($route['order_summaries'] as $summary): ?><div><strong>#<?= esc($summary['order_id']) ?></strong> - <?= esc($summary['customer_name']) ?></div><?php endforeach; ?><?php else: ?><span class="muted">Sin pedidos asignados</span><?php endif; ?></td><td><span class="pill <?= esc(route_status_class($route['status'])) ?>"><?= esc(status_label($route['status'])) ?></span></td></tr><?php endforeach; ?></tbody></table></div></section>
    <section class="summary-card"><div class="heading"><h3 class="section-title">Pedidos listos para salida</h3></div><?php if ($pendingOrders): foreach ($pendingOrders as $order): ?><div class="feature-card" style="margin-bottom:0.85rem;"><strong>Pedido #<?= esc($order['id']) ?></strong><p><?= esc($order['customer_name'] ?: 'Sin cliente') ?> | Fecha: <?= esc(format_order_datetime($order['order_date'] ?? null)) ?> | Estado: <?= esc(status_label($order['status'])) ?></p></div><?php endforeach; else: ?><div class="empty">No hay pedidos listos en este momento.</div><?php endif; ?></section>
</div>
<?= $this->endSection() ?>
