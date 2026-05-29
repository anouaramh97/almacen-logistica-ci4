<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="table-card">
    <div class="heading"><h1>Control de stock</h1><p class="section-copy">Consulta existencias por almacen y ajusta cantidades rapidamente.</p></div>
    <div style="margin-bottom:1rem;"><a href="<?= site_url('admin/products/create') ?>" class="btn btn-primary">Nuevo producto con stock</a></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Producto</th><th>Almacen</th><th>Cantidad</th><th>Minimo</th><th>Estado</th><th>Accion</th></tr></thead>
            <tbody>
            <?php foreach ($stocks as $stock): ?>
                <?php $low = (int) $stock['quantity'] <= (int) $stock['minimum_quantity']; ?>
                <tr>
                    <td><strong><?= esc($stock['product_name']) ?></strong><div class="muted"><?= esc($stock['sku']) ?></div></td>
                    <td><?= esc($stock['warehouse_name']) ?></td>
                    <td><?= esc($stock['quantity']) ?></td>
                    <td><?= esc($stock['minimum_quantity']) ?></td>
                    <td><span class="pill" style="background:<?= $low ? 'rgba(180,35,24,0.12)' : 'rgba(20,120,93,0.12)' ?>; color:<?= $low ? '#b42318' : '#14785d' ?>;"><?= $low ? 'Stock bajo' : 'Correcto' ?></span></td>
                    <td><a class="btn btn-outline" href="<?= site_url('admin/stocks/edit/' . $stock['id']) ?>">Ajustar</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>