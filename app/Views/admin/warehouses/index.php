<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$showWarehouseForm = $editingWarehouse !== null || old('name') !== null || session('error');
?>
<div class="dashboard-header">
    <div>
        <h1>Almacenes</h1>
        <p class="muted">Da de alta centros logísticos y consulta rápidamente sus datos principales.</p>
    </div>
</div>

<section class="table-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h2>Listado de almacenes</h2>
            <p class="section-copy">Vista administrativa con nombre, dirección y ciudad de cada ubicación.</p>
        </div>
        <div class="toolbar">
            <button
                type="button"
                class="btn btn-primary"
                id="toggleWarehouseForm"
                aria-expanded="<?= $showWarehouseForm ? 'true' : 'false' ?>"
                aria-controls="warehouseFormPanel"
            >
                <?= $editingWarehouse ? 'Editar almacén' : 'Añadir' ?>
            </button>
        </div>
    </div>

    <div id="warehouseFormPanel" style="<?= $showWarehouseForm ? '' : 'display:none;' ?> margin-bottom:1rem;">
        <div class="feature-card">
            <div class="heading">
                <h3 class="section-title"><?= $editingWarehouse ? 'Editar almacén' : 'Nuevo almacén' ?></h3>
                <p class="section-copy"><?= $editingWarehouse ? 'Actualiza los datos de la ubicación seleccionada.' : 'Completa los campos para registrar un nuevo almacén.' ?></p>
            </div>
            <form method="post" action="<?= $editingWarehouse ? site_url('admin/warehouses/update/' . $editingWarehouse['id']) : site_url('admin/warehouses') ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Nombre</label>
                    <input name="name" value="<?= esc($editingWarehouse['name'] ?? old('name')) ?>" required>
                </div>
                <div class="field">
                    <label>Dirección</label>
                    <input name="address" value="<?= esc($editingWarehouse['address'] ?? old('address')) ?>" required>
                </div>
                <div class="grid-2">
                    <div class="field">
                        <label>Ciudad</label>
                        <input name="city" value="<?= esc($editingWarehouse['city'] ?? old('city')) ?>" required>
                    </div>
                    <div class="field">
                        <label>Código postal</label>
                        <input name="postal_code" value="<?= esc($editingWarehouse['postal_code'] ?? old('postal_code')) ?>">
                    </div>
                </div>
                <div class="toolbar">
                    <button class="btn btn-primary" type="submit"><?= $editingWarehouse ? 'Actualizar almacén' : 'Guardar almacén' ?></button>
                    <?php if ($editingWarehouse): ?>
                        <a href="<?= site_url('admin/warehouses') ?>" class="btn btn-outline">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Ciudad</th>
                    <th>Código postal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($warehouses): ?>
                    <?php foreach ($warehouses as $warehouse): ?>
                        <tr>
                            <td>#<?= esc($warehouse['id']) ?></td>
                            <td><strong><?= esc($warehouse['name']) ?></strong></td>
                            <td><?= esc($warehouse['address']) ?></td>
                            <td><?= esc($warehouse['city']) ?></td>
                            <td><?= esc($warehouse['postal_code'] ?: '-') ?></td>
                            <td>
                                <div class="toolbar">
                                    <a class="btn btn-outline" href="<?= site_url('admin/warehouses/edit/' . $warehouse['id']) ?>">Editar</a>
                                    <form method="post" action="<?= site_url('admin/warehouses/delete/' . $warehouse['id']) ?>" onsubmit="return confirm('¿Eliminar este almacén?');" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="muted">No hay almacenes registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (() => {
        const toggleButton = document.getElementById('toggleWarehouseForm');
        const panel = document.getElementById('warehouseFormPanel');

        if (!toggleButton || !panel) return;

        toggleButton.addEventListener('click', () => {
            const isHidden = panel.style.display === 'none';
            panel.style.display = isHidden ? '' : 'none';
            toggleButton.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
    })();
</script>
<?= $this->endSection() ?>
