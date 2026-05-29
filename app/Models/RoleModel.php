<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'description'];
    protected $useTimestamps = true;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listAllByName(): array
    {
        return $this->orderBy('name')->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findByName(string $name): ?array
    {
        return $this->where('name', $name)->first();
    }
}
