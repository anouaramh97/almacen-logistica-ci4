<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="table-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro">
            <h1>Usuarios del sistema</h1>
            <p>Gestiona administradores, empresas logisticas, clientes y conductores.</p>
        </div>
        <a class="btn btn-primary" href="<?= site_url('admin/users/create') ?>">Nuevo usuario</a>
    </div>
    <?php if (($adminCount ?? 0) === 1): ?>
        <div class="flash flash-success">Solo queda un administrador activo en el sistema. Ese usuario no se puede eliminar hasta que exista otro administrador.</div>
    <?php endif; ?>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Telefono</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <?php $isProtectedLastAdmin = ($user['role_name'] ?? '') === 'administrador' && ($adminCount ?? 0) === 1; ?>
                <tr>
                    <td><strong><?= esc($user['name']) ?></strong></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><span class="pill"><?= esc(role_label($user['role_name'])) ?></span></td>
                    <td><span class="pill" style="background:<?= ($user['status'] ?? '') === 'activo' ? 'rgba(20,120,93,0.12)' : 'rgba(107,114,128,0.14)' ?>; color:<?= ($user['status'] ?? '') === 'activo' ? '#14785d' : '#4b5563' ?>;"><?= esc(status_label($user['status'])) ?></span></td>
                    <td><?= esc($user['phone'] ?: 'Sin dato') ?></td>
                    <td>
                        <div class="toolbar" style="justify-content:flex-end;">
                            <?php if (($user['status'] ?? '') !== 'activo'): ?>
                                <form method="post" action="<?= site_url('admin/users/activate/' . $user['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-primary" type="submit">Activar usuario</button>
                                </form>
                            <?php endif; ?>
                            <a class="btn btn-outline" href="<?= site_url('admin/users/edit/' . $user['id']) ?>">Editar</a>
                            <?php if ($isProtectedLastAdmin): ?>
                                <button class="btn btn-outline" type="button" disabled>No eliminable</button>
                            <?php else: ?>
                                <form method="post" action="<?= site_url('admin/users/delete/' . $user['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-danger" onclick="return confirm('Eliminar este usuario?')">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
