<?php // Vista de mensajeria: permite leer o crear conversaciones entre usuarios.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<style>
    .messaging-app {
        display: grid;
        grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
        gap: 1rem;
        min-width: 0;
    }

    .conversation-panel,
    .chat-panel {
        min-width: 0;
    }

    .conversation-stack {
        display: grid;
        gap: .75rem;
    }

    .conversation-item {
        display: block;
        padding: .95rem 1rem;
        border-radius: 18px;
        border: 1px solid rgba(15, 23, 42, .08);
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
        overflow: hidden;
    }

    .conversation-item.is-active {
        border-color: rgba(15, 98, 254, .24);
        background: #f6f9ff;
    }

    .conversation-item strong,
    .conversation-item span,
    .conversation-item small {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .chat-shell {
        display: grid;
        gap: 1rem;
    }

    .message-thread {
        display: flex;
        flex-direction: column;
        gap: .9rem;
        max-height: 62vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: .25rem;
    }

    .message-bubble {
        max-width: min(100%, 760px);
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 20px;
        padding: 1rem 1.1rem;
        background: #fff;
    }

    .message-bubble.is-own {
        align-self: flex-end;
        background: #f3f8ff;
        border-color: rgba(15, 98, 254, .14);
    }

    .message-bubble div,
    .message-bubble strong,
    .message-bubble span {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .message-meta {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: .45rem;
    }

    .reply-composer {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: end;
    }

    .reply-composer .field {
        margin: 0;
    }

    .reply-composer textarea {
        min-height: 120px;
        resize: vertical;
    }

    @media (max-width: 980px) {
        .messaging-app {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .reply-composer {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="dashboard-header">
    <div>
        <h1><?= esc($conversation['participant_name'] ?? $conversation['subject']) ?></h1>
        <p class="muted"><?= $conversation['order_ref'] ? 'Pedido #' . esc($conversation['order_ref']) : 'Conversación general' ?></p>
    </div>
    <div class="toolbar">
        <a href="<?= site_url('messages') ?>" class="btn btn-outline">Volver</a>
        <a href="<?= site_url('messages/new') ?>" class="btn btn-primary">Nuevo mensaje</a>
    </div>
</div>

<div class="messaging-app">
    <aside class="table-card conversation-panel">
        <div class="heading">
            <h3 class="section-title">Conversaciones</h3>
            <p class="section-copy">Abre otro hilo sin salir de la mensajería.</p>
        </div>
        <div class="conversation-stack">
            <?php foreach ($conversations as $item): ?>
                <?php $conversationTitle = $item['participant_name'] ?? $item['subject']; ?>
                <a href="<?= site_url('messages/' . $item['id']) ?>" class="conversation-item <?= (int) $item['id'] === (int) $conversation['id'] ? 'is-active' : '' ?>">
                    <strong><?= esc($conversationTitle) ?></strong>
                    <small class="muted"><?= esc(format_order_datetime($item['last_message_at'] ?? null) ?: 'Sin mensajes') ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </aside>

    <section class="table-card chat-panel">
        <div class="heading">
            <h3 class="section-title">Chat</h3>
            <p class="section-copy"><?= ! empty($replyTargetName) ? 'Escribe abajo y envía directamente a ' . esc($replyTargetName) . '.' : 'Escribe abajo para continuar la conversación.' ?></p>
        </div>
        <div class="chat-shell">
            <?php if ($messages): ?>
                <div class="message-thread">
                    <?php foreach ($messages as $message): ?>
                        <article class="message-bubble <?= ((int) $message['sender_id'] === (int) ($currentUser['id'] ?? 0)) ? 'is-own' : '' ?>">
                            <div class="message-meta">
                                <strong><?= esc($message['sender_name']) ?></strong>
                                <span class="muted"><?= esc(format_order_datetime($message['created_at'] ?? null)) ?></span>
                            </div>
                            <div><?= nl2br(esc($message['message'])) ?></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty">Esta conversación todavía no tiene mensajes.</div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('messages/reply/' . $conversation['id']) ?>">
                <?= csrf_field() ?>
                <div class="reply-composer">
                    <div class="field">
                        <label>Escribe tu mensaje</label>
                        <textarea name="message" rows="4" required placeholder="Escribe aquí y pulsa enviar..."><?= esc(old('message')) ?></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Enviar</button>
                </div>
            </form>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
