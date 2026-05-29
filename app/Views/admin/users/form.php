<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="summary-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro"><h1><?= $user ? 'Editar usuario' : 'Crear usuario' ?></h1><p>Gestiona administradores, logistica, conductores y clientes.</p></div>
    </div>
    <form method="post" action="<?= $user ? site_url('admin/users/update/' . $user['id']) : site_url('admin/users') ?>">
        <?= csrf_field() ?>
        <div class="grid-2">
            <div class="field"><label>Rol</label><select name="role_id" required><?php foreach ($roles as $role): ?><option value="<?= $role['id'] ?>" <?= (string) ($user['role_id'] ?? '') === (string) $role['id'] ? 'selected' : '' ?>><?= esc($role['name']) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Estado</label><select name="status"><option value="activo" <?= ($user['status'] ?? 'activo') === 'activo' ? 'selected' : '' ?>>Activo</option><option value="inactivo" <?= ($user['status'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option></select></div>
        </div>
        <div class="grid-2">
            <div class="field"><label>Nombre</label><input name="name" value="<?= esc($user['name'] ?? old('name')) ?>" required></div>
            <div class="field"><label>Email</label><input name="email" type="email" value="<?= esc($user['email'] ?? old('email')) ?>" required></div>
        </div>
        <div class="field"><label><?= $user ? 'Nueva contrasena (opcional)' : 'Contrasena' ?></label><input name="password" type="password" <?= $user ? '' : 'required' ?>></div>
        <div class="grid-3">
            <div class="field"><label>Telefono</label><input name="phone" value="<?= esc($user['phone'] ?? old('phone')) ?>"></div>
            <div class="field"><label>Ciudad</label><input name="city" value="<?= esc($user['city'] ?? old('city')) ?>"></div>
            <div class="field"><label>Codigo postal</label><input name="postal_code" value="<?= esc($user['postal_code'] ?? old('postal_code')) ?>"></div>
        </div>
        <div class="field"><label>Direccion</label><input name="address" value="<?= esc($user['address'] ?? old('address')) ?>"></div>
        <button class="btn btn-primary">Guardar usuario</button>
    </form>
</section>
<?= $this->endSection() ?>