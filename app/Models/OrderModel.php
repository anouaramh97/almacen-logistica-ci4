<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['customer_id', 'order_date', 'status', 'total', 'delivery_address', 'notes', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listRecentWithCustomer(int $limit = 5): array
    {
        return $this->select('orders.*, users.name as customer_name')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->orderBy('orders.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAllWithCustomer(): array
    {
        return $this->select('orders.*, users.name as customer_name')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAllWithCustomerAndInvoice(): array
    {
        return $this->select('orders.*, users.name as customer_name, invoices.id as invoice_id, invoices.invoice_number')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->join('invoices', 'invoices.order_id = orders.id', 'left')
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForCustomerWithInvoice(int $customerId): array
    {
        return $this->select('orders.*, invoices.id as invoice_id, invoices.invoice_number, deliveries.id as delivery_id')
            ->join('invoices', 'invoices.order_id = orders.id', 'left')
            ->join('deliveries', 'deliveries.order_id = orders.id', 'left')
            ->where('customer_id', $customerId)
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listRecentForCustomer(int $customerId, int $limit = 5): array
    {
        return $this->where('customer_id', $customerId)->orderBy('id', 'DESC')->limit($limit)->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAssignableOrders(bool $withCustomerName = false): array
    {
        $builder = $this;
        if ($withCustomerName) {
            $builder = $builder->select('orders.*, users.name as customer_name, users.address as customer_address, users.city as customer_city, users.postal_code as customer_postal_code')
                ->join('users', 'users.id = orders.customer_id');
        }

        return $builder->join('deliveries', 'deliveries.order_id = orders.id', 'left')
            ->whereIn('orders.status', ['confirmado', 'preparando'])
            ->where('deliveries.id', null)
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForLogisticsWithInvoice(): array
    {
        return $this->select('orders.*, users.name as customer_name, invoices.id as invoice_id, invoices.invoice_number')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->join('invoices', 'invoices.order_id = orders.id', 'left')
            ->whereIn('orders.status', ['confirmado', 'preparando', 'en_ruta', 'entregado', 'cancelado'])
            ->orderBy('orders.id', 'DESC')
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForLogisticsWithInvoice(int $id): ?array
    {
        return $this->select('orders.*, users.name as customer_name, users.email as customer_email, invoices.id as invoice_id')
            ->join('users', 'users.id = orders.customer_id')
            ->join('invoices', 'invoices.order_id = orders.id', 'left')
            ->where('orders.id', $id)
            ->whereIn('orders.status', ['confirmado', 'preparando', 'en_ruta', 'entregado', 'cancelado'])
            ->first();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listPendingForLogistics(int $limit = 4): array
    {
        return $this->select('orders.*, users.name as customer_name')
            ->join('users', 'users.id = orders.customer_id', 'left')
            ->whereIn('orders.status', ['confirmado', 'preparando'])
            ->orderBy('orders.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithCustomer(int $id): ?array
    {
        return $this->select('orders.*, users.name as customer_name, users.email as customer_email')
            ->join('users', 'users.id = orders.customer_id')
            ->where('orders.id', $id)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithCustomerAndInvoice(int $id): ?array
    {
        return $this->select('orders.*, users.name as customer_name, users.email as customer_email, invoices.id as invoice_id')
            ->join('users', 'users.id = orders.customer_id')
            ->join('invoices', 'invoices.order_id = orders.id', 'left')
            ->where('orders.id', $id)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForCustomer(int $id, int $customerId): ?array
    {
        return $this->where('id', $id)->where('customer_id', $customerId)->first();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countByStatus(?string $status = null): int
    {
        return $status === null ? $this->countAllResults() : $this->where('status', $status)->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countForCustomer(int $customerId, ?string $status = null): int
    {
        $builder = $this->where('customer_id', $customerId);
        if ($status !== null) {
            $builder->where('status', $status);
        }

        return $builder->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countReadyOrders(): int
    {
        return $this->whereIn('status', ['confirmado', 'preparando'])->countAllResults();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAvailableForMessaging(array $user): array
    {
        $role = $user['role_name'] ?? null;

        if ($role === 'administrador') {
            return $this->select('orders.*, users.name as customer_name, GROUP_CONCAT(DISTINCT routes.driver_id) as driver_ids')
                ->join('users', 'users.id = orders.customer_id', 'left')
                ->join('deliveries', 'deliveries.order_id = orders.id', 'left')
                ->join('routes', 'routes.id = deliveries.route_id', 'left')
                ->groupBy('orders.id')
                ->orderBy('orders.id', 'DESC')
                ->findAll();
        }

        if ($role === 'cliente') {
            return $this->select('orders.*, users.name as customer_name')
                ->join('users', 'users.id = orders.customer_id', 'left')
                ->where('customer_id', $user['id'])
                ->orderBy('orders.id', 'DESC')
                ->findAll();
        }

        if ($role === 'conductor') {
            return $this->select('orders.*, users.name as customer_name')
                ->join('users', 'users.id = orders.customer_id', 'left')
                ->join('deliveries', 'deliveries.order_id = orders.id')
                ->join('routes', 'routes.id = deliveries.route_id')
                ->where('routes.driver_id', $user['id'])
                ->orderBy('orders.id', 'DESC')
                ->findAll();
        }

        if ($role === 'logistica') {
            return $this->select('orders.*, users.name as customer_name')
                ->join('users', 'users.id = orders.customer_id', 'left')
                ->join('deliveries', 'deliveries.order_id = orders.id')
                ->groupBy('orders.id')
                ->orderBy('orders.id', 'DESC')
                ->findAll();
        }

        return [];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function isAssignedToDriver(int $orderId, int $driverId): bool
    {
        return $this->join('deliveries', 'deliveries.order_id = orders.id')
            ->join('routes', 'routes.id = deliveries.route_id')
            ->where('orders.id', $orderId)
            ->where('routes.driver_id', $driverId)
            ->first() !== null;
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createCustomerOrder(array $orderData, array $items): int
    {
        $this->db->transStart();
        $this->insert($orderData);
        $orderId = (int) $this->getInsertID();

        $itemModel = model(OrderItemModel::class);
        foreach ($items as $item) {
            $itemModel->insert($item + ['order_id' => $orderId]);
        }

        $this->db->transComplete();

        return $orderId;
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateCustomerPendingOrder(int $id, int $customerId, array $orderData, array $items): array
    {
        $order = $this->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (! $order) {
            return ['success' => false, 'message' => 'Pedido no encontrado.'];
        }

        if (($order['status'] ?? null) !== 'pendiente') {
            return ['success' => false, 'message' => 'Solo puedes modificar pedidos pendientes de confirmacion.'];
        }

        $this->db->transStart();
        $this->update($id, $orderData);

        $itemModel = model(OrderItemModel::class);
        $itemModel->where('order_id', $id)->delete();

        foreach ($items as $item) {
            $itemModel->insert($item + ['order_id' => $id]);
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            return ['success' => false, 'message' => 'No se pudo actualizar el pedido.'];
        }

        return ['success' => true, 'message' => 'Pedido actualizado correctamente.'];
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateStatus(int $id, string $status, ?string $updatedAt = null): bool
    {
        return $this->update($id, [
            'status' => $status,
            'updated_at' => $updatedAt ?? date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateStatusFromAdmin(int $id, string $status): array
    {
        $order = $this->find($id);

        if (! $order) {
            return ['success' => false, 'message' => 'Pedido no encontrado.'];
        }

        $updatedAt = date('Y-m-d H:i:s');

        if ($order['status'] === $status) {
            $this->updateStatus($id, $status, $updatedAt);
            return ['success' => true, 'message' => 'Estado del pedido actualizado correctamente.'];
        }

        if (in_array($order['status'], ['pendiente', 'cancelado'], true) && $status === 'confirmado') {
            $itemModel = model(OrderItemModel::class);
            $stockModel = model(StockModel::class);
            $invoiceModel = model(InvoiceModel::class);
            $items = $itemModel->itemsForOrder($id);

            if (! $stockModel->hasEnoughForItems($items)) {
                return ['success' => false, 'message' => 'No hay stock suficiente para confirmar este pedido.'];
            }

            $this->db->transStart();
            $stockModel->deductForItems($items, $updatedAt);
            $this->updateStatus($id, $status, $updatedAt);
            $invoiceResult = $invoiceModel->ensureForOrder($order, $updatedAt);
            $this->db->transComplete();

            if (! $this->db->transStatus()) {
                return ['success' => false, 'message' => 'No se pudo confirmar el pedido.'];
            }

            if (! ($invoiceResult['success'] ?? false)) {
                return ['success' => false, 'message' => $invoiceResult['message'] ?? 'No se pudo generar la factura.'];
            }

            return ['success' => true, 'message' => 'Pedido confirmado, stock actualizado y factura generada correctamente.'];
        }

        $stockedStatuses = ['confirmado', 'preparando', 'en_ruta', 'entregado'];
        $returnsStock = in_array($order['status'], $stockedStatuses, true)
            && in_array($status, ['pendiente', 'cancelado'], true);
        $removesRoute = in_array($status, ['pendiente', 'confirmado', 'preparando', 'cancelado'], true);

        if ($returnsStock || $removesRoute) {
            $itemModel = model(OrderItemModel::class);
            $stockModel = model(StockModel::class);
            $deliveryModel = model(DeliveryModel::class);
            $routeModel = model(RouteModel::class);
            $items = $itemModel->itemsForOrder($id);

            $this->db->transBegin();

            if ($returnsStock && ! $stockModel->restoreForItems($items, $updatedAt)) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'No se pudo devolver el stock del pedido.'];
            }

            $removedDelivery = ['removed' => false, 'route_id' => null];
            if ($removesRoute) {
                $removedDelivery = $deliveryModel->removeForOrder($id);
            }

            $this->updateStatus($id, $status, $updatedAt);

            if (! empty($removedDelivery['route_id'])) {
                $routeModel->syncStatusFromDeliveries((int) $removedDelivery['route_id']);
            }

            if (! $this->db->transStatus()) {
                $this->db->transRollback();
                return ['success' => false, 'message' => 'No se pudo actualizar el pedido.'];
            }

            $this->db->transCommit();

            $messages = ['Estado del pedido actualizado correctamente.'];
            if ($removedDelivery['removed'] ?? false) {
                $messages[] = 'Se eliminó de la ruta asignada.';
            }
            if ($returnsStock) {
                $messages[] = 'Las unidades del pedido se devolvieron al stock.';
            }

            return ['success' => true, 'message' => implode(' ', $messages)];
        }

        $this->updateStatus($id, $status, $updatedAt);

        return ['success' => true, 'message' => 'Estado del pedido actualizado correctamente.'];
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteFromAdmin(int $id): array
    {
        $order = $this->find($id);

        if (! $order) {
            return ['success' => false, 'message' => 'Pedido no encontrado.'];
        }

        $updatedAt = date('Y-m-d H:i:s');
        $itemModel = model(OrderItemModel::class);
        $stockModel = model(StockModel::class);
        $deliveryModel = model(DeliveryModel::class);
        $routeModel = model(RouteModel::class);
        $invoiceModel = model(InvoiceModel::class);
        $items = $itemModel->itemsForOrder($id);
        $stockedStatuses = ['confirmado', 'preparando', 'en_ruta', 'entregado'];
        $shouldRestoreStock = in_array((string) ($order['status'] ?? ''), $stockedStatuses, true);

        $this->db->transBegin();

        if ($shouldRestoreStock && ! $stockModel->restoreForItems($items, $updatedAt)) {
            $this->db->transRollback();

            return ['success' => false, 'message' => 'No se pudo devolver el stock del pedido.'];
        }

        $removedDelivery = $deliveryModel->removeForOrder($id);
        $invoiceModel->where('order_id', $id)->delete();
        $itemModel->where('order_id', $id)->delete();
        $this->delete($id);

        $deletedEmptyRoute = false;
        if (! empty($removedDelivery['route_id'])) {
            $routeId = (int) $removedDelivery['route_id'];
            $remainingDeliveries = $deliveryModel->where('route_id', $routeId)->countAllResults();

            if ($remainingDeliveries === 0) {
                $routeModel->delete($routeId);
                $deletedEmptyRoute = true;
            } else {
                $routeModel->syncStatusFromDeliveries($routeId);
            }
        }

        if (! $this->db->transStatus()) {
            $this->db->transRollback();

            return ['success' => false, 'message' => 'No se pudo eliminar el pedido.'];
        }

        $this->db->transCommit();

        $messages = ['Pedido eliminado correctamente.'];
        if ($shouldRestoreStock) {
            $messages[] = 'Las unidades del pedido se devolvieron al stock.';
        }
        if ($removedDelivery['removed'] ?? false) {
            $messages[] = 'Se elimino de la ruta asignada.';
        }
        if ($deletedEmptyRoute) {
            $messages[] = 'La ruta tambien se elimino porque ya no tenia pedidos.';
        }

        return ['success' => true, 'message' => implode(' ', $messages)];
    }
}
