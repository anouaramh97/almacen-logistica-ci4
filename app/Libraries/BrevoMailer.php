<?php

// Libreria: encapsula una integracion o servicio externo.

namespace App\Libraries;

use RuntimeException;

/**
 * Agrupa logica reutilizable del proyecto.
 */
class BrevoMailer
{
    /**
     * Envia una notificacion o mensaje externo desde el sistema.
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): void
    {
        $apiKey = (string) env('brevo.apiKey');

        if ($apiKey === '') {
            throw new RuntimeException('Brevo API key is not configured.');
        }

        $senderEmail = (string) env('brevo.senderEmail', 'no-reply@almacen-logistica.local');
        $senderName = (string) env('brevo.senderName', 'Almacen Logistica');

        $payload = [
            'sender' => [
                'name' => $senderName,
                'email' => $senderEmail,
            ],
            'to' => [
                [
                    'email' => $toEmail,
                    'name' => $toName,
                ],
            ],
            'subject' => 'Recuperar contrasena',
            'htmlContent' => view('emails/password_reset', [
                'name' => $toName,
                'resetUrl' => $resetUrl,
            ]),
        ];

        $response = service('curlrequest')->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => $apiKey,
                'content-type' => 'application/json',
            ],
            'json' => $payload,
            'http_errors' => false,
            'timeout' => 10,
        ]);

        $status = $response->getStatusCode();

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('Brevo rejected the email request with status ' . $status . ': ' . $response->getBody());
        }
    }
}
