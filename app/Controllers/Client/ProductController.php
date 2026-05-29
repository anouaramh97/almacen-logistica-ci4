<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\ProductImageModel;
use App\Models\ProductModel;

/**
 * Coordina las pantallas y acciones del modulo de cliente.
 */
class ProductController extends BaseController
{
    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $product = model(ProductModel::class)->findActiveWithCategory((int) $id);

        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $gallery = model(ProductImageModel::class)->forProduct((int) $id);

        return $this->render('client/products/show', [
            'product' => $product,
            'gallery' => $gallery,
        ]);
    }
}
