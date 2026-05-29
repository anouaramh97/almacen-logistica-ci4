<?php // Vista de repartidor: muestra rutas y entregas asignadas al conductor. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Mis rutas</h1>
        <p class="muted">Rutas asignadas al conductor con estado, origen, destino y número de entregas.</p>
    </div>
</div>

<section class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Salida</th>
                    <th>Pedidos y clientes</th>
                    <th>Estado</th>
                    <th>Origen</th>
                    <th>Entregas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                    <tr>
                        <td><?= esc($route['route_code']) ?></td>
                        <td><?= esc($route['departure_date']) ?></td>
                        <td>
                            <?php if (! empty($route['order_summaries'])): ?>
                                <?php foreach ($route['order_summaries'] as $summary): ?>
                                    <div><strong>#<?= esc($summary['order_id']) ?></strong> - <?= esc($summary['customer_name']) ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="muted">Sin pedidos asignados</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="pill <?= esc(route_status_class($route['status'])) ?>"><?= esc(status_label($route['status'])) ?></span></td>
                        <td><?= esc($route['origin']) ?></td>
                        <td><?= esc($route['delivery_total']) ?></td>
                        <td><a class="btn btn-outline" href="<?= site_url('driver/routes/' . $route['id']) ?>">Ver</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
