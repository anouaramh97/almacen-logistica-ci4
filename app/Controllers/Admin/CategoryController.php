<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

/**
 * Coordina las pantallas y acciones del modulo de administracion.
 */
class CategoryController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $categoryModel = model(CategoryModel::class);
        $categories = $categoryModel->listAllByName();

        return $this->render('admin/categories/index', ['categories' => $categories, 'editingCategory' => null]);
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $categoryModel = model(CategoryModel::class);
        $categories = $categoryModel->listAllByName();
        $editingCategory = $categoryModel->find((int) $id);

        if (! $editingCategory) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'Categoría no encontrada.');
        }

        return $this->render('admin/categories/index', ['categories' => $categories, 'editingCategory' => $editingCategory]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate(['name' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa el nombre de la categoría.');
        }

        model(CategoryModel::class)->createCategory([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        if (! $this->validate(['name' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Revisa el nombre de la categoría.');
        }

        model(CategoryModel::class)->updateCategory((int) $id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('admin/categories'))->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Elimina el registro indicado y redirige con el resultado de la operacion.
     */
    public function delete($id)
    {
        if (! model(CategoryModel::class)->deleteCategoryIfUnused((int) $id)) {
            return redirect()->to(site_url('admin/categories'))->with('error', 'No se puede eliminar una categoría asociada a productos.');
        }

        return redirect()->to(site_url('admin/categories'))->with('success', 'Categoría eliminada correctamente.');
    }
}
