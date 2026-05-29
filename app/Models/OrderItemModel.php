<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['order_id', 'product_id', 'quantity', 'unit_price', 'tax_rate', 'subtotal', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function itemsForOrdersSummary(array $orderIds): array
    {
        if ($orderIds === []) {
            return [];
        }

        return $this->select('order_items.order_id, order_items.quantity, order_items.subtotal, products.name as product_name')
            ->join('products', 'products.id = order_items.product_id')
            ->whereIn('order_items.order_id', $orderIds)
            ->orderBy('order_items.id')
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function itemsForOrder(int $orderId, bool $withImage = false): array
    {
        $select = 'order_items.*, products.name as product_name';
        if ($withImage) {
            $select .= ', products.image_path';
        }

        return $this->select($select)
            ->join('products', 'products.id = order_items.product_id')
            ->where('order_items.order_id', $orderId)
            ->findAll();
    }
}
