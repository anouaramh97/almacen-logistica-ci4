<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="table-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h1>Productos</h1>
            <p><?= $search !== '' ? 'Resultados para "' . esc($search) . '"' : 'Busca por nombre, SKU o categoria' ?></p>
        </div>
        <div class="toolbar">
            <form method="get" action="<?= site_url('admin/products') ?>" class="toolbar" style="flex:1 1 320px;">
                <input name="q" value="<?= esc($search) ?>" placeholder="Buscar producto..." style="flex:1 1 240px;">
                <button class="btn btn-outline">Buscar</button>
            </form>
            <a href="<?= site_url('admin/products/create') ?>" class="btn btn-primary">Nuevo producto</a>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Foto</th><th>ID</th><th>Nombre</th><th>SKU</th><th>Categoria</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td style="width:90px;"><img src="<?= esc(product_image_url($product['image_path'] ?? null, $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:64px;height:64px;object-fit:cover;border-radius:18px;"></td>
                    <td><?= esc($product['id']) ?></td>
                    <td><strong><?= esc($product['name']) ?></strong></td>
                    <td><?= esc($product['sku']) ?></td>
                    <td><?= esc($product['category_name']) ?></td>
                    <td><?= number_format((float) $product['price'], 2) ?> EUR</td>
                    <td><?= esc($product['stock_total']) ?></td>
                    <td><span class="pill"><?= esc(status_label($product['status'])) ?></span></td>
                    <td><div class="toolbar"><a class="btn btn-outline" href="<?= site_url('admin/products/' . $product['id']) ?>">Ver</a><a class="btn btn-outline" href="<?= site_url('admin/products/edit/' . $product['id']) ?>">Editar</a><form method="post" action="<?= site_url('admin/products/delete/' . $product['id']) ?>" onsubmit="return confirm('¿Eliminar este producto?');" style="display:inline;"><?= csrf_field() ?><button class="btn btn-danger" type="submit">Eliminar</button></form></div></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
