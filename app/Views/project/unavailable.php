<?php // Vista de estado del proyecto: informa cuando la aplicacion no esta disponible.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="summary-card">
    <div class="heading">
        <h1>Muy pronto</h1>
    </div>

    <div class="toolbar" style="margin-top:1rem;">
        <a href="<?= site_url('dashboard') ?>" class="btn btn-outline">Volver</a>
    </div>
</section>
<?= $this->endSection() ?>
