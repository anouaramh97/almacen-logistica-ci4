<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\WarehouseModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class WarehouseController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $warehouseModel = model(WarehouseModel::class);
        $warehouses = $warehouseModel->listAllByName();

        return $this->render('admin/warehouses/index', ['warehouses' => $warehouses, 'editingWarehouse' => null]);
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $warehouseModel = model(WarehouseModel::class);
        $warehouses = $warehouseModel->listAllByName();
        $editingWarehouse = $warehouseModel->find((int) $id);

        if (! $editingWarehouse) {
            return redirect()->to(site_url('admin/warehouses'))->with('error', 'Almacén no encontrado.');
        }

        return $this->render('admin/warehouses/index', ['warehouses' => $warehouses, 'editingWarehouse' => $editingWarehouse]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate(['name' => 'required', 'address' => 'required', 'city' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Completa los datos del almacén.');
        }

        model(WarehouseModel::class)->createWarehouse([
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/warehouses'))->with('success', 'Almacén creado correctamente.');
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['name' => 'required', 'address' => 'required', 'city' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Completa los datos del almacén.');
        }

        model(WarehouseModel::class)->updateWarehouse((int) $id, [
            'name' => $this->request->getPost('name'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'postal_code' => $this->request->getPost('postal_code'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/warehouses'))->with('success', 'Almacén actualizado correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete($id)
    {
        if (! model(WarehouseModel::class)->deleteWarehouseIfUnused((int) $id)) {
            return redirect()->to(site_url('admin/warehouses'))->with('error', 'No se puede eliminar un almacén que tiene stock asociado.');
        }

        return redirect()->to(site_url('admin/warehouses'))->with('success', 'Almacén eliminado correctamente.');
    }
}
