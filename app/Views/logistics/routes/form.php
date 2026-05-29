<?php // Vista de logistica: ayuda a coordinar pedidos, rutas y entregas operativas. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$selectedOrderIds = array_map('intval', (array) old('order_ids', []));
$deliveryEstimatedAtValues = (array) old('delivery_estimated_at', []);

$fullCustomerDestination = static function (array $order): string {
    $deliveryAddress = trim((string) ($order['delivery_address'] ?? ''));
    $deliveryParts = array_values(array_filter(array_map('trim', explode(',', $deliveryAddress)), static fn (string $part): bool => $part !== ''));

    if (count($deliveryParts) >= 3) {
        return implode(', ', $deliveryParts);
    }

    $profileParts = array_values(array_filter([
        trim((string) ($order['customer_address'] ?? '')),
        trim((string) ($order['customer_city'] ?? '')),
        trim((string) ($order['customer_postal_code'] ?? '')),
    ], static fn (string $part): bool => $part !== ''));

    if ($profileParts !== []) {
        return implode(', ', $profileParts);
    }

    return $deliveryAddress;
};
?>
<section class="card form-card">
    <style>
        .route-code-preview {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .95rem 1rem;
            border-radius: 18px;
            background: rgba(248, 251, 255, .96);
            border: 1px solid rgba(15, 23, 42, .08);
            font-weight: 700;
        }

        .route-orders-note,
        .route-driver-note {
            margin-top: .45rem;
            color: #6e7c90;
            line-height: 1.6;
        }

        .route-field-warning {
            color: #b45309;
        }

        .is-hidden {
            display: none;
        }

        .route-driver-status {
            margin-top: .6rem;
            padding: .9rem 1rem;
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, .08);
            background: rgba(248, 251, 255, .96);
            color: #334155;
        }

    </style>

    <div class="heading">
        <h2>Nueva ruta logística</h2>
        <p>Asigna conductor, salida y pedidos pendientes en un único formulario operativo.</p>
    </div>

    <form method="post" action="<?= site_url('logistics/routes') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label>Salida</label>
            <input type="datetime-local" id="departure_date" name="departure_date" value="<?= esc(old('departure_date')) ?>" required>
            <div class="route-orders-note route-field-warning is-hidden" id="departure-date-note"></div>
        </div>

        <div class="field">
            <label>Pedidos</label>
            <div class="route-orders-note route-field-warning is-hidden" id="delivery-time-note"></div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha pedido</th>
                            <th>Hora estimada de entrega</th>
                            <th>Dirección indicada por cliente</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php $customerDestination = $fullCustomerDestination($order); ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        name="order_ids[]"
                                        value="<?= $order['id'] ?>"
                                        <?= in_array((int) $order['id'], $selectedOrderIds, true) ? 'checked' : '' ?>
                                    >
                                </td>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= esc($order['customer_name']) ?></td>
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
                                <td><?= esc($customerDestination !== '' ? $customerDestination : 'Sin dirección indicada') ?></td>
                                <td><span class="pill <?= esc(order_status_class($order['status'])) ?>"><?= esc(status_label($order['status'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Conductor</label>
                <select name="driver_id" id="driver_id" required>
                    <option value="">Selecciona un conductor</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?= $driver['id'] ?>" <?= (string) old('driver_id') === (string) $driver['id'] ? 'selected' : '' ?>>
                            <?= esc($driver['name']) ?>
                        </option>
	                    <?php endforeach; ?>
	                </select>
	                <div class="route-driver-note route-field-warning is-hidden" id="driver-availability-note"></div>
	            </div>
	            <div class="field">
	                <label>Código de ruta</label>
	                <div class="route-code-preview">
	                    <span><?= esc($routeCode ?? 'Se generará al guardar') ?></span>
	                </div>
	            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Origen</label>
                <select name="origin" required>
                    <option value="">Selecciona una dirección de salida</option>
                    <?php foreach (($warehouses ?? []) as $warehouse): ?>
                        <?php $originAddress = trim(implode(', ', array_filter([
                            $warehouse['name'] ?? '',
                            $warehouse['address'] ?? '',
                            $warehouse['city'] ?? '',
                            $warehouse['postal_code'] ?? '',
                        ]))); ?>
                        <option value="<?= esc($originAddress) ?>" <?= old('origin') === $originAddress ? 'selected' : '' ?>>
                            <?= esc($originAddress) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field">
            <label>Notas</label>
            <textarea name="notes"><?= esc(old('notes')) ?></textarea>
        </div>

        <div class="toolbar">
            <button class="btn btn-primary">Crear ruta</button>
            <a class="btn btn-outline" href="<?= site_url('logistics/routes') ?>">Cancelar</a>
        </div>
    </form>
</section>
<script>
    (() => {
        const checkboxes = Array.from(document.querySelectorAll('input[name="order_ids[]"]'));
        const deliveryInputs = Array.from(document.querySelectorAll('[data-delivery-estimated-at]'));
        const departureInput = document.getElementById('departure_date');
        const driverSelect = document.getElementById('driver_id');
        const originSelect = document.querySelector('select[name="origin"]');
        const driverNote = document.getElementById('driver-availability-note');
        const departureDateNote = document.getElementById('departure-date-note');
        const deliveryTimeNote = document.getElementById('delivery-time-note');
        const driverSchedules = <?= json_encode($driverSchedules ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

        if (!departureInput || !driverSelect || !originSelect) {
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
        const minimumDepartureDate = () => ceilToMinute(addMinutes(new Date(), 30));
        const minimumDeliveryDate = () => {
            const departureDate = parseDateTime(departureInput.value);
            return departureDate ? addMinutes(departureDate, 30) : minimumDepartureDate();
        };

        const parseDateTime = (value) => {
            if (!value) return null;
            const parsed = new Date(String(value).replace(' ', 'T'));
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        };

        departureInput.min = localDateTimeValue(minimumDepartureDate());

        const toMillis = (value) => {
            const parsed = parseDateTime(value);
            return parsed ? parsed.getTime() : null;
        };

        const latestSelectedDeliveryTime = () => {
            const selectedTimes = deliveryInputs
                .filter((input) => checkboxes.some((checkbox) => checkbox.value === input.dataset.orderId && checkbox.checked))
                .map((input) => toMillis(input.value))
                .filter((time) => time !== null);

            return selectedTimes.length ? Math.max(...selectedTimes) : null;
        };

        const selectedDeliveryTimes = () => {
            return deliveryInputs
                .filter((input) => checkboxes.some((checkbox) => checkbox.value === input.dataset.orderId && checkbox.checked))
                .map((input) => toMillis(input.value))
                .filter((time) => time !== null);
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

            const existingDeliveries = scheduleDeliveryTimes(schedule);
            const estimatedArrival = toMillis(schedule.estimated_arrival);
            const endCandidates = [departure, estimatedArrival, ...existingDeliveries].filter((time) => time !== null);

            return {
                start: departure,
                end: Math.max(...endCandidates) + (30 * 60 * 1000),
            };
        };

        const proposedBusyWindow = () => {
            const departure = toMillis(departureInput.value);
            if (departure === null) return null;

            const arrival = latestSelectedDeliveryTime();
            const end = Math.max(departure, arrival ?? departure) + (30 * 60 * 1000);

            return { start: departure, end };
        };

        const overlapsBusyWindow = (schedule, proposedWindow) => {
            const existingWindow = scheduleBusyWindow(schedule);
            if (!existingWindow || !proposedWindow) return false;

            return proposedWindow.start < existingWindow.end && existingWindow.start < proposedWindow.end;
        };

        const dateKey = (value) => {
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return '';
            return [
                date.getFullYear(),
                String(date.getMonth() + 1).padStart(2, '0'),
                String(date.getDate()).padStart(2, '0'),
            ].join('-');
        };

        const timeLabel = (value) => {
            const date = new Date(String(value).replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return '';
            return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        };

        const sameDaySchedulesForDriver = (driverId, selectedDate) => {
            const selectedDay = dateKey(selectedDate);
            return driverSchedules.filter((schedule) => {
                return String(schedule.driver_id) === String(driverId) && dateKey(schedule.departure_date) === selectedDay;
            });
        };

        const setNote = (element, text) => {
            if (!element) return;
            element.textContent = text;
            element.classList.toggle('is-hidden', text === '');
        };

        const scheduleSummary = (schedules) => {
            if (!schedules.length) {
                return 'Sin rutas ese día.';
            }

            return schedules.map((schedule) => {
                const deliveryTotal = Number(schedule.delivery_total || 0);
                const orderLabel = deliveryTotal === 1 ? '1 pedido' : `${deliveryTotal} pedidos`;
                return `${timeLabel(schedule.estimated_arrival)} ${schedule.route_code || 'Ruta'} (${orderLabel})`;
            }).join(' | ');
        };

        const syncDriverAvailability = () => {
            const minDeparture = minimumDepartureDate();
            const minDelivery = minimumDeliveryDate();
            const proposedDeparture = departureInput.value;
            const windowToCreate = proposedBusyWindow();
            const proposedDepartureTime = toMillis(proposedDeparture);
            const selectedDriver = driverSelect.value;
            const departureTooSoon = proposedDepartureTime !== null && proposedDepartureTime < minDeparture.getTime();

            departureInput.min = localDateTimeValue(minDeparture);
            departureInput.setCustomValidity(departureTooSoon ? 'La fecha de salida debe ser al menos 30 minutos posterior a la hora actual.' : '');
            setNote(departureDateNote, departureTooSoon ? 'La salida debe ser al menos 30 minutos posterior a la hora actual.' : '');

            const selectedDeliveryInputs = deliveryInputs.filter((input) => {
                return checkboxes.some((item) => item.value === input.dataset.orderId && item.checked);
            });
            const selectedDeliveryTimes = selectedDeliveryInputs
                .map((input) => ({ input, time: toMillis(input.value) }))
                .filter((item) => item.time !== null)
                .sort((first, second) => first.time - second.time);
            let deliveryNoteText = '';
            const tooCloseInputs = new Set();

            for (let index = 1; index < selectedDeliveryTimes.length; index++) {
                const previous = selectedDeliveryTimes[index - 1];
                const current = selectedDeliveryTimes[index];
                if (current.time - previous.time < (30 * 60 * 1000)) {
                    tooCloseInputs.add(previous.input);
                    tooCloseInputs.add(current.input);
                    deliveryNoteText = 'Debe haber al menos 30 minutos entre la hora estimada de cada pedido.';
                    break;
                }
            }

            deliveryInputs.forEach((input) => {
                const checkbox = checkboxes.find((item) => item.value === input.dataset.orderId);
                input.min = localDateTimeValue(minDelivery);
                input.required = Boolean(checkbox && checkbox.checked);
                input.disabled = !input.required;

                if (!input.required) {
                    input.setCustomValidity('');
                    return;
                }

                const deliveryTime = toMillis(input.value);
                const minimumDeliveryTime = minDelivery.getTime();
                const deliveryTooSoon = deliveryTime !== null && deliveryTime < minimumDeliveryTime;
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
                const sameDaySchedules = proposedDeparture ? sameDaySchedulesForDriver(option.value, proposedDeparture) : [];
                const originalLabel = option.dataset.originalLabel || option.textContent.replace(/\s[-|]\s(Disponible|No disponible.*|[0-9]+ pedido.*)$/u, '').trim();
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

            if (!proposedDeparture) {
                setNote(driverNote, '');
                return;
            }

            if (selectedDriver) {
                const selectedOption = Array.from(driverSelect.options).find((option) => option.value === selectedDriver);
                if (selectedOption && selectedOption.disabled) {
                    driverSelect.value = '';
                    setNote(driverNote, 'El conductor seleccionado no está disponible para ese horario.');
                    return;
                }
            }

            const activeDriver = driverSelect.value;
            if (!activeDriver) {
                setNote(driverNote, '');
                return;
            }

            const selectedSchedules = sameDaySchedulesForDriver(activeDriver, proposedDeparture);
            setNote(driverNote, selectedSchedules.length ? `Rutas de ese día: ${scheduleSummary(selectedSchedules)}` : '');
        };

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', syncDriverAvailability);
        });

        departureInput.addEventListener('input', syncDriverAvailability);
        deliveryInputs.forEach((input) => input.addEventListener('input', syncDriverAvailability));
        driverSelect.addEventListener('change', syncDriverAvailability);
        originSelect.addEventListener('change', syncDriverAvailability);

        syncDriverAvailability();
    })();
</script>
<?= $this->endSection() ?>
