<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$routeStatusOptions = ['planificada', 'en_progreso', 'completada', 'cancelada'];
$selectedRouteStatus = old('status', $route['status'] ?? 'planificada');
?>
<style>
    .route-shell {
        display: grid;
        gap: 1rem;
    }
    .route-topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem 1.35rem;
        border-radius: 24px;
        background: linear-gradient(135deg, #0b1f39 0%, #13406c 56%, #1d5c95 100%);
        color: #fff;
        box-shadow: 0 22px 48px rgba(10, 31, 57, .18);
    }
    .route-topbar h1,
    .route-topbar p {
        margin: 0;
        color: #fff;
    }
    .route-topbar p {
        margin-top: .45rem;
        color: rgba(255,255,255,.8);
        line-height: 1.6;
    }
    .route-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .75rem;
        padding: .42rem .8rem;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.14);
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .route-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .7rem;
        justify-content: flex-end;
    }
    .route-actions .btn {
        min-width: 164px;
    }
    .route-status-form {
        display: grid;
        gap: .75rem;
        margin-top: 1rem;
    }
    .route-status-form select {
        width: 100%;
    }
    .route-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 1.45fr);
        gap: 1rem;
        align-items: start;
    }
    .route-card {
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 24px;
        box-shadow: 0 18px 38px rgba(15,23,42,.06);
        padding: 1.25rem;
    }
    .route-card h3 {
        margin: 0 0 .35rem;
        font-size: 1.05rem;
        color: #1b2430;
    }
    .route-card .section-copy {
        margin-bottom: 1rem;
    }
    .route-summary {
        display: grid;
        gap: .85rem;
    }
    .route-summary-item {
        padding: .95rem 1rem;
        border-radius: 18px;
        background: linear-gradient(180deg, #fbfcfe 0%, #f4f8fc 100%);
        border: 1px solid rgba(15,23,42,.08);
    }
    .route-summary-item span {
        display: block;
        margin-bottom: .28rem;
        font-size: .78rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: #738195;
    }
    .route-summary-item strong {
        display: block;
        font-size: 1rem;
        color: #1c2430;
        line-height: 1.55;
    }
    .route-status {
        display: inline-flex;
        align-items: center;
        padding: .38rem .72rem;
        border-radius: 999px;
        background: rgba(15,98,254,.1);
        color: #0f62fe;
        font-weight: 800;
        font-size: .8rem;
    }
    .route-deliveries {
        display: grid;
        gap: .9rem;
    }
    .route-delivery {
        padding: 1rem;
        border-radius: 20px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%);
    }
    .route-delivery-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: .75rem;
    }
    .route-delivery-head strong {
        display: block;
        color: #1c2430;
        font-size: 1rem;
    }
    .route-delivery-head .muted {
        margin-top: .18rem;
    }
    .route-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    .route-meta-item {
        padding: .8rem .9rem;
        border-radius: 16px;
        background: rgba(15,23,42,.03);
        border: 1px solid rgba(15,23,42,.06);
    }
    .route-meta-item span {
        display: block;
        margin-bottom: .22rem;
        font-size: .75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #738195;
    }
    .route-meta-item strong {
        color: #1d2733;
        line-height: 1.5;
    }
    @media (max-width: 1023.98px) {
        .route-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767.98px) {
        .route-topbar {
            padding: 1rem;
            border-radius: 20px;
        }
        .route-actions {
            width: 100%;
            justify-content: stretch;
        }
        .route-actions .btn {
            width: 100%;
            min-width: 0;
        }
        .route-card {
            padding: 1rem;
            border-radius: 20px;
        }
        .route-delivery-head,
        .route-meta {
            grid-template-columns: 1fr;
            flex-direction: column;
        }
    }
</style>

<div class="route-shell">
    <section class="route-topbar">
        <div>
            <span class="route-kicker">Ruta administrativa</span>
            <h1>Ruta <?= esc($route['route_code']) ?></h1>
            <p>Detalle operativo de la ruta, conductor asignado, estado actual y entregas relacionadas dentro del recorrido.</p>
        </div>
        <div class="route-actions">
            <a href="<?= site_url('admin/routes') ?>" class="btn btn-outline">Volver a rutas</a>
            <a href="<?= site_url('admin/orders') ?>" class="btn btn-primary">Ver pedidos</a>
            <form method="post" action="<?= site_url('admin/routes/delete/' . $route['id']) ?>" onsubmit="return confirm('¿Eliminar esta ruta? Los pedidos asignados dejarán de estar en ruta.');">
                <?= csrf_field() ?>
                <button class="btn btn-outline" type="submit">Eliminar ruta</button>
            </form>
        </div>
    </section>

    <div class="route-grid">
        <section class="route-card">
            <h3>Resumen de la ruta</h3>
            <p class="section-copy">Informacion principal del trayecto y del conductor asignado.</p>

            <div class="route-summary">
                <div class="route-summary-item">
                    <span>Conductor</span>
                    <strong><?= esc($route['driver_name']) ?></strong>
                </div>
                <div class="route-summary-item">
                    <span>Correo del conductor</span>
                    <strong><?= esc($route['driver_email']) ?></strong>
                </div>
                <div class="route-summary-item">
                    <span>Estado</span>
                    <form class="route-status-form" method="post" action="<?= site_url('admin/routes/status/' . $route['id']) ?>">
                        <?= csrf_field() ?>
                        <select name="status" required>
                            <?php foreach ($routeStatusOptions as $status): ?>
                                <option value="<?= esc($status) ?>" <?= $selectedRouteStatus === $status ? 'selected' : '' ?>>
                                    <?= esc(status_label($status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </form>
                </div>
                <div class="route-summary-item">
                    <span>Salida</span>
                    <strong><?= esc($route['departure_date']) ?></strong>
                </div>
                <div class="route-summary-item">
                    <span>Origen</span>
                    <strong><?= esc($route['origin']) ?></strong>
                </div>
                <div class="route-summary-item">
                    <span>Notas</span>
                    <strong><?= esc($route['notes'] ?: 'Sin notas registradas') ?></strong>
                </div>
            </div>
        </section>

        <section class="route-card">
            <h3>Entregas de la ruta</h3>
            <p class="section-copy">Pedidos incluidos dentro de esta ruta y su estado operativo actual.</p>

            <div class="route-deliveries">
                <?php if ($deliveries): ?>
                    <?php foreach ($deliveries as $delivery): ?>
                        <article class="route-delivery">
                            <div class="route-delivery-head">
                                <div>
                                    <strong><?= esc($delivery['customer_name']) ?></strong>
                                    <div class="muted"><?= esc($delivery['delivery_address']) ?></div>
                                </div>
                                <span class="route-status"><?= esc(status_label($delivery['status'])) ?></span>
                            </div>

                            <div class="route-meta">
                                <div class="route-meta-item">
                                    <span>Pedido</span>
                                    <strong>#<?= esc($delivery['order_id']) ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Fecha pedido</span>
                                    <strong><?= esc(format_order_datetime($delivery['order_date'] ?? null)) ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Hora estimada de entrega</span>
                                    <strong><?= esc(format_order_datetime($delivery['estimated_delivery_at'] ?? null) ?: '-') ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Salida real en reparto</span>
                                    <strong><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Entrega real</span>
                                    <strong><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Estado del pedido</span>
                                    <strong><?= esc(status_label($delivery['order_status'])) ?></strong>
                                </div>
                                <div class="route-meta-item">
                                    <span>Estado de entrega</span>
                                    <strong><?= esc(status_label($delivery['status'])) ?></strong>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty">Esta ruta todavia no tiene entregas relacionadas.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<?= $this->endSection() ?>
