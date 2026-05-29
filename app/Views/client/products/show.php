<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="summary-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h1><?= esc($product['name']) ?></h1>
            <p><?= esc($product['category_name']) ?> · SKU <?= esc($product['sku']) ?></p>
        </div>
        <div class="toolbar">
            <a class="btn btn-outline" href="<?= site_url('client/orders/create') ?>">Volver</a>
        </div>
    </div>

    <div class="grid-2" style="align-items:start;">
        <div>
            <img src="<?= esc(product_image_url($product['image_path'] ?? null, $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:100%;max-height:420px;object-fit:cover;border-radius:28px;">

            <?php if (! empty($gallery)): ?>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1rem;margin-top:1rem;">
                    <?php foreach ($gallery as $galleryImage): ?>
                        <img src="<?= esc(product_image_url($galleryImage['path'], $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:20px;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="feature-card">
            <div class="heading">
                <h3>Detalles del producto</h3>
                <p>Vista ampliada para que el cliente pueda revisar el producto antes de pedirlo.</p>
            </div>
            <div class="panel-grid">
                <div class="summary-line"><span>Precio</span><strong><?= number_format((float) $product['price'], 2) ?> EUR</strong></div>
                <div class="summary-line"><span>IVA</span><strong><?= number_format((float) $product['tax_rate'], 2) ?>%</strong></div>
                <div class="summary-line"><span>Estado</span><strong><?= esc(status_label($product['status'])) ?></strong></div>
                <div class="summary-line"><span>Peso</span><strong><?= esc($product['weight'] ?: '-') ?></strong></div>
            </div>
            <div class="feature-card" style="margin-top:1rem;">
                <strong>Descripcion</strong>
                <p><?= esc($product['description'] ?: 'Sin descripcion registrada.') ?></p>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
