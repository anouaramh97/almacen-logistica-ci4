<?php // Vista de administracion: presenta datos y acciones internas del panel administrador.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$featured = [];
foreach (array_merge($attentionStockItems ?? [], $outOfStockItems ?? []) as $item) {
    $featured[$item['product_id']] = $item;
}
$featuredStockItems = array_slice(array_values($featured), 0, 4);
?>
<style>
    .dashboard-shell {
        display: grid;
        gap: 1rem;
    }
    .dash-hero {
        position: relative;
        overflow: hidden;
        padding: 1.85rem;
        border-radius: 28px;
        background:
            radial-gradient(circle at top right, rgba(106, 227, 255, .18), transparent 24%),
            linear-gradient(135deg, #081b33 0%, #103760 54%, #1c588f 100%);
        color: #fff;
        box-shadow: 0 22px 48px rgba(8, 27, 51, .18);
    }
    .dash-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -100px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }
    .dash-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .95rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.14);
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .dash-hero h2,
    .dash-hero p {
        position: relative;
        z-index: 1;
        margin: 0;
        color: #fff;
    }
    .dash-hero p {
        max-width: 720px;
        margin-top: .75rem;
        color: rgba(255,255,255,.82);
        line-height: 1.7;
    }
    .dash-stats {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .85rem;
        margin-top: 1.4rem;
    }
    .dash-stat {
        padding: 1.1rem 1.15rem;
        border-radius: 20px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.08);
    }
    .dash-stat span {
        display: block;
        margin-bottom: .45rem;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: rgba(255,255,255,.74);
    }
    .dash-stat strong {
        display: block;
        font-size: 2rem;
        line-height: 1;
        color: #fff;
    }
    .dash-card {
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 24px;
        box-shadow: 0 18px 38px rgba(15,23,42,.06);
        padding: 1.35rem;
    }
    .dash-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.15rem;
    }
    .dash-head h3 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #1b2430;
    }
    .dash-head p {
        margin: .3rem 0 0;
        color: #738195;
        font-size: .9rem;
        line-height: 1.6;
    }
    .dash-activity {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }
    .dash-activity-card {
        min-height: 168px;
        padding: 1.15rem;
        border-radius: 20px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #fbfcfe 0%, #f3f7fb 100%);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    a.dash-activity-card {
        color: inherit;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    a.dash-activity-card:hover {
        transform: translateY(-2px);
        border-color: rgba(15,98,254,.18);
        box-shadow: 0 18px 34px rgba(15,23,42,.08);
    }
    .dash-activity-card strong {
        display: block;
        margin-bottom: .45rem;
        font-size: 2rem;
        line-height: 1;
    }
    .dash-activity-card span {
        display: block;
        margin-bottom: .35rem;
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #6f7c8d;
    }
    .dash-activity-card p {
        margin: 0;
        font-size: .88rem;
        line-height: 1.55;
        color: #6f7c8d;
    }
    .dash-activity-card.blue strong { color: #2886d8; }
    .dash-activity-card.red strong { color: #d65e69; }
    .dash-activity-card.green strong { color: #2ea27f; }
    .dash-activity-card.gold strong { color: #b8891d; }
    .dash-activity-card.purple strong { color: #7c3aed; }
    .dash-main {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(320px, .9fr);
        gap: 1rem;
        align-items: start;
    }
    .dash-column {
        display: grid;
        gap: 1rem;
    }
    .dash-alerts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .9rem;
    }
    .dash-alert {
        padding: 1.1rem;
        border-radius: 20px;
        border: 1px solid transparent;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.18);
    }
    .dash-alert h4 {
        margin: 0 0 .35rem;
        font-size: 1rem;
        font-weight: 800;
    }
    .dash-alert p {
        margin: 0 0 .9rem;
        font-size: .9rem;
        line-height: 1.55;
    }
    .dash-alert.warn {
        background: linear-gradient(180deg, #fffaf0 0%, #fff1c9 100%);
        border-color: rgba(186,132,18,.14);
        color: #7a5100;
    }
    .dash-alert.danger {
        background: linear-gradient(180deg, #fff2f3 0%, #ffd9de 100%);
        border-color: rgba(177,34,44,.12);
        color: #861820;
    }
    .dash-alert-list {
        display: grid;
        gap: .75rem;
    }
    .dash-alert-item {
        padding: .95rem 1rem;
        border-radius: 14px;
        background: rgba(255,255,255,.72);
        border: 1px solid rgba(255,255,255,.4);
    }
    .dash-alert-item strong {
        display: block;
        font-size: .94rem;
    }
    .dash-alert-item span {
        display: block;
        margin-top: .18rem;
        font-size: .84rem;
        line-height: 1.5;
        opacity: .9;
    }
    .dash-pending-users {
        display: grid;
        gap: .85rem;
    }
    .dash-pending-user {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.05rem;
        border-radius: 16px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #fbfcfe 0%, #f5f8fc 100%);
    }
    .dash-pending-user strong,
    .dash-pending-user span {
        display: block;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    .dash-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .dash-summary-list {
        display: grid;
        gap: .9rem;
    }
    .dash-summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.05rem;
        border-radius: 16px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #fbfcfe 0%, #f5f8fc 100%);
    }
    .dash-summary-item span {
        color: #6f7c8d;
        font-size: .82rem;
        font-weight: 800;
        letter-spacing: .05em;
        text-transform: uppercase;
    }
    .dash-summary-item strong {
        font-size: 1.6rem;
        line-height: 1;
        color: #1c2430;
    }
    .dash-donut {
        display: grid;
        justify-items: center;
        gap: .8rem;
        min-height: 100%;
        align-content: center;
    }
    .dash-donut-stage {
        position: relative;
        display: grid;
        place-items: center;
    }
    .dash-donut-ring {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: conic-gradient(#33c39a 0 <?= max(8, min(100, $stats['completion_rate'])) ?>%, #e8f0f4 <?= max(8, min(100, $stats['completion_rate'])) ?>% 100%);
    }
    .dash-donut-ring::after {
        content: "";
        width: 104px;
        height: 104px;
        border-radius: 50%;
        background: #fff;
        box-shadow: inset 0 0 0 1px rgba(15,23,42,.06);
    }
    .dash-donut-value {
        position: absolute;
        font-size: 1.8rem;
        font-weight: 800;
        color: #2d7c69;
    }
    .dash-products {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .85rem;
    }
    .dash-product {
        display: block;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #ffffff 0%, #f6f9fd 100%);
        color: inherit;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .dash-product:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 30px rgba(15,23,42,.08);
        border-color: rgba(15,98,254,.22);
    }
    .dash-product img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 14px;
        margin-bottom: .85rem;
    }
    .dash-product strong {
        display: block;
        font-size: .92rem;
        color: #1b2430;
    }
    .dash-product span {
        display: block;
        margin-top: .22rem;
        color: #6f7c8d;
        font-size: .85rem;
    }
    .dash-orders td {
        padding: .8rem 0;
        border-top: 1px solid rgba(15,23,42,.08);
        vertical-align: middle;
        color: #1d2733;
    }
    .dash-orders th {
        padding-bottom: .75rem;
    }
    .status-chip {
        display: inline-flex;
        align-items: center;
        padding: .35rem .7rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
    }
    .status-pending { background: rgba(255,177,66,.18); color: #925200; }
    .status-confirmed, .status-delivered, .status-completed { background: rgba(54,194,150,.16); color: #14785d; }
    .status-preparing, .status-planned, .status-in_progress { background: rgba(15,98,254,.12); color: #0f62fe; }
    .status-cancelled, .status-failed { background: rgba(215,76,88,.14); color: #a41e2b; }
    .dash-actions {
        display: grid;
        gap: .75rem;
    }
    .dash-actions a {
        display: block;
        padding: 1rem 1.05rem;
        border-radius: 16px;
        border: 1px solid rgba(15,23,42,.08);
        background: linear-gradient(180deg, #ffffff 0%, #f6f9fd 100%);
        color: #1c2430;
        font-weight: 700;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .dash-actions a:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(15,23,42,.08);
        border-color: rgba(15,98,254,.16);
    }
    .dash-actions a span {
        display: block;
        margin-top: .25rem;
        color: #738195;
        font-size: .86rem;
        font-weight: 500;
        line-height: 1.5;
    }
    .dash-section-shell,
    .dash-side-shell {
        display: grid;
        gap: 1rem;
        padding: 0;
        background: transparent;
        border: 0;
    }
    .dash-card--soft {
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(4px);
    }
    .dash-card--accent .dash-head h3 {
        color: #15385d;
    }
    .dash-card--accent {
        background: linear-gradient(180deg, #f7fbff 0%, #eef5fc 100%);
        border-color: rgba(15,98,254,.08);
    }
    .dash-card--orders {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    @media (max-width: 1199.98px) {
        .dash-stats,
        .dash-activity {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .dash-main,
        .dash-alerts,
        .dash-grid-2 {
            grid-template-columns: 1fr;
        }
        .dash-activity-card {
            min-height: 150px;
        }
        .dash-section-shell,
        .dash-side-shell {
            gap: 1rem;
        }
    }
    @media (max-width: 767.98px) {
        .dash-stats,
        .dash-activity,
        .dash-products {
            grid-template-columns: 1fr;
        }
        .dash-hero,
        .dash-card {
            padding: 1rem;
            border-radius: 20px;
        }
        .dash-activity-card,
        .dash-product {
            min-height: 0;
        }
        .dash-head,
        .dash-summary-item {
            flex-direction: column;
            align-items: flex-start;
        }
        .dash-summary-item strong {
            font-size: 1.35rem;
        }
        .dash-donut-ring {
            width: 136px;
            height: 136px;
        }
        .dash-donut-ring::after {
            width: 94px;
            height: 94px;
        }
    }
</style>

<div class="dashboard-shell">
    <section class="dash-hero">
        <span class="dash-kicker"><?= esc(lang('App.admin_panel')) ?></span>
        <h2><?= esc(lang('App.admin_dashboard_title')) ?></h2>
        <p><?= esc(lang('App.admin_dashboard_copy')) ?></p>

        <div class="dash-stats">
            <article class="dash-stat">
                <span><?= esc(lang('App.products')) ?></span>
                <strong><?= esc((string) $stats['products']) ?></strong>
            </article>
            <article class="dash-stat">
                <span><?= esc(lang('App.available')) ?></span>
                <strong><?= esc((string) $stats['quantity_in_hand']) ?></strong>
            </article>
            <article class="dash-stat">
                <span><?= esc(lang('App.pending_orders')) ?></span>
                <strong><?= esc((string) $stats['pending_orders']) ?></strong>
            </article>
            <article class="dash-stat">
                <span><?= esc(lang('App.pending_invoices')) ?></span>
                <strong><?= esc((string) $stats['pending_invoices']) ?></strong>
            </article>
        </div>
    </section>

    <section class="dash-card">
        <div class="dash-head">
            <div>
                <h3><?= esc(lang('App.operational_activity')) ?></h3>
                <p><?= esc(lang('App.operational_activity_copy')) ?></p>
            </div>
        </div>
        <div class="dash-activity">
            <a href="<?= site_url('admin/orders') ?>" class="dash-activity-card blue">
                <span><?= esc(lang('App.pending_orders')) ?></span>
                <strong><?= esc((string) $stats['pending_orders']) ?></strong>
                <p><?= esc(lang('App.review_orders_copy')) ?></p>
            </a>
            <a href="<?= site_url('admin/stocks') ?>" class="dash-activity-card red">
                <span><?= esc(lang('App.needs_attention')) ?></span>
                <strong><?= esc((string) $stats['low_stock']) ?></strong>
                <p><?= esc(lang('App.attention_copy')) ?></p>
            </a>
            <a href="<?= site_url('admin/orders') ?>" class="dash-activity-card green">
                <span><?= esc(lang('App.delivered_orders')) ?></span>
                <strong><?= esc((string) $stats['delivered_orders']) ?></strong>
                <p><?= esc(lang('App.current_performance')) ?></p>
            </a>
            <a href="<?= site_url('admin/invoices') ?>" class="dash-activity-card gold">
                <span><?= esc(lang('App.invoices_label')) ?></span>
                <strong><?= esc((string) $stats['pending_invoices']) ?></strong>
                <p><?= esc(lang('App.view_invoices_copy')) ?></p>
            </a>
            <a href="<?= site_url('admin/users') ?>" class="dash-activity-card purple">
                <span>Usuarios pendientes</span>
                <strong><?= esc((string) $stats['pending_users']) ?></strong>
                <p>Clientes registrados esperando activacion del administrador.</p>
            </a>
        </div>
    </section>

    <div class="dash-main">
        <div class="dash-column dash-section-shell">
            <section class="dash-card dash-card--accent">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.needs_attention')) ?></h3>
                        <p><?= esc(lang('App.attention_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-alerts">
                    <article class="dash-alert warn">
                        <h4><?= esc(lang('App.needs_attention')) ?></h4>
                        <p><?= esc(lang('App.attention_copy')) ?></p>
                        <div class="dash-alert-list">
                            <?php if ($attentionStockItems): ?>
                                <?php foreach ($attentionStockItems as $item): ?>
                                    <div class="dash-alert-item">
                                        <strong><?= esc($item['product_name']) ?></strong>
                                        <span><?= esc($item['warehouse_name']) ?> | <?= esc($item['quantity']) ?> <?= esc(lang('App.available')) ?> | <?= esc(lang('App.minimum')) ?> <?= esc($item['minimum_quantity']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dash-alert-item">
                                    <strong><?= esc(lang('App.no_alerts')) ?></strong>
                                    <span><?= esc(lang('App.no_low_stock')) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>

                    <article class="dash-alert danger">
                        <h4><?= esc(lang('App.out_of_stock_title')) ?></h4>
                        <p><?= esc(lang('App.out_of_stock_copy')) ?></p>
                        <div class="dash-alert-list">
                            <?php if ($outOfStockItems): ?>
                                <?php foreach ($outOfStockItems as $item): ?>
                                    <div class="dash-alert-item">
                                        <strong><?= esc($item['product_name']) ?></strong>
                                        <span><?= esc($item['warehouse_name']) ?> | <?= esc(lang('App.total_break')) ?> | <?= esc(lang('App.minimum')) ?> <?= esc($item['minimum_quantity']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dash-alert-item">
                                    <strong><?= esc(lang('App.no_breaks')) ?></strong>
                                    <span><?= esc(lang('App.no_out_of_stock')) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
            </section>

            <section class="dash-card dash-card--soft">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.top_products')) ?></h3>
                        <p><?= esc(lang('App.top_products_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-products">
                    <?php if ($featuredStockItems): ?>
                        <?php foreach ($featuredStockItems as $item): ?>
                            <a href="<?= site_url('admin/stocks/edit/' . $item['id']) ?>" class="dash-product">
                                <img src="<?= esc(product_image_url($item['image_path'] ?? null, $item['product_name'])) ?>" alt="<?= esc($item['product_name']) ?>">
                                <strong><?= esc($item['product_name']) ?></strong>
                                <span><?= esc($item['category_name'] ?: lang('App.no_category')) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dash-product">
                            <strong><?= esc(lang('App.no_featured_products')) ?></strong>
                            <span><?= esc(lang('App.no_featured_products_copy')) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="dash-card dash-card--orders">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.recent_orders')) ?></h3>
                        <p><?= esc(lang('App.recent_orders_copy')) ?></p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="dash-orders">
                        <thead>
                            <tr>
                                <th><?= esc(lang('App.customer')) ?></th>
                                <th>Fecha pedido</th>
                                <th><?= esc(lang('App.status')) ?></th>
                                <th><?= esc(lang('App.total')) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentOrders): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?= esc($order['customer_name'] ?: lang('App.no_customer')) ?></td>
                                        <td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td>
                                        <td><span class="status-chip status-<?= esc($order['status']) ?>"><?= esc(lang('App.' . $order['status'])) ?></span></td>
                                        <td><?= number_format((float) $order['total'], 2, ',', '.') ?> EUR</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="muted"><?= esc(lang('App.no_recent_orders')) ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="dash-card dash-card--soft">
                <div class="dash-head">
                    <div>
                        <h3>Usuarios pendientes de activar</h3>
                        <p>Cuando actives un usuario desde aquí desaparecerá automáticamente de este listado.</p>
                    </div>
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-outline">Ver usuarios</a>
                </div>
                <div class="dash-pending-users">
                    <?php if ($pendingUsers): ?>
                        <?php foreach ($pendingUsers as $pendingUser): ?>
                            <div class="dash-pending-user">
                                <div>
                                    <strong><?= esc($pendingUser['name']) ?></strong>
                                    <span class="muted"><?= esc($pendingUser['email']) ?></span>
                                    <span class="muted">Alta: <?= esc($pendingUser['created_at'] ?: 'Sin fecha') ?></span>
                                </div>
                                <form method="post" action="<?= site_url('admin/users/activate/' . $pendingUser['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-primary" type="submit">Activar usuario</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dash-pending-user">
                            <div>
                                <strong>No hay usuarios pendientes</strong>
                                <span class="muted">Los nuevos clientes pendientes aparecerán aquí hasta que los actives.</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="dash-column dash-side-shell">
            <section class="dash-card dash-card--soft">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.inventory_summary')) ?></h3>
                        <p><?= esc(lang('App.inventory_summary_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-summary-list">
                    <div class="dash-summary-item">
                        <span><?= esc(lang('App.available_products')) ?></span>
                        <strong><?= esc((string) $stats['quantity_in_hand']) ?></strong>
                    </div>
                    <div class="dash-summary-item">
                        <span><?= esc(lang('App.low_stock')) ?></span>
                        <strong><?= esc((string) $stats['low_stock']) ?></strong>
                    </div>
                    <div class="dash-summary-item">
                        <span><?= esc(lang('App.no_stock')) ?></span>
                        <strong><?= esc((string) $stats['out_of_stock']) ?></strong>
                    </div>
                </div>
            </section>

            <section class="dash-card dash-card--accent">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.performance')) ?></h3>
                        <p><?= esc(lang('App.performance_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-donut">
                    <div class="dash-donut-stage">
                        <div class="dash-donut-ring"></div>
                        <div class="dash-donut-value"><?= esc((string) $stats['completion_rate']) ?>%</div>
                    </div>
                    <div class="muted"><?= esc(lang('App.current_performance')) ?></div>
                </div>
            </section>

            <section class="dash-card dash-card--soft">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.route_status')) ?></h3>
                        <p><?= esc(lang('App.route_status_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-summary-list">
                    <?php foreach ($routeStatus as $status => $count): ?>
                        <div class="dash-summary-item">
                            <span><?= esc(lang('App.' . $status)) ?></span>
                            <strong><?= esc((string) $count) ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="dash-card dash-card--soft">
                <div class="dash-head">
                    <div>
                        <h3><?= esc(lang('App.quick_actions')) ?></h3>
                        <p><?= esc(lang('App.quick_actions_copy')) ?></p>
                    </div>
                </div>
                <div class="dash-actions">
                    <a href="<?= site_url('admin/products') ?>"><?= esc(lang('App.manage_products')) ?><span><?= esc(lang('App.manage_products_copy')) ?></span></a>
                    <a href="<?= site_url('admin/orders') ?>"><?= esc(lang('App.review_orders')) ?><span><?= esc(lang('App.review_orders_copy')) ?></span></a>
                    <a href="<?= site_url('admin/routes') ?>"><?= esc(lang('App.control_routes')) ?><span><?= esc(lang('App.control_routes_copy')) ?></span></a>
                    <a href="<?= site_url('admin/invoices') ?>"><?= esc(lang('App.view_invoices')) ?><span><?= esc(lang('App.view_invoices_copy')) ?></span></a>
                </div>
            </section>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
