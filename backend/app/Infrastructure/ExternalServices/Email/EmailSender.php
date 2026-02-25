<?php

declare(strict_types=1);

namespace App\Infrastructure\ExternalServices\Email;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;
use App\Domain\RepositoryInterfaces\MessageSenderInterface;
use Illuminate\Support\Facades\Mail;

class EmailSender implements MessageSenderInterface
{
    private string $fromAddress;
    private string $fromName;

    public function __construct(
        string $fromAddress,
        string $fromName = 'EdTech Platform'
    ) {
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
    }

    public function send(Notification $notification, string $recipient): string
    {
        try {
            Mail::html(
                $this->formatHtmlContent($notification),
                function ($message) use ($recipient, $notification) {
                    $message->to($recipient)
                        ->from($this->fromAddress, $this->fromName)
                        ->subject($notification->getSubject());
                }
            );

            // Return a unique ID since Laravel doesn't expose the SMTP message ID easily
            return uniqid('email_', true);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "Email sending failed: {$e->getMessage()}"
            );
        }
    }

    public function supports(Notification $notification): bool
    {
        return $notification->getChannel() === NotificationChannel::EMAIL;
    }

    public function getChannelName(): string
    {
        return 'email';
    }

    private function formatHtmlContent(Notification $notification): string
    {
        $subject = htmlspecialchars($notification->getSubject());
        $content = nl2br(htmlspecialchars($notification->getContent()));
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$subject}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4F46E5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$subject}</h1>
        </div>
        <div class="content">
            {$content}
        </div>
        <div class="footer">
            <p>Enviado por EdTech Platform</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Send email with custom view
     */
    public function sendWithView(
        string $to,
        string $subject,
        string $view,
        array $data = []
    ): string {
        try {
            Mail::send(
                $view,
                $data,
                function ($message) use ($to, $subject) {
                    $message->to($to)
                        ->from($this->fromAddress, $this->fromName)
                        ->subject($subject);
                }
            );

            return uniqid('email_', true);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                "Email sending failed: {$e->getMessage()}"
            );
        }
    }
}
