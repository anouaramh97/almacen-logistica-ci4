<?php // Vista de repartidor: muestra rutas y entregas asignadas al conductor.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid-4">
    <div class="feature-card"><strong>Rutas asignadas</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['assigned_routes']) ?></div></div>
    <div class="feature-card"><strong>Rutas activas</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['active_routes']) ?></div></div>
    <div class="feature-card"><strong>Entregas pendientes</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['pending_deliveries']) ?></div></div>
    <div class="feature-card"><strong>Entregas completadas</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['completed_deliveries']) ?></div></div>
</div>
    <section class="table-card" style="margin-top:1rem;"><div class="heading"><h2>Panel del conductor</h2><p class="section-copy">Consulta tus rutas asignadas y actualiza el estado de las entregas del dia.</p></div><div class="table-wrap"><table><thead><tr><th>Ruta</th><th>Salida</th><th>Pedidos y clientes</th><th>Estado</th><th>Entregas</th></tr></thead><tbody><?php foreach ($todayRoutes as $route): ?><tr><td><a class="btn btn-outline" href="<?= site_url('driver/routes/' . $route['id']) ?>"><?= esc($route['route_code']) ?></a></td><td><?= esc($route['departure_date']) ?></td><td><?php if (! empty($route['order_summaries'])): ?><?php foreach ($route['order_summaries'] as $summary): ?><div><strong>#<?= esc($summary['order_id']) ?></strong> - <?= esc($summary['customer_name']) ?></div><?php endforeach; ?><?php else: ?><span class="muted">Sin pedidos asignados</span><?php endif; ?></td><td><span class="pill <?= esc(route_status_class($route['status'])) ?>"><?= esc(status_label($route['status'])) ?></span></td><td><?= esc($deliveryCounts[$route['id']] ?? 0) ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?= $this->endSection() ?>
