<?php

// Modelo: centraliza las consultas y operaciones de base de datos.

namespace App\Models;

use CodeIgniter\Model;

/**
 * Encapsula el acceso a datos y consultas relacionadas con esta entidad.
 */
class ConversationModel extends Model
{
    protected $table = 'conversations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['subject', 'order_id', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    /**
     * Devuelve una coleccion filtrada u ordenada para listados de la aplicacion.
     */
    public function listForUser(int $userId): array
    {
        $participantSelect = $this->participantNameSelect($userId);

        return $this->select('conversations.*, orders.id as order_ref, MAX(messages.created_at) as last_message_at')
            ->select($participantSelect, false)
            ->join('messages', 'messages.conversation_id = conversations.id', 'left')
            ->join('users sender_user', 'sender_user.id = messages.sender_id', 'left')
            ->join('users receiver_user', 'receiver_user.id = messages.receiver_id', 'left')
            ->join('users creator_user', 'creator_user.id = conversations.created_by', 'left')
            ->join('orders', 'orders.id = conversations.order_id', 'left')
            ->groupStart()
            ->where('conversations.created_by', $userId)
            ->orWhere('messages.sender_id', $userId)
            ->orWhere('messages.receiver_id', $userId)
            ->groupEnd()
            ->groupBy('conversations.id')
            ->orderBy('last_message_at', 'DESC')
            ->findAll();
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    public function findForUser(int $conversationId, int $userId): ?array
    {
        $participantSelect = $this->participantNameSelect($userId);

        return $this->select('conversations.*, orders.id as order_ref')
            ->select($participantSelect, false)
            ->join('messages', 'messages.conversation_id = conversations.id', 'left')
            ->join('users sender_user', 'sender_user.id = messages.sender_id', 'left')
            ->join('users receiver_user', 'receiver_user.id = messages.receiver_id', 'left')
            ->join('users creator_user', 'creator_user.id = conversations.created_by', 'left')
            ->join('orders', 'orders.id = conversations.order_id', 'left')
            ->where('conversations.id', $conversationId)
            ->groupStart()
            ->where('conversations.created_by', $userId)
            ->orWhere('messages.sender_id', $userId)
            ->orWhere('messages.receiver_id', $userId)
            ->groupEnd()
            ->groupBy('conversations.id')
            ->first();
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    public function createConversationWithMessage(array $conversationData, array $messageData): int
    {
        $this->db->transStart();
        $this->insert($conversationData);
        $conversationId = (int) $this->getInsertID();
        model(MessageModel::class)->insert($messageData + ['conversation_id' => $conversationId]);
        $this->db->transComplete();

        return $conversationId;
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function touchConversation(int $id, string $updatedAt): bool
    {
        return $this->update($id, ['updated_at' => $updatedAt]);
    }

    /**
     * Resuelve el nombre del otro participante para mostrarlo en la mensajeria.
     */
    private function participantNameSelect(int $userId): string
    {
        return "COALESCE(
            MAX(CASE
                WHEN messages.sender_id != {$userId} THEN sender_user.name
                WHEN messages.receiver_id != {$userId} THEN receiver_user.name
                ELSE NULL
            END),
            MAX(creator_user.name),
            MAX(conversations.subject)
        ) as participant_name";
    }
}
