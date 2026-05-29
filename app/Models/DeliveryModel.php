<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class DeliveryModel extends Model
{
    protected $table = 'deliveries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['order_id', 'route_id', 'estimated_delivery_at', 'departed_at', 'status', 'recipient_name', 'observations', 'delivered_at', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function existsForOrder(int $orderId): bool
    {
        return $this->where('order_id', $orderId)->first() !== null;
    }

    /**
     * Obtiene datos vinculados a una entidad concreta.
     */
    public function forRouteDetailed(int $routeId): array
    {
        return $this->select('deliveries.*, orders.delivery_address, orders.order_date, orders.status as order_status, users.name as customer_name')
            ->join('orders', 'orders.id = deliveries.order_id')
            ->join('users', 'users.id = orders.customer_id')
            ->where('deliveries.route_id', $routeId)
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForDriverDetail(int $id, int $driverId): ?array
    {
        return $this->select('deliveries.*, orders.delivery_address, orders.order_date, users.name as customer_name, routes.departure_date')
            ->join('orders', 'orders.id = deliveries.order_id')
            ->join('users', 'users.id = orders.customer_id')
            ->join('routes', 'routes.id = deliveries.route_id')
            ->where('deliveries.id', $id)
            ->where('routes.driver_id', $driverId)
            ->first();
    }

    /**
     * Devuelve las entregas visibles para un cliente.
     */
    public function listForCustomer(int $customerId): array
    {
        return $this->select('deliveries.*, orders.status as order_status, orders.order_date, orders.delivery_address, orders.total, routes.route_code, routes.departure_date, routes.estimated_arrival, driver.name as driver_name, driver.phone as driver_phone')
            ->join('orders', 'orders.id = deliveries.order_id')
            ->join('routes', 'routes.id = deliveries.route_id')
            ->join('users driver', 'driver.id = routes.driver_id', 'left')
            ->where('orders.customer_id', $customerId)
            ->orderBy('deliveries.id', 'DESC')
            ->findAll();
    }

    /**
     * Busca una entrega concreta de un cliente.
     */
    public function findForCustomerDetail(int $id, int $customerId): ?array
    {
        return $this->select('deliveries.*, orders.status as order_status, orders.order_date, orders.delivery_address, orders.total, routes.route_code, routes.departure_date, routes.estimated_arrival, routes.origin, routes.destination, routes.status as route_status, driver.name as driver_name, driver.phone as driver_phone, driver.email as driver_email')
            ->join('orders', 'orders.id = deliveries.order_id')
            ->join('routes', 'routes.id = deliveries.route_id')
            ->join('users driver', 'driver.id = routes.driver_id', 'left')
            ->where('deliveries.id', $id)
            ->where('orders.customer_id', $customerId)
            ->first();
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
    public function countPendingByDriver(int $driverId): int
    {
        return $this->join('routes', 'routes.id = deliveries.route_id')
            ->where('routes.driver_id', $driverId)
            ->where('deliveries.status', 'pendiente')
            ->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countDeliveredByDriver(int $driverId): int
    {
        return $this->join('routes', 'routes.id = deliveries.route_id')
            ->where('routes.driver_id', $driverId)
            ->where('deliveries.status', 'entregada')
            ->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countsByRouteIds(array $routeIds): array
    {
        if ($routeIds === []) {
            return [];
        }

        $rows = $this->select('route_id, COUNT(*) as total')
            ->whereIn('route_id', $routeIds)
            ->groupBy('route_id')
            ->findAll();

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['route_id']] = $row['total'];
        }

        return $counts;
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listStatusesForRoute(int $routeId): array
    {
        $rows = $this->select('status')
            ->where('route_id', $routeId)
            ->findAll();

        $statuses = [];
        foreach ($rows as $row) {
            $statuses[] = (string) ($row['status'] ?? '');
        }

        return $statuses;
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateDeliveryProgress(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function removeForOrder(int $orderId): array
    {
        $delivery = $this->where('order_id', $orderId)->first();

        if (! $delivery) {
            return ['removed' => false, 'route_id' => null];
        }

        $routeId = (int) ($delivery['route_id'] ?? 0);
        $this->delete((int) $delivery['id']);

        return ['removed' => true, 'route_id' => $routeId > 0 ? $routeId : null];
    }
}
