<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['category_id', 'name', 'sku', 'description', 'image_path', 'price', 'tax_rate', 'weight', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForAdmin(string $query = ''): array
    {
        $builder = $this->select('products.*, categories.name as category_name, COALESCE(SUM(stocks.quantity), 0) as stock_total')
            ->join('categories', 'categories.id = products.category_id')
            ->join('stocks', 'stocks.product_id = products.id', 'left')
            ->groupBy('products.id');

        if ($query !== '') {
            $builder->groupStart()
                ->like('products.name', $query)
                ->orLike('products.sku', $query)
                ->orLike('products.description', $query)
                ->orLike('categories.name', $query)
                ->groupEnd();
        }

        return $builder->orderBy('products.id', 'DESC')->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithCategory(int $id): ?array
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.id', $id)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findActiveWithCategory(int $id): ?array
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.id', $id)
            ->where('products.status', 'activo')
            ->first();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listActive(): array
    {
        return $this->select('products.*, categories.name as category_name, COALESCE(SUM(stocks.quantity), 0) as stock_total')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('stocks', 'stocks.product_id = products.id', 'left')
            ->where('products.status', 'activo')
            ->groupBy('products.id')
            ->orderBy('products.name')
            ->findAll();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countAllProducts(): int
    {
        return $this->countAllResults();
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createCatalogProduct(array $productData, array $galleryImages, array $stockData): int
    {
        $this->db->transStart();
        $this->insert($productData);
        $productId = (int) $this->getInsertID();

        $productImageModel = model(ProductImageModel::class);
        foreach ($galleryImages as $galleryImage) {
            $productImageModel->insert($galleryImage + ['product_id' => $productId]);
        }

        model(StockModel::class)->insert($stockData + ['product_id' => $productId]);
        $this->db->transComplete();

        return $productId;
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateCatalogProduct(int $id, array $productData, array $galleryImages = []): bool
    {
        $this->db->transStart();
        $this->update($id, $productData);

        $productImageModel = model(ProductImageModel::class);
        foreach ($galleryImages as $galleryImage) {
            $productImageModel->insert($galleryImage + ['product_id' => $id]);
        }

        $this->db->transComplete();

        return true;
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteCatalogProduct(int $id): void
    {
        $productImageModel = model(ProductImageModel::class);
        $product = $this->find($id);

        if ($product) {
            delete_project_media($product['image_path'] ?? null);
            foreach ($productImageModel->forProduct($id) as $image) {
                delete_project_media($image['path'] ?? null);
            }
        }

        $this->delete($id);
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteGalleryImage(int $productId, int $imageId): bool
    {
        $imageModel = model(ProductImageModel::class);
        $image = $imageModel->findForProduct($productId, $imageId);

        if (! $image) {
            return false;
        }

        delete_project_media($image['path'] ?? null);
        $imageModel->delete($imageId);

        return true;
    }
}
