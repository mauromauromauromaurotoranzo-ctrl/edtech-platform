<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\WhatsApp;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;
use App\Domain\RepositoryInterfaces\MessageSenderInterface;
use Illuminate\Support\Facades\Http;

class WhatsAppSender implements MessageSenderInterface
{
    private string $apiUrl;
    private string $accessToken;
    private string $phoneNumberId;

    public function __construct(
        string $apiUrl,
        string $accessToken,
        string $phoneNumberId
    ) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->accessToken = $accessToken;
        $this->phoneNumberId = $phoneNumberId;
    }

    public function send(Notification $notification, string $recipient): string
    {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";
        
        // Format phone number (remove + and spaces)
        $to = preg_replace('/[^0-9]/', '', $recipient);

        $response = Http::withToken($this->accessToken)
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $this->formatMessage($notification),
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "WhatsApp API error: {$response->body()}"
            );
        }

        $data = $response->json();
        return $data['messages'][0]['id'] ?? uniqid('wa_', true);
    }

    public function supports(Notification $notification): bool
    {
        return $notification->getChannel() === NotificationChannel::WHATSAPP;
    }

    public function getChannelName(): string
    {
        return 'whatsapp';
    }

    private function formatMessage(Notification $notification): string
    {
        return "*{$notification->getSubject()}*\n\n{$notification->getContent()}";
    }

    /**
     * Send a template message (for approved templates)
     */
    public function sendTemplate(
        string $to,
        string $templateName,
        string $languageCode = 'es',
        array $components = []
    ): string {
        $url = "{$this->apiUrl}/{$this->phoneNumberId}/messages";
        
        $to = preg_replace('/[^0-9]/', '', $to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        $response = Http::withToken($this->accessToken)->post($url, $payload);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "WhatsApp API error: {$response->body()}"
            );
        }

        $data = $response->json();
        return $data['messages'][0]['id'] ?? uniqid('wa_', true);
    }
}
