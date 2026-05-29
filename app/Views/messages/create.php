<?php // Vista de mensajeria: permite leer o crear conversaciones entre usuarios.
// Vista: plantilla encargada de presentar los datos preparados por el controlador.
// Vista: muestra la pantalla con los datos recibidos del controlador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $isAdmin = ($currentUser['role_name'] ?? '') === 'administrador'; ?>
<div class="dashboard-header">
    <div>
        <h1>Nuevo mensaje</h1>
        <p class="muted">Crea una conversación nueva y envía el primer mensaje desde una pantalla separada.</p>
    </div>
    <a href="<?= site_url('messages') ?>" class="btn btn-outline">Volver a conversaciones</a>
</div>

<section class="form-card" style="max-width:840px;">
    <div class="heading">
        <h3 class="section-title">Enviar mensaje</h3>
        <p class="section-copy">Abre un hilo nuevo para seguimiento de pedidos o coordinación interna.</p>
    </div>
    <form method="post" action="<?= site_url('messages') ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label>Asunto</label>
            <input name="subject" value="<?= esc(old('subject')) ?>" placeholder="Seguimiento de pedido o consulta interna" required>
        </div>
        <div class="grid-2">
            <div class="field">
                <label>Destinatario</label>
                <select name="receiver_id" id="receiver_id" required>
                    <option value="">Selecciona un usuario</option>
                    <?php foreach ($recipients as $recipient): ?>
                        <option
                            value="<?= $recipient['id'] ?>"
                            data-role-name="<?= esc($recipient['role_name']) ?>"
                            <?= old('receiver_id') == $recipient['id'] ? 'selected' : '' ?>
                        >
                            <?= esc($recipient['name']) ?> | <?= esc(role_label($recipient['role_name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Pedido relacionado (opcional)</label>
                <select name="order_id" id="order_id">
                    <option value="">Sin pedido relacionado</option>
                    <?php foreach ($orders as $order): ?>
                        <option
                            value="<?= $order['id'] ?>"
                            data-customer-id="<?= esc((string) ($order['customer_id'] ?? '')) ?>"
                            data-driver-ids="<?= esc((string) ($order['driver_ids'] ?? '')) ?>"
                            <?= old('order_id') == $order['id'] ? 'selected' : '' ?>
                        >
                            #<?= $order['id'] ?> | <?= esc($order['customer_name'] ?? 'Sin cliente') ?> | <?= esc(status_label($order['status'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label>Mensaje</label>
            <textarea name="message" rows="8" required><?= esc(old('message')) ?></textarea>
        </div>
        <div class="toolbar">
            <button class="btn btn-primary">Enviar mensaje</button>
            <a href="<?= site_url('messages') ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</section>
<?php if ($isAdmin): ?>
    <script>
        (() => {
            const receiverSelect = document.getElementById('receiver_id');
            const orderSelect = document.getElementById('order_id');

            if (!receiverSelect || !orderSelect) {
                return;
            }

            const orderOptions = Array.from(orderSelect.querySelectorAll('option'));

            const filterOrdersByReceiver = () => {
                const receiverId = receiverSelect.value;
                const selectedReceiverOption = receiverSelect.options[receiverSelect.selectedIndex];
                const receiverRole = selectedReceiverOption?.dataset.roleName || '';
                const currentValue = orderSelect.value;
                let currentValueStillVisible = false;

                orderOptions.forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const driverIds = (option.dataset.driverIds || '')
                        .split(',')
                        .map((value) => value.trim())
                        .filter((value) => value !== '');

                    let matchesReceiver = true;
                    if (receiverId !== '') {
                        if (receiverRole === 'cliente') {
                            matchesReceiver = option.dataset.customerId === receiverId;
                        } else if (receiverRole === 'conductor') {
                            matchesReceiver = driverIds.includes(receiverId);
                        }
                    }

                    option.hidden = receiverId !== '' ? !matchesReceiver : false;

                    if (!option.hidden && option.value === currentValue) {
                        currentValueStillVisible = true;
                    }
                });

                if (receiverId !== '' && !currentValueStillVisible) {
                    orderSelect.value = '';
                }
            };

            receiverSelect.addEventListener('change', filterOrdersByReceiver);
            filterOrdersByReceiver();
        })();
    </script>
<?php endif; ?>
<?= $this->endSection() ?>
