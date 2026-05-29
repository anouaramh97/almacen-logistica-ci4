<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="table-card">
    <style>
        .orders-actions-cell {
            width: 1%;
            white-space: nowrap;
            text-align: center;
        }

        .orders-actions-bar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .65rem;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .orders-actions-bar form {
            margin: 0;
        }

        .orders-more-menu {
            position: relative;
        }

        .orders-more-menu summary {
            list-style: none;
        }

        .orders-more-menu summary::-webkit-details-marker {
            display: none;
        }

        .orders-more-trigger {
            width: 42px;
            height: 42px;
            min-width: 42px;
            padding: 0;
            border-radius: 14px;
            font-size: 1.35rem;
            line-height: 1;
        }

        .orders-more-panel {
            position: absolute;
            right: 0;
            top: calc(100% + .55rem);
            z-index: 20;
            min-width: 190px;
            display: grid;
            gap: .45rem;
            padding: .55rem;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 16px;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .orders-more-panel .btn {
            width: 100%;
            justify-content: flex-start;
            border-radius: 12px;
            padding: .72rem .85rem;
        }

        .orders-actions-head {
            text-align: center;
        }

        .orders-table-wrap {
            overflow: visible;
        }
    </style>

    <div class="heading"><h1>Gestion de pedidos</h1><p class="section-copy">Vista administrativa con cliente, productos, fecha, estado y total.</p></div>
    <div class="table-wrap orders-table-wrap">
        <table>
            <thead><tr><th>ID</th><th>Cliente</th><th>Productos</th><th>Fecha pedido</th><th>Estado</th><th>Total</th><th class="orders-actions-head">Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= esc($order['id']) ?></td>
                    <td><strong><?= esc($order['customer_name']) ?></strong></td>
                    <td>
                        <?php foreach (($itemsByOrder[$order['id']] ?? []) as $item): ?>
                            <div style="padding:0.4rem 0; border-bottom:1px solid rgba(15,23,42,0.06);">
                                <strong><?= esc($item['product_name']) ?></strong>
                                <div class="muted"><?= esc($item['quantity']) ?> uds | <?= number_format((float) $item['subtotal'], 2) ?> EUR</div>
                            </div>
                        <?php endforeach; ?>
                    </td>
                    <td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td>
                    <td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td>
                    <td><?= number_format((float) $order['total'], 2) ?> EUR</td>
                    <td class="orders-actions-cell">
                        <div class="orders-actions-bar">
                            <?php if (($order['status'] ?? null) === 'pendiente'): ?>
                                <form method="post" action="<?= site_url('admin/orders/update/' . $order['id']) ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="confirmado">
                                    <button type="submit" class="btn btn-primary">Confirmar pedido</button>
                                </form>
                            <?php else: ?>
                                <a href="<?= site_url('admin/orders/' . $order['id']) ?>" class="btn btn-outline">Ver detalle</a>
                            <?php endif; ?>
                            <details class="orders-more-menu">
                                <summary class="btn btn-outline orders-more-trigger" aria-label="Mas acciones">...</summary>
                                <div class="orders-more-panel">
                                    <?php if (($order['status'] ?? null) === 'pendiente'): ?>
                                        <a href="<?= site_url('admin/orders/' . $order['id']) ?>" class="btn btn-outline">Ver detalle</a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('admin/orders/edit/' . $order['id']) ?>" class="btn btn-outline">Cambiar estado</a>
                                    <form method="post" action="<?= site_url('admin/orders/delete/' . $order['id']) ?>" onsubmit="return confirm('¿Eliminar este pedido? Esta accion tambien eliminara su factura y entrega asociada.');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </details>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
