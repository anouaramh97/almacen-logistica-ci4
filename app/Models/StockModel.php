<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class StockModel extends Model
{
    protected $table = 'stocks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['product_id', 'warehouse_id', 'quantity', 'minimum_quantity', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listWithRelations(): array
    {
        return $this->select('stocks.*, products.name as product_name, products.sku, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stocks.product_id')
            ->join('warehouses', 'warehouses.id = stocks.warehouse_id')
            ->orderBy('products.name')
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithRelations(int $id): ?array
    {
        return $this->select('stocks.*, products.name as product_name, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stocks.product_id')
            ->join('warehouses', 'warehouses.id = stocks.warehouse_id')
            ->where('stocks.id', $id)
            ->first();
    }

    /**
     * Obtiene datos vinculados a una entidad concreta.
     */
    public function forProductWithWarehouse(int $productId): array
    {
        return $this->select('stocks.*, warehouses.name as warehouse_name, warehouses.city')
            ->join('warehouses', 'warehouses.id = stocks.warehouse_id')
            ->where('stocks.product_id', $productId)
            ->orderBy('warehouses.name')
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function lowStockItems(int $limit = 5): array
    {
        return $this->select('stocks.*, products.id as product_id, products.name as product_name, products.image_path, categories.name as category_name, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stocks.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('warehouses', 'warehouses.id = stocks.warehouse_id', 'left')
            ->where('stocks.quantity >', 0)
            ->where('stocks.quantity <= minimum_quantity', null, false)
            ->orderBy('stocks.quantity', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function outOfStockItems(int $limit = 5): array
    {
        return $this->select('stocks.*, products.id as product_id, products.name as product_name, products.image_path, categories.name as category_name, warehouses.name as warehouse_name')
            ->join('products', 'products.id = stocks.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('warehouses', 'warehouses.id = stocks.warehouse_id', 'left')
            ->where('stocks.quantity <=', 0)
            ->orderBy('stocks.quantity', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countDistinctProductsInHand(): int
    {
        return $this->select('product_id')->where('quantity >', 0)->distinct()->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countLowStock(): int
    {
        return $this->where('quantity >', 0)->where('quantity <= minimum_quantity', null, false)->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countOutOfStock(): int
    {
        return $this->where('quantity <=', 0)->countAllResults();
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createInitialStock(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function availableQuantityForProduct(int $productId): int
    {
        $row = $this->selectSum('quantity')
            ->where('product_id', $productId)
            ->first();

        return (int) ($row['quantity'] ?? 0);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function hasEnoughForItems(array $items): bool
    {
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $required = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $required <= 0) {
                continue;
            }

            if ($this->availableQuantityForProduct($productId) < $required) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function deductForItems(array $items, ?string $updatedAt = null): bool
    {
        $updatedAt ??= date('Y-m-d H:i:s');

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $remaining = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $remaining <= 0) {
                continue;
            }

            $stocks = $this->where('product_id', $productId)
                ->orderBy('id', 'ASC')
                ->findAll();

            foreach ($stocks as $stock) {
                if ($remaining <= 0) {
                    break;
                }

                $currentQuantity = (int) ($stock['quantity'] ?? 0);
                if ($currentQuantity <= 0) {
                    continue;
                }

                $deduction = min($currentQuantity, $remaining);
                $this->update((int) $stock['id'], [
                    'quantity' => $currentQuantity - $deduction,
                    'updated_at' => $updatedAt,
                ]);

                $remaining -= $deduction;
            }

            if ($remaining > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function restoreForItems(array $items, ?string $updatedAt = null): bool
    {
        $updatedAt ??= date('Y-m-d H:i:s');

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $stock = $this->where('product_id', $productId)
                ->orderBy('id', 'ASC')
                ->first();

            if (! $stock) {
                return false;
            }

            $this->update((int) $stock['id'], [
                'quantity' => (int) ($stock['quantity'] ?? 0) + $quantity,
                'updated_at' => $updatedAt,
            ]);
        }

        return true;
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateLevels(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }
}
