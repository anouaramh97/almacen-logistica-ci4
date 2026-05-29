<?php // Vista de administracion: presenta datos y acciones internas del panel administrador. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$routeStatusOptions = ['planificada', 'en_progreso', 'completada', 'cancelada'];
$selectedRouteStatus = old('status', 'planificada');
$selectedOrderIds = array_map('intval', (array) old('order_ids', []));
$deliveryEstimatedAtValues = (array) old('delivery_estimated_at', []);
?>
<style>
    .is-hidden {
        display: none;
    }
</style>
<section class="summary-card">
    <div class="topbar" style="margin-bottom:1rem;">
        <div class="top-intro"><h1>Nueva ruta</h1><p>Asigna pedidos a un conductor y prepara la salida logistica.</p></div>
    </div>
    <?php $canCreateRoute = ! empty($drivers) && ! empty($orders); ?>
    <?php if (! $canCreateRoute): ?>
        <div class="flash flash-error">
            <?php if (empty($drivers) && empty($orders)): ?>Necesitas al menos un conductor activo y un pedido preparado para crear una ruta.
            <?php elseif (empty($drivers)): ?>No hay conductores disponibles. Crea o activa un usuario con rol conductor.
            <?php else: ?>No hay pedidos disponibles para enrutar. Primero confirma o prepara un pedido.
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= site_url('admin/routes') ?>">
        <?= csrf_field() ?>
        <div class="grid-2">
            <div class="field">
                <label>Conductor</label>
                <select name="driver_id" id="admin_driver_id" required>
                    <option value="">Selecciona un conductor</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?= $driver['id'] ?>" <?= (string) old('driver_id') === (string) $driver['id'] ? 'selected' : '' ?>>
                            <?= esc($driver['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="route-field-warning is-hidden" id="admin-driver-availability-note" style="margin-top:.45rem;color:#b45309;line-height:1.6;"></div>
            </div>
            <div class="field"><label>Codigo de ruta</label><input name="route_code" value="<?= esc(old('route_code')) ?>" required></div>
        </div>
        <div class="field">
            <label>Fecha de salida</label>
            <input type="datetime-local" id="admin_departure_date" name="departure_date" value="<?= esc(old('departure_date')) ?>" required>
            <div class="route-field-warning is-hidden" id="admin-departure-note" style="margin-top:.45rem;color:#b45309;line-height:1.6;"></div>
        </div>
        <div class="field">
            <label>Estado</label>
            <select name="status" required>
                <?php foreach ($routeStatusOptions as $status): ?>
                    <option value="<?= esc($status) ?>" <?= $selectedRouteStatus === $status ? 'selected' : '' ?>>
                        <?= esc(status_label($status)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field"><label>Origen</label><input name="origin" id="admin_origin" value="<?= esc(old('origin')) ?>" required></div>
        <div class="field"><label>Notas</label><textarea name="notes" rows="4"><?= esc(old('notes')) ?></textarea></div>
        <div class="feature-card" style="margin:1rem 0;">
            <strong>Pedidos</strong>
            <div class="route-field-warning is-hidden" id="admin-delivery-time-note" style="margin-top:.45rem;color:#b45309;line-height:1.6;"></div>
            <div class="table-wrap" style="margin-top:1rem;">
                <table>
                    <thead><tr><th></th><th>ID</th><th>Cliente</th><th>Fecha pedido</th><th>Hora estimada de entrega</th><th>Direccion</th><th>Estado</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><input type="checkbox" name="order_ids[]" value="<?= $order['id'] ?>" <?= in_array((int) $order['id'], $selectedOrderIds, true) ? 'checked' : '' ?>></td>
                            <td>#<?= esc($order['id']) ?></td>
                            <td><?= esc($order['customer_name'] ?? 'Sin cliente') ?></td>
                            <td><?= esc(format_order_datetime($order['order_date'] ?? null)) ?></td>
                            <td>
                                <input
                                    type="datetime-local"
                                    name="delivery_estimated_at[<?= esc($order['id']) ?>]"
                                    value="<?= esc($deliveryEstimatedAtValues[$order['id']] ?? '') ?>"
                                    data-delivery-estimated-at
                                    data-order-id="<?= esc($order['id']) ?>"
                                >
                            </td>
                            <td><?= esc($order['delivery_address']) ?></td>
                            <td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <button class="btn btn-primary" <?= $canCreateRoute ? '' : 'disabled' ?>>Crear ruta</button>
    </form>
</section>
    <script>
    (() => {
        const departureInput = document.getElementById('admin_departure_date');
        const departureNote = document.getElementById('admin-departure-note');
        const deliveryTimeNote = document.getElementById('admin-delivery-time-note');
        const driverSelect = document.getElementById('admin_driver_id');
        const originInput = document.getElementById('admin_origin');
        const driverNote = document.getElementById('admin-driver-availability-note');
        const orderCheckboxes = Array.from(document.querySelectorAll('input[name="order_ids[]"]'));
        const deliveryInputs = Array.from(document.querySelectorAll('[data-delivery-estimated-at]'));
        const driverSchedules = <?= json_encode($driverSchedules ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        if (!departureInput || !driverSelect || !originInput) {
            return;
        }

        const localDateTimeValue = (date) => {
            const pad = (value) => String(value).padStart(2, '0');

            return [
                date.getFullYear(),
                pad(date.getMonth() + 1),
                pad(date.getDate()),
            ].join('-') + 'T' + [
                pad(date.getHours()),
                pad(date.getMinutes()),
            ].join(':');
        };

        const addMinutes = (date, minutes) => new Date(date.getTime() + (minutes * 60 * 1000));
        const ceilToMinute = (date) => {
            const rounded = new Date(date);
            if (rounded.getSeconds() > 0 || rounded.getMilliseconds() > 0) {
                rounded.setMinutes(rounded.getMinutes() + 1);
            }
            rounded.setSeconds(0, 0);
            return rounded;
        };

        const toDate = (value) => {
            if (!value) return null;
            const parsed = new Date(String(value).replace(' ', 'T'));
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        };

        const toMillis = (value) => {
            const parsed = toDate(value);
            return parsed ? parsed.getTime() : null;
        };

        const minimumDepartureDate = () => ceilToMinute(addMinutes(new Date(), 30));
        const minimumDeliveryDate = () => {
            const departureDate = toDate(departureInput.value);
            return departureDate ? addMinutes(departureDate, 30) : minimumDepartureDate();
        };

        const setNote = (element, text) => {
            if (!element) return;
            element.textContent = text;
            element.classList.toggle('is-hidden', text === '');
        };

        departureInput.min = localDateTimeValue(minimumDepartureDate());

        const selectedDeliveryTimes = () => {
            return deliveryInputs
                .filter((input) => orderCheckboxes.some((checkbox) => checkbox.value === input.dataset.orderId && checkbox.checked))
                .map((input) => toMillis(input.value))
                .filter((time) => time !== null);
        };

        const latestSelectedDeliveryTime = () => {
            const times = selectedDeliveryTimes();
            return times.length ? Math.max(...times) : null;
        };

        const scheduleDeliveryTimes = (schedule) => {
            return String(schedule.delivery_times || '')
                .split('|')
                .map((value) => toMillis(value))
                .filter((time) => time !== null);
        };

        const scheduleBusyWindow = (schedule) => {
            const departure = toMillis(schedule.departure_date);
            if (departure === null) return null;

            const estimatedArrival = toMillis(schedule.estimated_arrival);
            const endCandidates = [departure, estimatedArrival, ...scheduleDeliveryTimes(schedule)].filter((time) => time !== null);

            return {
                start: departure,
                end: Math.max(...endCandidates) + (30 * 60 * 1000),
            };
        };

        const proposedBusyWindow = () => {
            const departure = toMillis(departureInput.value);
            if (departure === null) return null;

            const arrival = latestSelectedDeliveryTime();

            return {
                start: departure,
                end: Math.max(departure, arrival ?? departure) + (30 * 60 * 1000),
            };
        };

        const overlapsBusyWindow = (schedule, windowToCreate) => {
            const existingWindow = scheduleBusyWindow(schedule);
            if (!existingWindow || !windowToCreate) return false;

            return windowToCreate.start < existingWindow.end && existingWindow.start < windowToCreate.end;
        };

        const dateKey = (value) => {
            const date = toDate(value);
            if (!date) return '';

            return [
                date.getFullYear(),
                String(date.getMonth() + 1).padStart(2, '0'),
                String(date.getDate()).padStart(2, '0'),
            ].join('-');
        };

        const sameDaySchedulesForDriver = (driverId, selectedDate) => {
            const selectedDay = dateKey(selectedDate);
            return driverSchedules.filter((schedule) => {
                return String(schedule.driver_id) === String(driverId) && dateKey(schedule.departure_date) === selectedDay;
            });
        };

        const syncArrivalValidity = () => {
            const minDeparture = minimumDepartureDate();
            const minDelivery = minimumDeliveryDate();
            const windowToCreate = proposedBusyWindow();
            departureInput.min = localDateTimeValue(minDeparture);

            const departureDate = toDate(departureInput.value);
            const departureTooSoon = departureDate !== null && departureDate.getTime() < minDeparture.getTime();

            departureInput.setCustomValidity(departureTooSoon ? 'La fecha de salida debe ser al menos 30 minutos posterior a la hora actual.' : '');
            setNote(departureNote, departureTooSoon ? 'La salida debe ser al menos 30 minutos posterior a la hora actual.' : '');

            const selectedDeliveryInputs = deliveryInputs.filter((input) => {
                return orderCheckboxes.some((item) => item.value === input.dataset.orderId && item.checked);
            });
            const selectedDeliveryTimes = selectedDeliveryInputs
                .map((input) => ({ input, date: toDate(input.value) }))
                .filter((item) => item.date !== null)
                .sort((first, second) => first.date.getTime() - second.date.getTime());
            let deliveryNoteText = '';
            const tooCloseInputs = new Set();

            for (let index = 1; index < selectedDeliveryTimes.length; index++) {
                const previous = selectedDeliveryTimes[index - 1];
                const current = selectedDeliveryTimes[index];
                if (current.date.getTime() - previous.date.getTime() < (30 * 60 * 1000)) {
                    tooCloseInputs.add(previous.input);
                    tooCloseInputs.add(current.input);
                    deliveryNoteText = 'Debe haber al menos 30 minutos entre la hora estimada de cada pedido.';
                    break;
                }
            }

            deliveryInputs.forEach((input) => {
                const checkbox = orderCheckboxes.find((item) => item.value === input.dataset.orderId);
                input.min = localDateTimeValue(minDelivery);
                input.required = Boolean(checkbox && checkbox.checked);
                input.disabled = !input.required;

                if (!input.required) {
                    input.setCustomValidity('');
                    return;
                }

                const deliveryDate = toDate(input.value);
                const deliveryTooSoon = deliveryDate !== null && deliveryDate.getTime() < minDelivery.getTime();
                let error = '';
                if (deliveryTooSoon) {
                    error = 'La hora de entrega debe ser al menos 30 minutos posterior a la salida de la ruta.';
                    deliveryNoteText ||= error;
                } else if (tooCloseInputs.has(input)) {
                    error = 'Debe haber al menos 30 minutos entre la hora estimada de cada pedido.';
                }
                input.setCustomValidity(error);
            });

            setNote(deliveryTimeNote, deliveryNoteText);

            Array.from(driverSelect.options).forEach((option) => {
                if (!option.value) return;

                const unavailableSchedules = driverSchedules.filter((schedule) => String(schedule.driver_id) === option.value && overlapsBusyWindow(schedule, windowToCreate));
                const sameDaySchedules = departureInput.value ? sameDaySchedulesForDriver(option.value, departureInput.value) : [];
                const originalLabel = option.dataset.originalLabel || option.textContent.replace(/\s-\s(No disponible.*|[0-9]+ pedido.*)$/u, '').trim();
                option.dataset.originalLabel = originalLabel;

                if (unavailableSchedules.length) {
                    option.textContent = `${originalLabel} - No disponible (horario ocupado)`;
                    option.disabled = true;
                } else {
                    const deliveryTotal = sameDaySchedules.reduce((total, schedule) => total + Number(schedule.delivery_total || 0), 0);
                    option.textContent = sameDaySchedules.length
                        ? `${originalLabel} - ${deliveryTotal} pedido(s) ese día`
                        : originalLabel;
                    option.disabled = false;
                }
            });

            const selectedOption = Array.from(driverSelect.options).find((option) => option.value === driverSelect.value);
            if (selectedOption && selectedOption.disabled) {
                driverSelect.value = '';
                setNote(driverNote, 'El conductor seleccionado no está disponible para ese horario.');
            } else {
                setNote(driverNote, '');
            }
        };

        departureInput.addEventListener('input', syncArrivalValidity);
        driverSelect.addEventListener('change', syncArrivalValidity);
        originInput.addEventListener('input', syncArrivalValidity);
        orderCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', syncArrivalValidity));
        deliveryInputs.forEach((input) => input.addEventListener('input', syncArrivalValidity));
        syncArrivalValidity();
    })();
</script>
<?= $this->endSection() ?>
