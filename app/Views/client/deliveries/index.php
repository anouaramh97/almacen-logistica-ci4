<?php // Vista de cliente: muestra el seguimiento de entregas del usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-header">
    <div>
        <h1>Mis entregas</h1>
        <p class="muted">Seguimiento de tus pedidos asignados a ruta, con conductor y estado actual.</p>
    </div>
</div>

<section class="table-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Ruta</th>
                    <th>Conductor</th>
                    <th>Estado pedido</th>
                    <th>Estado entrega</th>
                    <th>Salida real</th>
                    <th>Entrega real</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($deliveries): ?>
                    <?php foreach ($deliveries as $delivery): ?>
                        <tr>
                            <td><strong>#<?= esc($delivery['order_id']) ?></strong></td>
                            <td><?= esc($delivery['route_code'] ?? '-') ?></td>
                            <td><?= esc($delivery['driver_name'] ?: '-') ?></td>
                            <td><span class="pill <?= esc(order_status_class($delivery['order_status'])) ?>"><?= esc(status_label($delivery['order_status'])) ?></span></td>
                            <td><span class="pill <?= esc(delivery_status_class($delivery['status'])) ?>"><?= esc(status_label($delivery['status'])) ?></span></td>
                            <td><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></td>
                            <td><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></td>
                            <td><a class="btn btn-primary" href="<?= site_url('client/deliveries/' . $delivery['id']) ?>">Seguir</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="muted">Todavia no tienes entregas asignadas a una ruta.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
