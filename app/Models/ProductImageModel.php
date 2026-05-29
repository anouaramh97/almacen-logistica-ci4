<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class ProductImageModel extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['product_id', 'path', 'sort_order', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Obtiene datos vinculados a una entidad concreta.
     */
    public function forProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function groupedByProductIds(array $productIds): array
    {
        if ($productIds === []) {
            return [];
        }

        $rows = $this->whereIn('product_id', $productIds)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['product_id']][] = $row;
        }

        return $grouped;
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countForProduct(int $productId): int
    {
        return $this->where('product_id', $productId)->countAllResults();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForProduct(int $productId, int $imageId): ?array
    {
        return $this->where('product_id', $productId)->where('id', $imageId)->first();
    }
}
