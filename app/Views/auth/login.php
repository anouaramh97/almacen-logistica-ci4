<?php // Vista de autenticacion: formulario publico de acceso, registro o recuperacion.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="auth-grid">
    <section class="auth-showcase">
        <div class="hero-kicker">Acceso al sistema</div>
        <h2><?= esc(lang('App.login_showcase_title')) ?></h2>
        <p class="hero-text"><?= esc(lang('App.login_showcase_copy')) ?></p>
        <ul><li><?= esc(lang('App.login_bullet_1')) ?></li><li><?= esc(lang('App.login_bullet_2')) ?></li><li><?= esc(lang('App.login_bullet_3')) ?></li></ul>
    </section>
    <section class="auth-card">
        <div class="heading"><h2><?= esc(lang('App.login_title')) ?></h2><p class="helper"><?= esc(lang('App.login_copy')) ?></p></div>
        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <div class="field"><label for="email">Correo electronico</label><input id="email" type="email" name="email" value="<?= esc(old('email')) ?>" required></div>
            <div class="field">
                <label for="password">Contrasena</label>
                <div class="password-field">
                    <input id="password" type="password" name="password" required>
                    <button type="button" class="password-toggle" data-password-toggle aria-controls="password" aria-label="Mostrar contrasena">
                        <svg class="password-icon password-icon-show" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                            <path d="M2.25 12s3.5-6.75 9.75-6.75S21.75 12 21.75 12 18.25 18.75 12 18.75 2.25 12 2.25 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15.25a3.25 3.25 0 1 0 0-6.5 3.25 3.25 0 0 0 0 6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <svg class="password-icon password-icon-hide" aria-hidden="true" viewBox="0 0 24 24" fill="none">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M10.58 10.58a3.25 3.25 0 0 0 4.6 4.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M8.1 5.87A10.4 10.4 0 0 1 12 5.25c6.25 0 9.75 6.75 9.75 6.75a18.2 18.2 0 0 1-3.1 3.98M5.38 7.72A18.3 18.3 0 0 0 2.25 12S5.75 18.75 12 18.75c1.42 0 2.72-.35 3.9-.88" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button class="btn btn-primary" type="submit"><?= esc(lang('App.login')) ?></button>
        </form>
        <p class="helper" style="margin-top:1rem;"><a href="<?= site_url('forgot-password') ?>" style="color:var(--danger);font-weight:800;">Olvide mi contrasena</a></p>
        <p class="helper" style="margin-top:1rem;"><?= esc(lang('App.login_register_hint')) ?></p>
        <a href="<?= site_url('register') ?>" class="btn btn-outline"><?= esc(lang('App.go_register')) ?></a>
    </section>
</div>
<script>
    (() => {
        const toggle = document.querySelector('[data-password-toggle]');
        const password = document.getElementById('password');

        if (!toggle || !password) return;

        toggle.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            toggle.classList.toggle('is-visible', isHidden);
            toggle.setAttribute('aria-label', isHidden ? 'Ocultar contrasena' : 'Mostrar contrasena');
        });
    })();
</script>
<?= $this->endSection() ?>
