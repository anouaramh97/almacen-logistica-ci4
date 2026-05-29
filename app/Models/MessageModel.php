<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class MessageModel extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['conversation_id', 'sender_id', 'receiver_id', 'message', 'read_at', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForConversation(int $conversationId): array
    {
        return $this->select('messages.*, su.name as sender_name, ru.name as receiver_name')
            ->join('users su', 'su.id = messages.sender_id')
            ->join('users ru', 'ru.id = messages.receiver_id')
            ->where('conversation_id', $conversationId)
            ->orderBy('messages.id')
            ->findAll();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function latestForConversation(int $conversationId): ?array
    {
        return $this->where('conversation_id', $conversationId)->orderBy('id', 'DESC')->first();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function addMessage(array $data): int
    {
        $this->insert($data);

        return (int) $this->getInsertID();
    }

    /**
     * Cuenta mensajes recibidos que el usuario todavia no ha leido.
     */
    public function countUnreadForUser(int $userId): int
    {
        return $this->where('receiver_id', $userId)
            ->where('read_at', null)
            ->countAllResults();
    }

    /**
     * Marca como leidos los mensajes recibidos dentro de una conversacion.
     */
    public function markConversationReadForUser(int $conversationId, int $userId): bool
    {
        return $this->where('conversation_id', $conversationId)
            ->where('receiver_id', $userId)
            ->where('read_at', null)
            ->set([
                'read_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }
}
