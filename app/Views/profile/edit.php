<?php // Vista de perfil: permite consultar y actualizar datos personales.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="dashboard-grid">
    <aside class="summary-card">
        <div style="display:flex; gap:1rem; align-items:center; margin-bottom:1rem;">
            <div class="avatar-fallback" style="width:96px;height:96px;border-radius:24px;"><img src="<?= esc(avatar_url($user['avatar_path'] ?? null, $user['name'])) ?>" alt="<?= esc($user['name']) ?>"></div>
            <div>
                <h2>Mi perfil</h2>
                <div class="muted"><?= esc($user['email']) ?></div>
                <div class="pill" style="margin-top:.5rem;"><?= esc(role_label($user['role_name'])) ?></div>
            </div>
        </div>
        <div class="panel-grid">
            <div class="summary-line"><span>Estado</span><strong><?= esc(status_label($user['status'])) ?></strong></div>
            <div class="summary-line"><span>Telefono</span><strong><?= esc($user['phone'] ?: 'Sin definir') ?></strong></div>
            <div class="summary-line"><span>Ciudad</span><strong><?= esc($user['city'] ?: 'Sin definir') ?></strong></div>
            <div class="summary-line"><span>Codigo postal</span><strong><?= esc($user['postal_code'] ?: 'Sin definir') ?></strong></div>
        </div>
    </aside>
    <section class="summary-card">
        <div class="heading"><h3 class="section-title">Informacion personal</h3><p class="section-copy">Actualiza tus datos visibles dentro del sistema.</p></div>
        <form method="post" action="<?= site_url('profile') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="grid-2">
                <div class="field"><label>Nombre completo</label><input name="name" value="<?= esc($user['name']) ?>" required></div>
                <div class="field"><label>Correo electronico</label><input name="email" type="email" value="<?= esc($user['email']) ?>" required></div>
            </div>
            <div class="grid-3">
                <div class="field"><label>Telefono</label><input name="phone" value="<?= esc($user['phone'] ?? '') ?>"></div>
                <div class="field"><label>Ciudad</label><input name="city" value="<?= esc($user['city'] ?? '') ?>"></div>
                <div class="field"><label>Codigo postal</label><input name="postal_code" value="<?= esc($user['postal_code'] ?? '') ?>"></div>
            </div>
            <div class="field"><label>Direccion</label><input name="address" value="<?= esc($user['address'] ?? '') ?>"></div>
            <div class="field"><label>Avatar</label><input type="file" name="avatar" accept="image/*"></div>
            <button class="btn btn-primary">Guardar perfil</button>
        </form>
        <?php if (($user['role_name'] ?? '') !== 'administrador'): ?>
            <div style="margin-top:2rem; padding-top:2rem; border-top:1px solid rgba(15,23,42,0.08);">
                <div class="heading"><h3 class="section-title">Zona delicada</h3><p class="section-copy">Elimina tu cuenta solo si estas completamente seguro.</p></div>
                <form method="post" action="<?= site_url('profile/delete') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger" onclick="return confirm('Seguro que quieres eliminar tu cuenta?')">Eliminar cuenta</button>
                </form>
            </div>
        <?php else: ?>
            <div class="empty" style="margin-top:1rem;">La cuenta del administrador principal no se puede eliminar desde el perfil.</div>
        <?php endif; ?>
    </section>
</div>
<?= $this->endSection() ?>
