<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="summary-card">
    <style>
        .admin-order-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
        }

        .admin-order-more-menu {
            position: relative;
        }

        .admin-order-more-menu summary {
            list-style: none;
        }

        .admin-order-more-menu summary::-webkit-details-marker {
            display: none;
        }

        .admin-order-more-trigger {
            width: 42px;
            height: 42px;
            min-width: 42px;
            padding: 0;
            border-radius: 14px;
            font-size: 1.35rem;
            line-height: 1;
        }

        .admin-order-more-panel {
            position: absolute;
            right: 0;
            top: calc(100% + .55rem);
            z-index: 20;
            min-width: 200px;
            display: grid;
            gap: .45rem;
            padding: .55rem;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 16px;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .admin-order-more-panel form {
            margin: 0;
        }

        .admin-order-more-panel .btn {
            width: 100%;
            justify-content: flex-start;
            border-radius: 12px;
            padding: .72rem .85rem;
        }

        .admin-order-hero {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .admin-order-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            align-items: start;
        }

        .admin-order-block {
            background: rgba(255, 255, 255, .72);
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 22px;
            padding: 1.15rem;
        }

        .admin-order-grid {
            display: grid;
            gap: .85rem;
        }

        .admin-order-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: .85rem 0;
            border-bottom: 1px solid rgba(15, 23, 42, .08);
        }

        .admin-order-line:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .admin-order-line span {
            color: #6e7c90;
            font-weight: 700;
        }

        .admin-order-products {
            display: grid;
            gap: 1rem;
        }

        .admin-order-product {
            display: grid;
            grid-template-columns: 86px minmax(0, 1fr);
            gap: 1rem;
            padding: 1rem;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(248, 251, 255, .95), rgba(255, 255, 255, .98));
        }

        .admin-order-product-photo {
            width: 86px;
            height: 86px;
            object-fit: cover;
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: #fff;
        }

        .admin-order-product-meta {
            display: flex;
            gap: .55rem;
            flex-wrap: wrap;
            margin-top: .75rem;
        }

        @media (max-width: 760px) {
            .admin-order-summary {
                grid-template-columns: 1fr;
            }

            .admin-order-product {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="admin-order-hero">
        <div class="top-intro">
            <h1>Pedido #<?= esc($order['id']) ?></h1>
            <p>Detalle administrativo del pedido con resumen, cliente y productos.</p>
        </div>
        <div class="admin-order-actions">
            <?php if ($invoice): ?>
                <a class="btn btn-outline" href="<?= site_url('admin/invoices/' . $invoice['id']) ?>">Ver factura</a>
            <?php elseif (! $invoice): ?>
                <form method="post" action="<?= site_url('admin/orders/' . $order['id'] . '/invoice') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline">Generar factura</button>
                </form>
            <?php endif; ?>
            <details class="admin-order-more-menu">
                <summary class="btn btn-outline admin-order-more-trigger" aria-label="Mas acciones">...</summary>
                <div class="admin-order-more-panel">
                    <a class="btn btn-outline" href="<?= site_url('admin/orders/edit/' . $order['id']) ?>">Cambiar estado</a>
                    <?php if (($order['status'] ?? null) === 'pendiente'): ?>
                        <form method="post" action="<?= site_url('admin/orders/update/' . $order['id']) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="confirmado">
                            <input type="hidden" name="redirect_to" value="<?= esc(site_url('admin/orders/' . $order['id'])) ?>">
                            <button class="btn btn-primary" type="submit">Confirmar pedido</button>
                        </form>
                    <?php endif; ?>
                    <form method="post" action="<?= site_url('admin/orders/delete/' . $order['id']) ?>" onsubmit="return confirm('¿Eliminar este pedido? Esta accion tambien eliminara su factura y entrega asociada.');">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger" type="submit">Eliminar pedido</button>
                    </form>
                </div>
            </details>
        </div>
    </div>

    <div class="admin-order-summary">
        <section class="admin-order-block">
            <div class="heading"><h3 class="section-title">Resumen</h3></div>
            <div class="admin-order-grid">
                <div class="admin-order-line"><span>Estado</span><strong><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></strong></div>
                <div class="admin-order-line"><span>Fecha pedido</span><strong><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></strong></div>
                <div class="admin-order-line"><span>Direccion</span><strong><?= esc($order['delivery_address']) ?></strong></div>
                <div class="admin-order-line"><span>Total</span><strong><?= number_format((float) $order['total'], 2) ?> EUR</strong></div>
            </div>
        </section>

        <section class="admin-order-block">
            <div class="heading"><h3 class="section-title">Cliente</h3></div>
            <div class="admin-order-grid">
                <div class="admin-order-line"><span>Nombre</span><strong><?= esc($order['customer_name']) ?></strong></div>
                <div class="admin-order-line"><span>Correo</span><strong><?= esc($order['customer_email']) ?></strong></div>
                <?php if (! empty($order['notes'])): ?>
                    <div style="margin-top:.35rem;">
                        <strong>Notas</strong>
                        <p class="muted" style="margin:.45rem 0 0; line-height:1.65;"><?= esc($order['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>

<section class="table-card" style="margin-top:1rem;">
    <div class="heading">
        <h3 class="section-title">Productos del pedido</h3>
        <p class="section-copy">Vista ampliada con foto, unidades y precios de cada producto.</p>
    </div>

    <div class="admin-order-products">
        <?php foreach ($items as $item): ?>
            <article class="admin-order-product">
                <div>
                    <img
                        src="<?= esc(product_image_url($item['image_path'] ?? null, $item['product_name'])) ?>"
                        alt="<?= esc($item['product_name']) ?>"
                        class="admin-order-product-photo"
                    >
                </div>
                <div>
                    <strong style="font-size:1.05rem;"><?= esc($item['product_name']) ?></strong>
                    <div class="admin-order-product-meta">
                        <span class="pill"><?= esc($item['quantity']) ?> uds</span>
                        <span class="pill"><?= number_format((float) $item['unit_price'], 2) ?> EUR/u</span>
                        <span class="pill is-success-soft"><?= number_format((float) $item['subtotal'], 2) ?> EUR</span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="toolbar" style="margin-top:1.25rem; justify-content:flex-start;">
        <a class="btn btn-outline" href="<?= site_url('admin/orders') ?>">Volver a pedidos</a>
    </div>
</section>
<?= $this->endSection() ?>
