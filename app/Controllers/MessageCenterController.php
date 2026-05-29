<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers;

use App\Models\ConversationModel;
use App\Models\MessageModel;
use App\Models\OrderModel;
use App\Models\UserModel;

/**
 * Coordina las pantallas y acciones del modulo de general.
 */
class MessageCenterController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $user = current_user();
        $conversations = model(ConversationModel::class)->listForUser((int) $user['id']);

        return $this->render('messages/index', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Prepara el formulario de alta con los datos auxiliares necesarios.
     */
    public function create(): string
    {
        $user = current_user();
        $recipients = model(UserModel::class)->listActiveRecipientsForUser((int) $user['id'], ($user['role_name'] ?? '') !== 'administrador');

        return $this->render('messages/create', [
            'recipients' => $recipients,
            'orders' => $this->availableOrders($user),
        ]);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $user = current_user();
        $conversation = $this->findConversationForUser((int) $id, (int) $user['id']);

        if (! $conversation) {
            return redirect()->to(site_url('messages'))->with('error', 'Conversacion no encontrada.');
        }

        $messageModel = model(MessageModel::class);
        $messageModel->markConversationReadForUser((int) $id, (int) $user['id']);
        $messages = $messageModel->listForConversation((int) $id);
        $replyTargetName = $this->resolveReplyTargetName($messages, (int) $user['id']);
        $conversations = model(ConversationModel::class)->listForUser((int) $user['id']);

        return $this->render('messages/show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'replyTargetName' => $replyTargetName,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate([
            'subject' => 'required',
            'receiver_id' => 'required|integer',
            'message' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Completa los datos del mensaje.');
        }

        $now = date('Y-m-d H:i:s');
        $user = current_user();
        $receiverId = (int) $this->request->getPost('receiver_id');
        $userModel = model(UserModel::class);
        $conversationModel = model(ConversationModel::class);
        $orderModel = model(OrderModel::class);
        $receiver = $userModel->findWithRoleById($receiverId);

        if (($user['role_name'] ?? '') !== 'administrador') {
            if (($receiver['role_name'] ?? '') !== 'administrador') {
                return redirect()->back()->with('error', 'Solo puedes iniciar conversaciones con el administrador.');
            }
        } else {
            $selectedOrderId = (int) ($this->request->getPost('order_id') ?: 0);
            if ($selectedOrderId > 0) {
                $selectedOrder = $orderModel->find($selectedOrderId);
                $receiverRole = (string) ($receiver['role_name'] ?? '');
                $orderBelongsToReceiver = false;

                if ($receiverRole === 'cliente') {
                    $orderBelongsToReceiver = $selectedOrder && (int) ($selectedOrder['customer_id'] ?? 0) === $receiverId;
                } elseif ($receiverRole === 'conductor') {
                    $orderBelongsToReceiver = $orderModel->isAssignedToDriver($selectedOrderId, $receiverId);
                } else {
                    $orderBelongsToReceiver = $selectedOrder !== null;
                }

                if (! $orderBelongsToReceiver) {
                    return redirect()->back()->withInput()->with('error', 'El pedido relacionado debe pertenecer al usuario seleccionado.');
                }
            }
        }

        $conversationId = $conversationModel->createConversationWithMessage(
            [
                'subject' => $this->request->getPost('subject'),
                'order_id' => $this->request->getPost('order_id') ?: null,
                'created_by' => $user['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'sender_id' => $user['id'],
                'receiver_id' => $receiverId,
                'message' => $this->request->getPost('message'),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return redirect()->to(site_url('messages/' . $conversationId))->with('success', 'Mensaje enviado correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    public function reply($id)
    {
        if (! $this->validate(['message' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Escribe un mensaje para responder.');
        }

        $user = current_user();
        $now = date('Y-m-d H:i:s');
        $messageModel = model(MessageModel::class);
        $conversation = $this->findConversationForUser((int) $id, (int) $user['id']);

        if (! $conversation) {
            return redirect()->to(site_url('messages'))->with('error', 'Conversacion no encontrada.');
        }

        $last = $messageModel->latestForConversation((int) $id);

        if (! $last) {
            return redirect()->to(site_url('messages'))->with('error', 'Conversacion no encontrada.');
        }

        $receiverId = ((int) $last['sender_id'] === (int) $user['id']) ? (int) $last['receiver_id'] : (int) $last['sender_id'];

        $messageModel->addMessage([
            'conversation_id' => $id,
            'sender_id' => $user['id'],
            'receiver_id' => $receiverId,
            'message' => $this->request->getPost('message'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        model(ConversationModel::class)->touchConversation((int) $id, $now);

        return redirect()->to(site_url('messages/' . $id))->with('success', 'Respuesta enviada correctamente.');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function availableOrders(array $user): array
    {
        return model(OrderModel::class)->listAvailableForMessaging($user);
    }

    /**
     * Busca y devuelve un registro con la informacion adicional necesaria.
     */
    private function findConversationForUser(int $conversationId, int $userId): ?array
    {
        return model(ConversationModel::class)->findForUser($conversationId, $userId);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function resolveReplyTargetName(array $messages, int $userId): ?string
    {
        for ($index = count($messages) - 1; $index >= 0; $index--) {
            $message = $messages[$index];
            if ((int) ($message['sender_id'] ?? 0) !== $userId) {
                return (string) ($message['sender_name'] ?? '');
            }

            if ((int) ($message['receiver_id'] ?? 0) !== $userId) {
                return (string) ($message['receiver_name'] ?? '');
            }
        }

        return null;
    }
}
