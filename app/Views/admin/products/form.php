<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="summary-card">
        <div class="topbar" style="margin-bottom:1rem;">
            <div class="top-intro"><h1><?= $product ? 'Editar producto' : 'Crear producto' ?></h1><p><?= $product ? 'Actualiza el catalogo y el pricing.' : 'Da de alta el producto y define su stock inicial directamente.' ?></p></div>
        </div>
    <form method="post" action="<?= $product ? site_url('admin/products/update/' . $product['id']) : site_url('admin/products') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="grid-2">
            <div class="field"><label>Categoria</label><select name="category_id" required><?php foreach ($categories as $category): ?><option value="<?= $category['id'] ?>" <?= (string) ($product['category_id'] ?? old('category_id')) === (string) $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Estado</label><select name="status"><option value="activo" <?= ($product['status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($product['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
        </div>
        <div class="grid-2">
            <div class="field"><label>Nombre</label><input name="name" value="<?= esc($product['name'] ?? old('name')) ?>" required></div>
            <div class="field"><label>SKU</label><input name="sku" value="<?= esc($product['sku'] ?? old('sku')) ?>" required></div>
        </div>
        <div class="field"><label>Descripcion</label><textarea name="description" rows="5"><?= esc($product['description'] ?? old('description')) ?></textarea></div>
        <div class="field"><label>Imagen principal</label><input type="file" name="image" accept="image/*"></div>
        <?php if (! empty($product['image_path'])): ?><div class="field"><img src="<?= esc(product_image_url($product['image_path'], $product['name'])) ?>" alt="<?= esc($product['name']) ?>" style="width:min(180px, 100%);height:auto;aspect-ratio:1/1;object-fit:cover;border-radius:24px;"></div><?php endif; ?>
        <div class="field">
            <label>Galeria del producto</label>
            <input type="file" name="gallery_images[]" accept="image/*" multiple>
            <small style="display:block;margin-top:.5rem;color:#64748b;">Puedes seleccionar varias imagenes para que el cliente vea el producto desde diferentes angulos.</small>
        </div>
        <div class="grid-3">
            <div class="field"><label>Precio</label><input name="price" type="number" step="0.01" value="<?= esc($product['price'] ?? old('price')) ?>" required></div>
            <div class="field"><label>IVA (%)</label><input name="tax_rate" type="number" step="0.01" value="<?= esc($product['tax_rate'] ?? old('tax_rate') ?: 21) ?>"></div>
            <div class="field"><label>Peso</label><input name="weight" type="number" step="0.01" value="<?= esc($product['weight'] ?? old('weight')) ?>"></div>
        </div>
        <?php if (! $product): ?>
            <div class="feature-card" style="margin:1rem 0;">
                <strong>Stock inicial</strong>
                <div class="grid-3" style="margin-top:1rem;">
                    <div class="field"><label>Almacen</label><select name="warehouse_id" required><option value="">Selecciona un almacén</option><?php foreach ($warehouses as $warehouse): ?><option value="<?= $warehouse['id'] ?>" <?= (string) old('warehouse_id') === (string) $warehouse['id'] ? 'selected' : '' ?>><?= esc($warehouse['name']) ?></option><?php endforeach; ?></select></div>
                    <div class="field"><label>Cantidad inicial</label><input name="quantity" type="number" value="<?= esc(old('quantity') ?: '0') ?>"></div>
                    <div class="field"><label>Stock minimo</label><input name="minimum_quantity" type="number" value="<?= esc(old('minimum_quantity') ?: '0') ?>"></div>
                </div>
            </div>
        <?php endif; ?>
        <button class="btn btn-primary">Guardar producto</button>
    </form>
    <?php if (! empty($gallery)): ?>
        <div class="field" style="margin-top:1.5rem;">
            <label>Imagenes actuales</label>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;">
                <?php foreach ($gallery as $galleryImage): ?>
                    <div class="feature-card" style="padding:1rem;">
                        <img src="<?= esc(product_image_url($galleryImage['path'], $product['name'] ?? 'Producto')) ?>" alt="<?= esc($product['name'] ?? 'Producto') ?>" style="width:100%;aspect-ratio:1/1;object-fit:cover;border-radius:20px;">
                        <?php if (! empty($product['id'])): ?>
                            <form method="post" action="<?= site_url('admin/products/' . $product['id'] . '/gallery/delete/' . $galleryImage['id']) ?>" onsubmit="return confirm('¿Eliminar esta imagen?');" style="margin-top:.75rem;">
                                <?= csrf_field() ?>
                                <button class="btn btn-danger" type="submit" style="width:100%;">Eliminar imagen</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
