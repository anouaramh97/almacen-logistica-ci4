<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StockModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class StockController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $stocks = model(StockModel::class)->listWithRelations();

        return $this->render('admin/stocks/index', ['stocks' => $stocks]);
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $stock = model(StockModel::class)->findWithRelations((int) $id);
        return $this->render('admin/stocks/edit', ['stock' => $stock]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['quantity' => 'required|integer', 'minimum_quantity' => 'required|integer'])) return redirect()->back()->withInput()->with('error', 'Revisa los valores de stock.');
        model(StockModel::class)->updateLevels((int) $id, ['quantity' => $this->request->getPost('quantity'), 'minimum_quantity' => $this->request->getPost('minimum_quantity'), 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->to(site_url('admin/stocks'))->with('success', 'Stock actualizado correctamente.');
    }
}
