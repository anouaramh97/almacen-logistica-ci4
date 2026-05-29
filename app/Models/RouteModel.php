<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class RouteModel extends Model
{
    private const DRIVER_RETURN_BUFFER_MINUTES = 30;

    protected $table = 'routes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['driver_id', 'route_code', 'departure_date', 'estimated_arrival', 'status', 'origin', 'destination', 'notes', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAdmin(): array
    {
        $routes = $this->select('routes.*, users.name as driver_name')
            ->join('users', 'users.id = routes.driver_id')
            ->orderBy('routes.id', 'DESC')
            ->findAll();

        return $this->withOrderSummaries($routes);
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listLogistics(): array
    {
        $routes = $this->select('routes.*, users.name as driver_name, COUNT(deliveries.id) as delivery_total')
            ->join('users', 'users.id = routes.driver_id')
            ->join('deliveries', 'deliveries.route_id = routes.id', 'left')
            ->groupBy('routes.id')
            ->orderBy('routes.id', 'DESC')
            ->findAll();

        return $this->withOrderSummaries($routes);
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForDriver(int $driverId): array
    {
        $routes = $this->select('routes.*, COUNT(deliveries.id) as delivery_total')
            ->join('deliveries', 'deliveries.route_id = routes.id', 'left')
            ->where('driver_id', $driverId)
            ->groupBy('routes.id')
            ->orderBy('departure_date', 'DESC')
            ->findAll();

        return $this->withOrderSummaries($routes);
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listRecentWithDriver(int $limit = 5): array
    {
        $routes = $this->select('routes.*, users.name as driver_name')
            ->join('users', 'users.id = routes.driver_id', 'left')
            ->orderBy('routes.id', 'DESC')
            ->limit($limit)
            ->findAll();

        return $this->withOrderSummaries($routes);
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listRecentForDriver(int $driverId, int $limit = 5): array
    {
        $routes = $this->where('driver_id', $driverId)->orderBy('departure_date', 'DESC')->limit($limit)->findAll();

        return $this->withOrderSummaries($routes);
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithDriver(int $id, bool $withEmail = false): ?array
    {
        $select = 'routes.*, users.name as driver_name';
        if ($withEmail) {
            $select .= ', users.email as driver_email';
        }

        return $this->select($select)
            ->join('users', 'users.id = routes.driver_id')
            ->where('routes.id', $id)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForDriver(int $id, int $driverId): ?array
    {
        return $this->where('id', $id)->where('driver_id', $driverId)->first();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countForDriver(int $driverId, ?string $status = null): int
    {
        $builder = $this->where('driver_id', $driverId);
        if ($status !== null) {
            $builder->where('status', $status);
        }

        return $builder->countAllResults();
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createRouteWithDeliveries(array $routeData, array $orderIds, array $deliveryEstimatedAt = []): int
    {
        $this->db->transStart();
        $this->insert($routeData);
        $routeId = (int) $this->getInsertID();

        $deliveryModel = model(DeliveryModel::class);
        foreach ($orderIds as $orderId) {
            if (! $deliveryModel->existsForOrder((int) $orderId)) {
                $deliveryModel->insert([
                    'order_id' => $orderId,
                    'route_id' => $routeId,
                    'estimated_delivery_at' => $deliveryEstimatedAt[(int) $orderId] ?? null,
                    'status' => 'pendiente',
                    'created_at' => $routeData['created_at'],
                    'updated_at' => $routeData['updated_at'],
                ]);
            }
        }

        $this->db->transComplete();

        return $routeId;
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteRouteWithDeliveries(int $routeId): array
    {
        $route = $this->find($routeId);

        if (! $route) {
            return ['success' => false, 'message' => 'Ruta no encontrada.'];
        }

        $deliveryModel = model(DeliveryModel::class);
        $orderModel = model(OrderModel::class);
        $deliveries = $deliveryModel->where('route_id', $routeId)->findAll();

        $this->db->transStart();

        foreach ($deliveries as $delivery) {
            $orderId = (int) ($delivery['order_id'] ?? 0);
            if ($orderId > 0) {
                $order = $orderModel->find($orderId);

                if (($order['status'] ?? null) === 'en_ruta') {
                    $orderModel->updateStatus($orderId, 'preparando');
                }
            }
        }

        $deliveryModel->where('route_id', $routeId)->delete();
        $this->delete($routeId);

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return ['success' => false, 'message' => 'No se pudo eliminar la ruta.'];
        }

        return ['success' => true, 'message' => 'Ruta eliminada correctamente.'];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function nextRouteCode(): string
    {
        $latest = $this->select('id')->orderBy('id', 'DESC')->first();
        $nextId = (int) ($latest['id'] ?? 0) + 1;

        return 'R-' . date('Ymd') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listSchedulesForDrivers(): array
    {
        return $this->select("routes.id, routes.driver_id, routes.route_code, routes.departure_date, routes.estimated_arrival, routes.status, routes.origin, COUNT(deliveries.id) as delivery_total, GROUP_CONCAT(COALESCE(deliveries.delivered_at, deliveries.estimated_delivery_at) ORDER BY COALESCE(deliveries.delivered_at, deliveries.estimated_delivery_at) SEPARATOR '|') as delivery_times", false)
            ->join('deliveries', 'deliveries.route_id = routes.id', 'left')
            ->whereIn('routes.status', ['planificada', 'en_progreso'])
            ->groupBy('routes.id')
            ->orderBy('routes.departure_date', 'ASC')
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function hasDriverScheduleConflict(int $driverId, string $departureDate, ?string $estimatedArrival = null, ?string $origin = null, array $proposedDeliveryTimes = []): bool
    {
        $proposedBusyWindow = $this->routeBusyWindow($departureDate, $estimatedArrival, $proposedDeliveryTimes);
        if ($proposedBusyWindow === null) {
            return false;
        }

        [$proposedStart, $proposedEnd] = $proposedBusyWindow;
        $routes = $this->select("routes.id, routes.departure_date, routes.estimated_arrival, routes.origin, GROUP_CONCAT(COALESCE(deliveries.delivered_at, deliveries.estimated_delivery_at) ORDER BY COALESCE(deliveries.delivered_at, deliveries.estimated_delivery_at) SEPARATOR '|') as delivery_times", false)
            ->join('deliveries', 'deliveries.route_id = routes.id', 'left')
            ->where('driver_id', $driverId)
            ->whereIn('routes.status', ['planificada', 'en_progreso'])
            ->groupBy('routes.id')
            ->findAll();

        foreach ($routes as $route) {
            $existingBusyWindow = $this->routeBusyWindow(
                (string) ($route['departure_date'] ?? ''),
                $route['estimated_arrival'] ?? null,
                explode('|', (string) ($route['delivery_times'] ?? ''))
            );
            if ($existingBusyWindow === null) {
                continue;
            }

            [$existingStart, $existingEnd] = $existingBusyWindow;
            if ($proposedStart < $existingEnd && $existingStart < $proposedEnd) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcula la franja ocupada de una ruta, incluyendo 30 minutos tras la ultima entrega.
     */
    private function routeBusyWindow(string $departureDate, ?string $estimatedArrival = null, array $deliveryTimes = []): ?array
    {
        $departureTime = strtotime($departureDate);
        if ($departureTime === false) {
            return null;
        }

        $latestTime = $this->latestRouteTime($departureTime, $estimatedArrival, $deliveryTimes);

        return [$departureTime, $latestTime + self::DRIVER_RETURN_BUFFER_MINUTES * 60];
    }

    /**
     * Obtiene la hora final real o estimada mas tardia de la ruta.
     */
    private function latestRouteTime(int $departureTime, ?string $estimatedArrival = null, array $deliveryTimes = []): int
    {
        $latestTime = $departureTime;
        $arrivalTime = strtotime((string) $estimatedArrival);
        if ($arrivalTime !== false) {
            $latestTime = max($latestTime, $arrivalTime);
        }

        foreach ($deliveryTimes as $deliveryTime) {
            $time = strtotime((string) $deliveryTime);
            if ($time !== false) {
                $latestTime = max($latestTime, $time);
            }
        }

        return $latestTime;
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateRouteStatus(int $routeId, string $status): bool
    {
        return $this->update($routeId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Sincroniza ruta, entregas y pedidos cuando el admin cambia el estado.
     */
    public function updateRouteStatusFromAdmin(int $routeId, string $status): array
    {
        $route = $this->find($routeId);

        if (! $route) {
            return ['success' => false, 'message' => 'Ruta no encontrada.'];
        }

        $statusMap = [
            'planificada' => ['delivery' => 'pendiente', 'order' => 'preparando', 'clear_delivery_finish' => true, 'clear_departure' => true],
            'en_progreso' => ['delivery' => 'en_transito', 'order' => 'en_ruta', 'clear_delivery_finish' => true, 'clear_departure' => false],
            'completada' => ['delivery' => 'entregada', 'order' => 'entregado', 'clear_delivery_finish' => false, 'clear_departure' => false],
            'cancelada' => ['delivery' => 'fallida', 'order' => 'cancelado', 'clear_delivery_finish' => true, 'clear_departure' => true],
        ];

        if (! isset($statusMap[$status])) {
            return ['success' => false, 'message' => 'Selecciona un estado valido para la ruta.'];
        }

        $now = date('Y-m-d H:i:s');
        $deliveryModel = model(DeliveryModel::class);
        $orderModel = model(OrderModel::class);
        $deliveries = $deliveryModel->where('route_id', $routeId)->findAll();

        $this->db->transBegin();

        $this->update($routeId, [
            'status' => $status,
            'updated_at' => $now,
        ]);

        foreach ($deliveries as $delivery) {
            $deliveryData = [
                'status' => $statusMap[$status]['delivery'],
                'updated_at' => $now,
            ];

            if ($statusMap[$status]['clear_delivery_finish']) {
                $deliveryData['delivered_at'] = null;
                $deliveryData['recipient_name'] = null;
                $deliveryData['observations'] = null;
            }

            if ($statusMap[$status]['clear_departure']) {
                $deliveryData['departed_at'] = null;
            } elseif ($status === 'en_progreso' && empty($delivery['departed_at'])) {
                $deliveryData['departed_at'] = $now;
            } elseif ($status === 'completada' && empty($delivery['delivered_at'])) {
                if (empty($delivery['departed_at'])) {
                    $deliveryData['departed_at'] = $now;
                }
                $deliveryData['delivered_at'] = $now;
            }

            $deliveryModel->update((int) $delivery['id'], $deliveryData);
            $orderModel->updateStatus((int) $delivery['order_id'], $statusMap[$status]['order'], $now);
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();

            return ['success' => false, 'message' => 'No se pudo actualizar el estado de la ruta.'];
        }

        $this->db->transCommit();

        return ['success' => true, 'message' => 'Estado de la ruta actualizado correctamente.'];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function syncStatusFromDeliveries(int $routeId): string
    {
        $deliveryModel = model(DeliveryModel::class);
        $statuses = $deliveryModel->listStatusesForRoute($routeId);

        if ($statuses === []) {
            $this->updateRouteStatus($routeId, 'planificada');
            return 'planificada';
        }

        $hasInTransit = in_array('en_transito', $statuses, true);
        $allPending = true;
        $allFinished = true;

        foreach ($statuses as $status) {
            if ($status !== 'pendiente') {
                $allPending = false;
            }

            if ($status !== 'entregada' && $status !== 'fallida') {
                $allFinished = false;
            }
        }

        if ($allPending) {
            $this->updateRouteStatus($routeId, 'planificada');
            return 'planificada';
        }

        if ($allFinished) {
            $this->updateRouteStatus($routeId, 'completada');
            return 'completada';
        }

        if ($hasInTransit || ! $allPending) {
            $this->updateRouteStatus($routeId, 'en_progreso');
            return 'en_progreso';
        }

        $this->updateRouteStatus($routeId, 'planificada');
        return 'planificada';
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function withOrderSummaries(array $routes): array
    {
        if ($routes === []) {
            return [];
        }

        $routeIds = array_values(array_filter(array_map(static fn ($route) => (int) ($route['id'] ?? 0), $routes)));

        if ($routeIds === []) {
            return $routes;
        }

        $rows = $this->db->table('deliveries')
            ->select('deliveries.route_id, deliveries.order_id, orders.order_date, users.name as customer_name')
            ->join('orders', 'orders.id = deliveries.order_id', 'left')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->whereIn('deliveries.route_id', $routeIds)
            ->orderBy('deliveries.order_id', 'ASC')
            ->get()
            ->getResultArray();

        $summariesByRoute = [];

        foreach ($rows as $row) {
            $routeId = (int) ($row['route_id'] ?? 0);
            $orderId = (int) ($row['order_id'] ?? 0);

            if ($routeId <= 0 || $orderId <= 0) {
                continue;
            }

            $customerName = trim((string) ($row['customer_name'] ?? ''));
            $summariesByRoute[$routeId][] = [
                'order_id' => $orderId,
                'customer_name' => $customerName !== '' ? $customerName : 'Sin cliente',
                'order_date' => $row['order_date'] ?? null,
            ];
        }

        foreach ($routes as &$route) {
            $routeId = (int) ($route['id'] ?? 0);
            $summaries = $summariesByRoute[$routeId] ?? [];

            $route['order_summaries'] = $summaries;
            $route['order_customer_summary'] = implode(', ', array_map(
                static fn ($summary) => '#' . $summary['order_id'] . ' - ' . $summary['customer_name'],
                $summaries
            ));

            if (! array_key_exists('delivery_total', $route)) {
                $route['delivery_total'] = count($summaries);
            }
        }
        unset($route);

        return $routes;
    }
}
