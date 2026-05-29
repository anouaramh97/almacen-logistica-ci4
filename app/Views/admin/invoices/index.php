<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="table-card">
    <div class="heading"><h1>Facturas</h1><p class="section-copy">Facturacion generada desde pedidos del sistema.</p></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Numero</th><th>Pedido</th><th>Cliente</th><th>Total</th><th>Accion</th></tr></thead>
            <tbody>
            <?php foreach ($invoices as $invoice): ?>
                <tr>
                    <td><?= esc($invoice['invoice_number']) ?></td>
                    <td>#<?= esc($invoice['order_ref']) ?></td>
                    <td><?= esc($invoice['customer_name']) ?></td>
                    <td><?= number_format((float) $invoice['total'], 2) ?> EUR</td>
                    <td><div class="toolbar"><a href="<?= site_url('admin/invoices/' . $invoice['id']) ?>" class="btn btn-outline">Ver</a><a href="<?= site_url('admin/invoices/' . $invoice['id'] . '/pdf') ?>" class="btn btn-primary">Descargar PDF</a></div></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
