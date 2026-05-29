<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$showCategoryForm = $editingCategory !== null || old('name') !== null || session('error');
?>
<div class="dashboard-header">
    <div>
        <h1>Categorías</h1>
        <p class="muted">Gestiona la clasificación del catálogo y añade nuevas categorías desde el mismo panel.</p>
    </div>
</div>

<section class="table-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h2>Listado de categorías</h2>
            <p class="section-copy">Vista administrativa con identificador, nombre y descripción.</p>
        </div>
        <div class="toolbar">
            <button
                type="button"
                class="btn btn-primary"
                id="toggleCategoryForm"
                aria-expanded="<?= $showCategoryForm ? 'true' : 'false' ?>"
                aria-controls="categoryFormPanel"
            >
                <?= $editingCategory ? 'Editar categoría' : 'Añadir' ?>
            </button>
        </div>
    </div>

    <div id="categoryFormPanel" style="<?= $showCategoryForm ? '' : 'display:none;' ?> margin-bottom:1rem;">
        <div class="feature-card">
            <div class="heading">
                <h3 class="section-title"><?= $editingCategory ? 'Editar categoría' : 'Nueva categoría' ?></h3>
                <p class="section-copy"><?= $editingCategory ? 'Actualiza los datos de la categoría seleccionada.' : 'Completa los campos para registrar una nueva categoría.' ?></p>
            </div>
            <form method="post" action="<?= $editingCategory ? site_url('admin/categories/update/' . $editingCategory['id']) : site_url('admin/categories') ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label>Nombre</label>
                    <input name="name" value="<?= esc($editingCategory['name'] ?? old('name')) ?>" required>
                </div>
                <div class="field">
                    <label>Descripción</label>
                    <textarea name="description" rows="5"><?= esc($editingCategory['description'] ?? old('description')) ?></textarea>
                </div>
                <div class="toolbar">
                    <button class="btn btn-primary" type="submit"><?= $editingCategory ? 'Actualizar categoría' : 'Guardar categoría' ?></button>
                    <?php if ($editingCategory): ?>
                        <a href="<?= site_url('admin/categories') ?>" class="btn btn-outline">Cancelar</a>
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
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories): ?>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td>#<?= esc($category['id']) ?></td>
                            <td><strong><?= esc($category['name']) ?></strong></td>
                            <td><?= esc($category['description'] ?: 'Sin descripción') ?></td>
                            <td>
                                <div class="toolbar">
                                    <a class="btn btn-outline" href="<?= site_url('admin/categories/edit/' . $category['id']) ?>">Editar</a>
                                    <form method="post" action="<?= site_url('admin/categories/delete/' . $category['id']) ?>" onsubmit="return confirm('¿Eliminar esta categoría?');" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-danger" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="muted">No hay categorías registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (() => {
        const toggleButton = document.getElementById('toggleCategoryForm');
        const panel = document.getElementById('categoryFormPanel');

        if (!toggleButton || !panel) return;

        toggleButton.addEventListener('click', () => {
            const isHidden = panel.style.display === 'none';
            panel.style.display = isHidden ? '' : 'none';
            toggleButton.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
    })();
</script>
<?= $this->endSection() ?>
