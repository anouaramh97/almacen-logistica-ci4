<?php // Vista de mensajeria: permite leer o crear conversaciones entre usuarios.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .message-shell {
        display: grid;
        gap: 1rem;
    }

    .message-list-card {
        display: block;
        padding: 1rem 1.1rem;
        border-radius: 20px;
        border: 1px solid rgba(15, 23, 42, .08);
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 14px 30px rgba(15, 23, 42, .06);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .message-list-card:hover {
        transform: translateY(-1px);
        border-color: rgba(15, 98, 254, .22);
        box-shadow: 0 18px 34px rgba(15, 23, 42, .08);
    }

    .message-list-top {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: .55rem;
    }

    .message-list-card strong,
    .message-list-card p,
    .message-list-card span,
    .message-list-card small {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .message-list-meta {
        display: flex;
        gap: .55rem;
        flex-wrap: wrap;
        margin-top: .7rem;
    }
</style>
<div class="dashboard-header">
    <div>
        <h1>Mensajería interna</h1>
        <p class="muted"><?= (($currentUser['role_name'] ?? '') === 'administrador') ? 'Abre una conversación para verla completa y seguir respondiendo desde el chat.' : 'Consulta tus conversaciones y continúa escribiendo sin perder el hilo.' ?></p>
    </div>
    <a href="<?= site_url('messages/new') ?>" class="btn btn-primary">Nuevo mensaje</a>
</div>

<section class="table-card">
    <div class="heading">
        <h3 class="section-title">Conversaciones</h3>
        <p class="section-copy">Cada conversación se abre en una vista tipo app de mensajería.</p>
    </div>
    <div class="message-shell">
        <?php if ($conversations): ?>
            <?php foreach ($conversations as $conversation): ?>
                <?php $conversationTitle = $conversation['participant_name'] ?? $conversation['subject']; ?>
                <a href="<?= site_url('messages/' . $conversation['id']) ?>" class="message-list-card">
                    <div class="message-list-top">
                        <div>
                            <strong><?= esc($conversationTitle) ?></strong>
                        </div>
                        <span class="pill is-primary">Abrir</span>
                    </div>
                    <small class="muted">Último movimiento: <?= esc(format_order_datetime($conversation['last_message_at'] ?? null) ?: 'Sin mensajes') ?></small>
                    <div class="message-list-meta">
                        <span class="pill"><?= $conversation['order_ref'] ? 'Pedido #' . esc($conversation['order_ref']) : 'General' ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty">Todavía no hay conversaciones registradas.</div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
