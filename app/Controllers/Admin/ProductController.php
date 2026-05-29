<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\StockModel;
use App\Models\WarehouseModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class ProductController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $products = model(ProductModel::class)->listForAdmin($q);

        return $this->render('admin/products/index', ['products' => $products, 'search' => $q]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $product = model(ProductModel::class)->findWithCategory((int) $id);
        $stocks = model(StockModel::class)->forProductWithWarehouse((int) $id);
        $gallery = model(ProductImageModel::class)->forProduct((int) $id);

        return $this->render('admin/products/show', ['product' => $product, 'stocks' => $stocks, 'gallery' => $gallery]);
    }

    /**
     * Prepara el formulario de alta con los datos auxiliares necesarios.
     */
    public function create(): string
    {
        return $this->render('admin/products/form', ['product' => null, 'categories' => model(CategoryModel::class)->listAllByName(), 'warehouses' => model(WarehouseModel::class)->listAllByName(), 'gallery' => []]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        $rules = ['category_id' => 'required|integer', 'name' => 'required', 'sku' => 'required', 'price' => 'required|decimal', 'status' => 'required', 'warehouse_id' => 'required|integer'];
        if (! $this->validate($rules)) return redirect()->back()->withInput()->with('error', 'Revisa los datos del producto.');
        $productModel = model(ProductModel::class);
        $now = date('Y-m-d H:i:s');

        $image = $this->request->getFile('image');
        $imagePath = store_project_media($image, 'product');

        $productData = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'sku' => $this->request->getPost('sku'),
            'description' => $this->request->getPost('description'),
            'image_path' => $imagePath,
            'price' => $this->request->getPost('price'),
            'tax_rate' => $this->request->getPost('tax_rate') ?: 21,
            'weight' => $this->request->getPost('weight'),
            'status' => $this->request->getPost('status'),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $galleryPayload = [];
        $galleryImages = $this->request->getFiles()['gallery_images'] ?? [];

        foreach ($galleryImages as $index => $galleryImage) {
            $galleryPath = store_project_media($galleryImage, 'product');
            if ($galleryPath === null) {
                continue;
            }

            $galleryPayload[] = [
                'path' => $galleryPath,
                'sort_order' => $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $productModel->createCatalogProduct($productData, $galleryPayload, [
            'warehouse_id' => $this->request->getPost('warehouse_id'),
            'quantity' => (int) ($this->request->getPost('quantity') ?: 0),
            'minimum_quantity' => (int) ($this->request->getPost('minimum_quantity') ?: 0),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->to(site_url('admin/products'))->with('success', 'Producto creado correctamente.');
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $product = model(ProductModel::class)->find((int) $id);
        $gallery = model(ProductImageModel::class)->forProduct((int) $id);

        return $this->render('admin/products/form', ['product' => $product, 'categories' => model(CategoryModel::class)->listAllByName(), 'warehouses' => model(WarehouseModel::class)->listAllByName(), 'gallery' => $gallery]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['category_id' => 'required|integer', 'name' => 'required', 'sku' => 'required', 'price' => 'required|decimal', 'status' => 'required'])) return redirect()->back()->withInput()->with('error', 'Revisa los datos del producto.');
        $productModel = model(ProductModel::class);
        $currentProduct = $productModel->find((int) $id);

        if (! $currentProduct) {
            return redirect()->to(site_url('admin/products'))->with('error', 'Producto no encontrado.');
        }

        $now = date('Y-m-d H:i:s');
        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'sku' => $this->request->getPost('sku'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'tax_rate' => $this->request->getPost('tax_rate') ?: 21,
            'weight' => $this->request->getPost('weight'),
            'status' => $this->request->getPost('status'),
            'updated_at' => $now,
        ];

        $image = $this->request->getFile('image');
        $data['image_path'] = store_project_media($image, 'product', $currentProduct['image_path'] ?? null);

        $galleryImages = $this->request->getFiles()['gallery_images'] ?? [];
        $existingGalleryCount = model(ProductImageModel::class)->countForProduct((int) $id);

        $galleryPayload = [];
        foreach ($galleryImages as $index => $galleryImage) {
            $galleryPath = store_project_media($galleryImage, 'product');
            if ($galleryPath === null) {
                continue;
            }

            $galleryPayload[] = [
                'path' => $galleryPath,
                'sort_order' => $existingGalleryCount + $index + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $productModel->updateCatalogProduct((int) $id, $data, $galleryPayload);
        return redirect()->to(site_url('admin/products'))->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteGalleryImage($productId, $imageId)
    {
        if (! model(ProductModel::class)->deleteGalleryImage((int) $productId, (int) $imageId)) {
            return redirect()->to(site_url('admin/products/edit/' . $productId))->with('error', 'Imagen no encontrada.');
        }

        return redirect()->to(site_url('admin/products/edit/' . $productId))->with('success', 'Imagen eliminada correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete($id)
    {
        model(ProductModel::class)->deleteCatalogProduct((int) $id);
        return redirect()->to(site_url('admin/products'))->with('success', 'Producto eliminado correctamente.');
    }
}
