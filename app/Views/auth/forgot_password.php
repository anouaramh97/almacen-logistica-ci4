<?php // Vista de autenticacion: formulario publico de acceso, registro o recuperacion.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="auth-grid">
    <section class="auth-showcase">
        <div class="hero-kicker">Recuperacion</div>
        <h2>Recupera el acceso a tu cuenta</h2>
        <p class="hero-text">Te enviaremos un enlace seguro para crear una nueva contrasena si el correo pertenece a una cuenta registrada.</p>
    </section>
    <section class="auth-card">
        <div class="heading"><h2>Olvide mi contrasena</h2><p class="helper">Introduce tu correo electronico para recibir el enlace de recuperacion.</p></div>
        <form method="post" action="<?= site_url('forgot-password') ?>">
            <?= csrf_field() ?>
            <div class="field"><label for="email">Correo electronico</label><input id="email" type="email" name="email" value="<?= esc(old('email')) ?>" required></div>
            <button class="btn btn-primary" type="submit">Enviar enlace</button>
        </form>
        <p class="helper" style="margin-top:1rem;"><a href="<?= site_url('login') ?>">Volver al inicio de sesion</a></p>
    </section>
</div>
<?= $this->endSection() ?>
