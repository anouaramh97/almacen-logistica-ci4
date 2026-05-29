<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAllByName(): array
    {
        return $this->orderBy('name')->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function isInUse(int $id): bool
    {
        return $this->db->table('products')->where('category_id', $id)->countAllResults() > 0;
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createCategory(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateCategory(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteCategoryIfUnused(int $id): bool
    {
        if ($this->isInUse($id)) {
            return false;
        }

        $this->delete($id);

        return true;
    }
}
