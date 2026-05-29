<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $routeStatusOptions = ['planificada', 'en_progreso', 'completada', 'cancelada']; ?>
<section class="table-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h1>Rutas</h1>
            <p>Planificacion y seguimiento de rutas de reparto.</p>
        </div>
        <a href="<?= site_url('admin/routes/create') ?>" class="btn btn-primary">Nueva ruta</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Codigo</th><th>Conductor</th><th>Salida</th><th>Pedidos y clientes</th><th>Estado</th><th>Origen</th><th>Accion</th></tr></thead>
            <tbody>
            <?php foreach ($routes as $route): ?>
                <tr>
                    <td><strong><?= esc($route['route_code']) ?></strong></td>
                    <td><?= esc($route['driver_name']) ?></td>
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
                    <td>
                        <form method="post" action="<?= site_url('admin/routes/status/' . $route['id']) ?>" class="toolbar">
                            <?= csrf_field() ?>
                            <select name="status" required>
                                <?php foreach ($routeStatusOptions as $status): ?>
                                    <option value="<?= esc($status) ?>" <?= ($route['status'] ?? '') === $status ? 'selected' : '' ?>>
                                        <?= esc(status_label($status)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-outline" type="submit">Guardar</button>
                        </form>
                    </td>
                    <td><?= esc($route['origin']) ?></td>
                    <td>
                        <div class="toolbar">
                            <a href="<?= site_url('admin/routes/' . $route['id']) ?>" class="btn btn-outline">Ver</a>
                            <form method="post" action="<?= site_url('admin/routes/delete/' . $route['id']) ?>" onsubmit="return confirm('¿Eliminar esta ruta? Los pedidos asignados dejarán de estar en ruta.');">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline" type="submit">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
