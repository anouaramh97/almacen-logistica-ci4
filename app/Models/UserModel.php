<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['role_id', 'name', 'email', 'password', 'avatar_path', 'phone', 'address', 'city', 'postal_code', 'status', 'email_verified_at', 'remember_token'];
    protected $useTimestamps = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    protected function hashPassword(array $data): array
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        $password = $data['data']['password'];

        if ($password && ! preg_match('/^\$2y\$/', $password)) {
            $data['data']['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        return $data;
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.email', $email)
            ->first();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findWithRoleById(int $id): ?array
    {
        return $this->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $id)
            ->first();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listWithRoles(): array
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->orderBy('users.id', 'DESC')
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listDrivers(): array
    {
        return $this->select('users.id, users.name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'conductor')
            ->orderBy('users.name')
            ->findAll();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countAdmins(): int
    {
        return $this->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'administrador')
            ->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countActiveDrivers(): int
    {
        return $this->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'conductor')
            ->where('users.status', 'activo')
            ->countAllResults();
    }

    /**
     * Calcula un total usado en paneles, validaciones o resumenes.
     */
    public function countPendingClients(): int
    {
        return $this->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'cliente')
            ->where('users.status', 'inactivo')
            ->countAllResults();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listPendingClients(int $limit = 5): array
    {
        return $this->select('users.*, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('roles.name', 'cliente')
            ->where('users.status', 'inactivo')
            ->orderBy('users.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listActiveRecipientsForUser(int $userId, bool $adminsOnly): array
    {
        $builder = $this->select('users.id, users.name, roles.name as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.status', 'activo')
            ->where('users.id !=', $userId);

        if ($adminsOnly) {
            $builder->where('roles.name', 'administrador');
        }

        return $builder->orderBy('users.name')->findAll();
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteWithRelations(int $id): void
    {
        $db = $this->db;

        $db->table('messages')->groupStart()->where('sender_id', $id)->orWhere('receiver_id', $id)->groupEnd()->delete();
        $db->table('conversations')->where('created_by', $id)->delete();
        $this->delete($id);
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createBackofficeUser(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateBackofficeUser(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Actualiza registros relacionados manteniendo la coherencia de los datos.
     */
    public function updateProfileData(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function activateUser(int $id): bool
    {
        return $this->update($id, [
            'status' => 'activo',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Elimina datos relacionados y limpia recursos asociados cuando corresponde.
     */
    public function deleteOwnAccount(int $id, ?string $avatarPath = null): void
    {
        delete_project_media($avatarPath);
        $this->delete($id);
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createClient(array $data): int
    {
        $role = model(RoleModel::class)->findByName('cliente');
        $data['role_id'] = $role['id'] ?? 0;

        $data['status'] = $data['status'] ?? 'inactivo';
        $this->insert($data);

        return (int) $this->getInsertID();
    }
}
