<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-grid">
    <section class="summary-card">
        <div style="padding-bottom:1rem;"><img src="<?= esc(product_image_url($product['image_path'] ?? null, $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:100%;max-height:280px;object-fit:cover;border-radius:24px;"></div>
        <?php if (! empty($gallery)): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:.85rem;margin-bottom:1rem;">
                <?php foreach ($gallery as $galleryImage): ?>
                    <img src="<?= esc(product_image_url($galleryImage['path'], $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:18px;">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="heading"><h2><?= esc($product['name']) ?></h2><p class="section-copy">SKU <?= esc($product['sku']) ?> | <?= esc($product['category_name']) ?></p></div>
        <div class="panel-grid">
            <div class="summary-line"><span>Precio</span><strong><?= number_format((float) $product['price'], 2) ?> EUR</strong></div>
            <div class="summary-line"><span>Estado</span><strong><?= esc(status_label($product['status'])) ?></strong></div>
            <div class="summary-line"><span>IVA</span><strong><?= esc($product['tax_rate']) ?>%</strong></div>
            <div class="summary-line"><span>Peso</span><strong><?= esc($product['weight'] ?: '-') ?></strong></div>
        </div>
        <div class="feature-card" style="margin-top:1rem;"><strong>Descripcion</strong><p><?= esc($product['description'] ?: 'Sin descripcion registrada.') ?></p></div>
    </section>
    <section class="table-card">
        <div class="heading"><h3 class="section-title">Stock por almacen</h3><p class="section-copy">Disponibilidad actual del producto.</p></div>
        <div class="table-wrap"><table><thead><tr><th>Almacen</th><th>Ciudad</th><th>Cantidad</th><th>Minimo</th></tr></thead><tbody><?php if ($stocks): foreach ($stocks as $stock): ?><tr><td><?= esc($stock['warehouse_name']) ?></td><td><?= esc($stock['city']) ?></td><td><?= esc($stock['quantity']) ?></td><td><?= esc($stock['minimum_quantity']) ?></td></tr><?php endforeach; else: ?><tr><td colspan="4">No hay stock registrado para este producto.</td></tr><?php endif; ?></tbody></table></div>
    </section>
</div>
<?= $this->endSection() ?>
