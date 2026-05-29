<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['order_id', 'invoice_number', 'issue_date', 'subtotal', 'tax', 'total', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForAdmin(): array
    {
        return $this->select('invoices.*, orders.id as order_ref, users.name as customer_name')
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.customer_id')
            ->orderBy('invoices.id', 'DESC')
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findDetailed(int $id): ?array
    {
        return $this->select('invoices.*, orders.delivery_address, orders.id as order_id, users.name as customer_name, users.email as customer_email')
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.customer_id')
            ->where('invoices.id', $id)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findDetailedForCustomer(int $id, int $customerId): ?array
    {
        return $this->select('invoices.*, orders.id as order_id, orders.delivery_address, users.name as customer_name, users.email as customer_email')
            ->join('orders', 'orders.id = invoices.order_id')
            ->join('users', 'users.id = orders.customer_id')
            ->where('invoices.id', $id)
            ->where('orders.customer_id', $customerId)
            ->first();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listRecentForCustomer(int $customerId, int $limit = 4): array
    {
        return $this->select('invoices.*, orders.id as order_ref')
            ->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.customer_id', $customerId)
            ->orderBy('invoices.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countPending(): int
    {
        return $this->where('status', 'pendiente')->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countForCustomer(int $customerId): int
    {
        return $this->join('orders', 'orders.id = invoices.order_id')
            ->where('orders.customer_id', $customerId)
            ->countAllResults();
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createForOrder(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function ensureForOrder(array $order, ?string $createdAt = null): array
    {
        $orderId = (int) ($order['id'] ?? 0);

        if ($orderId <= 0) {
            return ['success' => false, 'message' => 'Pedido no valido para generar factura.'];
        }

        $existing = $this->where('order_id', $orderId)->first();
        if ($existing) {
            return [
                'success' => true,
                'created' => false,
                'invoice_id' => (int) $existing['id'],
                'message' => 'La factura ya existia.',
            ];
        }

        $timestamp = $createdAt ?? date('Y-m-d H:i:s');

        $subtotal = (float) $order['total'] / 1.21;
        $tax = (float) $order['total'] - $subtotal;

        $invoiceId = $this->createForOrder([
            'order_id' => $orderId,
            'invoice_number' => 'FAC-' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT),
            'issue_date' => date('Y-m-d'),
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => $order['total'],
            'status' => 'pendiente',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        return [
            'success' => true,
            'created' => true,
            'invoice_id' => $invoiceId,
            'message' => 'Factura generada correctamente.',
        ];
    }
}
