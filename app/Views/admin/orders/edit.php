<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$statusOptions = ['pendiente', 'confirmado', 'preparando', 'en_ruta', 'entregado', 'cancelado'];
?>
<section class="card form-card" style="max-width:760px;">
    <div class="heading">
        <h2>Actualizar estado del pedido #<?= esc($order['id']) ?></h2>
        <p class="section-copy">La confirmación se realiza desde el detalle o el listado. Aquí solo gestionas el resto de estados.</p>
    </div>

    <form method="post" action="<?= site_url('admin/orders/update/' . $order['id']) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label>Estado</label>
            <select name="status">
                <?php foreach ($statusOptions as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                        <?= esc(status_label($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="toolbar" style="margin-top:1rem;">
            <button class="btn btn-primary">Guardar cambio</button>
            <a class="btn btn-outline" href="<?= site_url('admin/orders/' . $order['id']) ?>">Volver al detalle</a>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
