<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="grid-4">
    <div class="feature-card"><strong>Pedidos totales</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['orders']) ?></div></div>
    <div class="feature-card"><strong>Pendientes</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['pending_orders']) ?></div></div>
    <div class="feature-card"><strong>Entregados</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['delivered_orders']) ?></div></div>
    <div class="feature-card"><strong>Facturas</strong><div style="font-size:2rem;font-weight:800;"><?= esc((string) $stats['invoices']) ?></div></div>
</div>
<div class="dashboard-grid" style="margin-top:1rem;">
    <section class="table-card"><div class="heading"><h2>Tu area de cliente</h2><p class="section-copy">Consulta tus pedidos, revisa su estado y accede a tus facturas de forma sencilla.</p></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Fecha pedido</th><th>Estado</th><th>Total</th></tr></thead><tbody><?php foreach ($recentOrders as $order): ?><tr><td>#<?= esc($order['id']) ?></td><td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td><td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td><td><?= number_format((float) $order['total'], 2) ?> EUR</td></tr><?php endforeach; ?></tbody></table></div></section>
    <section class="summary-card"><div class="heading"><h3 class="section-title">Facturas recientes</h3></div><?php if ($recentInvoices): foreach ($recentInvoices as $invoice): ?><div class="feature-card" style="margin-bottom:0.85rem;"><strong><?= esc($invoice['invoice_number']) ?></strong><p>Pedido #<?= esc($invoice['order_ref']) ?> | <?= number_format((float) $invoice['total'], 2) ?> EUR</p></div><?php endforeach; else: ?><div class="empty">No tienes facturas registradas todavia.</div><?php endif; ?></section>
</div>
<?= $this->endSection() ?>
