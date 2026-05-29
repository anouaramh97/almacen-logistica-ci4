<?php // Vista general: presenta la informacion preparada por el controlador.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="home-shell">
    <section class="hero">
        <div class="hero-kicker">Gestion de almacen y operaciones</div>
        <h1 class="hero-title"><?= esc(lang('App.home_title')) ?></h1>
        <p class="hero-text">Plataforma operativa para gestionar acceso, usuarios, catálogo, stock y pedidos desde una única interfaz.</p>
        <div class="hero-actions">
            <a href="<?= site_url('login') ?>" class="btn btn-primary"><?= esc(lang('App.login')) ?></a>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
