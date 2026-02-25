<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\Telegram;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;
use App\Domain\RepositoryInterfaces\MessageSenderInterface;
use Illuminate\Support\Facades\Http;

class TelegramSender implements MessageSenderInterface
{
    private string $botToken;
    private string $apiBaseUrl = 'https://api.telegram.org/bot';

    public function __construct(string $botToken)
    {
        $this->botToken = $botToken;
    }

    public function send(Notification $notification, string $recipient): string
    {
        $url = "{$this->apiBaseUrl}{$this->botToken}/sendMessage";
        
        $response = Http::post($url, [
            'chat_id' => $recipient,
            'text' => $this->formatMessage($notification),
            'parse_mode' => 'HTML',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Telegram API error: {$response->body()}"
            );
        }

        $data = $response->json();
        return (string) $data['result']['message_id'];
    }

    public function supports(Notification $notification): bool
    {
        return $notification->getChannel() === NotificationChannel::TELEGRAM;
    }

    public function getChannelName(): string
    {
        return 'telegram';
    }

    private function formatMessage(Notification $notification): string
    {
        $subject = htmlspecialchars($notification->getSubject());
        $content = htmlspecialchars($notification->getContent());
        
        return "<b>{$subject}</b>\n\n{$content}";
    }

    /**
     * Send a message with custom formatting
     */
    public function sendRaw(string $chatId, string $message, string $parseMode = 'HTML'): string
    {
        $url = "{$this->apiBaseUrl}{$this->botToken}/sendMessage";
        
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => $parseMode,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                "Telegram API error: {$response->body()}"
            );
        }

        $data = $response->json();
        return (string) $data['result']['message_id'];
    }
}
