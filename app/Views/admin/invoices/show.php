<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="topbar" style="margin-bottom:1rem;">
    <div class="top-intro">
        <h1>Factura <?= esc($invoice['invoice_number']) ?></h1>
        <p>Consulta el detalle de la factura y descargala en PDF.</p>
    </div>
    <a href="<?= site_url('admin/invoices/' . $invoice['id'] . '/pdf') ?>" class="btn btn-primary">Descargar PDF</a>
</div>
<div class="dashboard-grid">
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Datos generales</h3></div>
        <p><strong>Cliente:</strong> <?= esc($invoice['customer_name']) ?></p>
        <p><strong>Correo:</strong> <?= esc($invoice['customer_email']) ?></p>
        <p><strong>Fecha:</strong> <?= esc($invoice['issue_date']) ?></p>
        <p><strong>Pedido:</strong> #<?= esc($invoice['order_id']) ?></p>
        <p><strong>Direccion:</strong> <?= esc($invoice['delivery_address']) ?></p>
    </section>
    <section class="table-card">
        <div class="heading"><h3 class="section-title">Totales</h3></div>
        <div class="panel-grid">
            <div class="summary-line"><span>Subtotal</span><strong><?= number_format((float) $invoice['subtotal'], 2) ?> EUR</strong></div>
            <div class="summary-line"><span>IVA</span><strong><?= number_format((float) $invoice['tax'], 2) ?> EUR</strong></div>
            <div class="summary-line"><span>Total</span><strong><?= number_format((float) $invoice['total'], 2) ?> EUR</strong></div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
