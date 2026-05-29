<?php // Vista de repartidor: muestra rutas y entregas asignadas al conductor. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$status = (string) ($delivery['status'] ?? 'pendiente');
$nextActions = match ($status) {
    'pendiente' => [
        ['value' => 'en_transito', 'label' => 'Marcar en reparto', 'class' => 'btn-primary'],
        ['value' => 'fallida', 'label' => 'Marcar incidencia', 'class' => 'btn-outline'],
    ],
    'en_transito' => [
        ['value' => 'entregada', 'label' => 'Marcar entregado', 'class' => 'btn-primary'],
        ['value' => 'fallida', 'label' => 'Marcar incidencia', 'class' => 'btn-outline'],
    ],
    default => [],
};
$isClosedStatus = in_array($status, ['entregada', 'fallida'], true);
?>
<section class="card form-card">
    <style>
        .delivery-action-panel {
            padding: 1rem;
            border-radius: 22px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: linear-gradient(180deg, rgba(248, 251, 255, .96), rgba(255, 255, 255, .98));
            margin-bottom: 1rem;
        }

        .delivery-action-buttons {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            margin-top: .9rem;
        }

        .delivery-action-buttons form {
            margin: 0;
        }

        .delivery-final-note {
            color: #6e7c90;
            line-height: 1.65;
            margin: .35rem 0 0;
        }
    </style>

    <div class="heading">
        <h2>Entrega #<?= esc($delivery['id']) ?></h2>
        <p><?= esc($delivery['customer_name']) ?> - <?= esc($delivery['delivery_address']) ?></p>
    </div>

    <div class="grid-2" style="margin-bottom:1rem;">
        <div class="summary-line"><span>Estado actual</span><strong><span class="pill <?= esc(delivery_status_class($delivery['status'])) ?>"><?= esc(status_label($delivery['status'])) ?></span></strong></div>
        <div class="summary-line"><span>Pedido</span><strong>#<?= esc($delivery['order_id']) ?></strong></div>
        <div class="summary-line"><span>Fecha pedido</span><strong><?= esc(format_order_datetime($delivery['order_date'] ?? null)) ?></strong></div>
        <div class="summary-line"><span>Hora de salida estimada</span><strong><?= esc(format_order_datetime($delivery['departure_date'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Salida real en reparto</span><strong><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Hora estimada de entrega</span><strong><?= esc(format_order_datetime($delivery['estimated_delivery_at'] ?? null) ?: '-') ?></strong></div>
        <div class="summary-line"><span>Entrega real</span><strong><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></strong></div>
    </div>

    <div class="delivery-action-panel">
        <strong>Siguiente acción</strong>
        <p class="delivery-final-note">Salida real en reparto: <strong><?= esc(format_order_datetime($delivery['departed_at'] ?? null) ?: '-') ?></strong></p>
        <p class="delivery-final-note">Entrega real: <strong><?= esc(format_order_datetime($delivery['delivered_at'] ?? null) ?: '-') ?></strong></p>
        <?php if ($nextActions): ?>
            <p class="delivery-final-note">Solo puedes avanzar la entrega al siguiente estado válido. No se puede volver hacia atrás.</p>
            <?php if ($status === 'pendiente'): ?>
                <p class="delivery-final-note">Los campos de receptor y observaciones aparecerán después de marcar la entrega en reparto.</p>
            <?php endif; ?>
            <div class="delivery-action-buttons">
                <?php foreach ($nextActions as $action): ?>
                    <?php if ($status !== 'pendiente'): ?>
                        <button type="submit" form="delivery-progress-form" name="transition_status" value="<?= esc($action['value']) ?>" class="btn <?= esc($action['class']) ?>"><?= esc($action['label']) ?></button>
                    <?php else: ?>
                        <form method="post" action="<?= site_url('driver/deliveries/' . $delivery['id']) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="transition_status" value="<?= esc($action['value']) ?>">
                            <button type="submit" class="btn <?= esc($action['class']) ?>"><?= esc($action['label']) ?></button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="delivery-final-note">
                <?= $isClosedStatus ? 'Esta entrega ya está cerrada y no admite cambios de estado.' : 'No hay más transiciones disponibles para esta entrega.' ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if ($status !== 'pendiente'): ?>
        <form method="post" action="<?= site_url('driver/deliveries/' . $delivery['id']) ?>" id="delivery-progress-form">
            <?= csrf_field() ?>
            <div class="field">
                <label>Receptor</label>
                <input name="recipient_name" value="<?= esc(old('recipient_name', $delivery['recipient_name'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Observaciones</label>
                <textarea name="observations"><?= esc(old('observations', $delivery['observations'] ?? '')) ?></textarea>
            </div>
            <div class="toolbar">
                <button class="btn btn-primary">Guardar datos</button>
                <a class="btn btn-outline" href="<?= site_url('driver/routes/' . $delivery['route_id']) ?>">Volver a la ruta</a>
            </div>
        </form>
    <?php else: ?>
        <div class="toolbar">
            <a class="btn btn-outline" href="<?= site_url('driver/routes/' . $delivery['route_id']) ?>">Volver a la ruta</a>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
