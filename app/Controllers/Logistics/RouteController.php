<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Logistics;

use App\Controllers\BaseController;
use App\Models\DeliveryModel;
use App\Models\OrderModel;
use App\Models\RouteModel;
use App\Models\UserModel;
use App\Models\WarehouseModel;

/**
 * Coordina las pantallas y acciones del modulo de logistica.
 */
class RouteController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $routes = model(RouteModel::class)->listLogistics();

        return $this->render('logistics/routes/index', ['routes' => $routes]);
    }

    /**
     * Prepara el formulario de alta con los datos auxiliares necesarios.
     */
    public function create(): string
    {
        $routeModel = model(RouteModel::class);
        $drivers = model(UserModel::class)->listDrivers();
        $orders = model(OrderModel::class)->listAssignableOrders(true);
        $routeCode = $routeModel->nextRouteCode();

        return $this->render('logistics/routes/form', [
            'drivers' => $drivers,
            'orders' => $orders,
            'routeCode' => $routeCode,
            'driverSchedules' => $routeModel->listSchedulesForDrivers(),
            'warehouses' => model(WarehouseModel::class)->listAllByName(),
        ]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate(['driver_id' => 'required|integer', 'departure_date' => 'required', 'origin' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos de la ruta.');
        }

        $now = date('Y-m-d H:i:s');
        $routeModel = model(RouteModel::class);
        $driverId = (int) $this->request->getPost('driver_id');
        $departureDate = (string) $this->request->getPost('departure_date');
        $origin = (string) $this->request->getPost('origin');
        $orderIds = array_values(array_unique(array_map('intval', (array) ($this->request->getPost('order_ids') ?? []))));
        $departureTime = strtotime($departureDate);
        $minimumDepartureTime = time() + 30 * 60;
        $minimumDepartureTime += (60 - ($minimumDepartureTime % 60)) % 60;

        if ($departureTime === false) {
            return redirect()->back()->withInput()->with('error', 'Revisa la fecha de salida de la ruta.');
        }

        if ($departureTime < $minimumDepartureTime) {
            return redirect()->back()->withInput()->with('error', 'La fecha de salida debe ser al menos 30 minutos posterior a la hora actual.');
        }

        $deliveryPayload = $this->deliveryEstimatedAtPayload($orderIds, $departureTime);
        if (isset($deliveryPayload['error'])) {
            return redirect()->back()->withInput()->with('error', $deliveryPayload['error']);
        }

        $deliveryEstimatedAt = $deliveryPayload['values'];
        $estimatedArrival = $deliveryPayload['estimated_arrival'];
        $destination = $this->routeDestinationFromOrders($orderIds);

        if ($routeModel->hasDriverScheduleConflict($driverId, $departureDate, $estimatedArrival, $origin, array_values($deliveryEstimatedAt))) {
            return redirect()->back()->withInput()->with('error', 'Ese conductor ya tiene una ruta activa en ese horario. Elige una hora al menos 30 minutos posterior a su ultima entrega u otro conductor.');
        }

        model(RouteModel::class)->createRouteWithDeliveries([
            'driver_id' => $driverId,
            'route_code' => $routeModel->nextRouteCode(),
            'departure_date' => $departureDate,
            'estimated_arrival' => $estimatedArrival,
            'status' => $this->request->getPost('status') ?: 'planificada',
            'origin' => $origin,
            'destination' => $destination,
            'notes' => $this->request->getPost('notes'),
            'created_at' => $now,
            'updated_at' => $now,
        ], $orderIds, $deliveryEstimatedAt);

        return redirect()->to(site_url('logistics/routes'))->with('success', 'Ruta creada correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function deliveryEstimatedAtPayload(array $orderIds, int $departureTime): array
    {
        $postedValues = (array) ($this->request->getPost('delivery_estimated_at') ?? []);
        $deliveryEstimatedAt = [];
        $usedTimes = [];
        $latestTime = null;

        foreach ($orderIds as $orderId) {
            $value = trim((string) ($postedValues[$orderId] ?? ''));

            if ($value === '') {
                return ['error' => 'Selecciona una hora estimada de entrega para cada pedido de la ruta.'];
            }

            $time = strtotime($value);
            if ($time === false) {
                return ['error' => 'Revisa las horas estimadas de entrega de los pedidos.'];
            }

            if ($time - $departureTime < 30 * 60) {
                return ['error' => 'Cada hora de entrega debe ser al menos 30 minutos posterior a la salida de la ruta.'];
            }

            $usedTimes[] = $time;
            $latestTime = $latestTime === null ? $time : max($latestTime, $time);
            $deliveryEstimatedAt[$orderId] = date('Y-m-d H:i:s', $time);
        }

        sort($usedTimes);
        for ($index = 1, $total = count($usedTimes); $index < $total; $index++) {
            if ($usedTimes[$index] - $usedTimes[$index - 1] < 30 * 60) {
                return ['error' => 'Debe haber al menos 30 minutos entre la hora estimada de cada pedido.'];
            }
        }

        return [
            'values' => $deliveryEstimatedAt,
            'estimated_arrival' => $latestTime !== null ? date('Y-m-d H:i:s', $latestTime) : null,
        ];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function routeDestinationFromOrders(array $orderIds): string
    {
        if (count($orderIds) > 1) {
            return 'Varios destinos';
        }

        $orderId = (int) ($orderIds[0] ?? 0);
        if ($orderId <= 0) {
            return 'Sin destino';
        }

        $order = model(OrderModel::class)->find($orderId);
        $destination = trim((string) ($order['delivery_address'] ?? ''));

        return $destination !== '' ? $destination : 'Sin destino';
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $route = model(RouteModel::class)->findWithDriver((int) $id);
        $deliveries = model(DeliveryModel::class)->forRouteDetailed((int) $id);
        return $this->render('logistics/routes/show', ['route' => $route, 'deliveries' => $deliveries]);
    }
}
