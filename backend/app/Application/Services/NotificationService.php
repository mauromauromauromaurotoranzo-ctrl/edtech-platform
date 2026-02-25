<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Notification;
use App\Domain\Entities\NotificationChannel;
use App\Domain\Entities\StudentChannelPreference;
use App\Domain\RepositoryInterfaces\MessageSenderInterface;
use App\Domain\RepositoryInterfaces\NotificationRepositoryInterface;
use App\Domain\RepositoryInterfaces\StudentChannelPreferenceRepositoryInterface;
use Psr\Log\LoggerInterface;

class NotificationService
{
    /**
     * @var MessageSenderInterface[]
     */
    private array $senders = [];

    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private StudentChannelPreferenceRepositoryInterface $preferenceRepository,
        private LoggerInterface $logger,
    ) {}

    public function registerSender(MessageSenderInterface $sender): void
    {
        $this->senders[$sender->getChannelName()] = $sender;
    }

    public function send(
        int $studentId,
        string $subject,
        string $content,
        ?NotificationChannel $preferredChannel = null
    ): Notification {
        // Get student preferences
        $preference = $this->preferenceRepository->findByStudentId($studentId);
        
        if (!$preference || !$preference->isNotificationsEnabled()) {
            throw new \RuntimeException('Student has no channel preferences or notifications disabled');
        }

        // Determine which channel to use
        $channelsToTry = $this->determineChannels($preference, $preferredChannel);
        
        $lastError = null;
        
        foreach ($channelsToTry as $channel) {
            $notification = Notification::create(
                studentId: $studentId,
                subject: $subject,
                content: $content,
                channel: $channel,
            );

            $recipient = $preference->getContactForChannel($channel);
            
            if (!$recipient) {
                $this->logger->warning("No contact for channel {$channel->value}", [
                    'student_id' => $studentId,
                ]);
                continue;
            }

            try {
                $externalId = $this->sendViaChannel($notification, $recipient);
                $notification->markAsSent($externalId);
                $this->notificationRepository->save($notification);
                
                $this->logger->info("Notification sent successfully", [
                    'notification_id' => $notification->getId(),
                    'channel' => $channel->value,
                    'student_id' => $studentId,
                ]);
                
                return $notification;
                
            } catch (\Exception $e) {
                $lastError = $e;
                $notification->markAsFailed($e->getMessage());
                $this->notificationRepository->save($notification);
                
                $this->logger->error("Failed to send notification via {$channel->value}", [
                    'student_id' => $studentId,
                    'error' => $e->getMessage(),
                ]);
                
                // Continue to next channel (fallback)
                continue;
            }
        }

        throw new \RuntimeException(
            "Failed to send notification through all channels: " . ($lastError?->getMessage() ?? 'Unknown error')
        );
    }

    public function retryFailed(int $notificationId): Notification
    {
        $notification = $this->notificationRepository->findById($notificationId);
        
        if (!$notification) {
            throw new \InvalidArgumentException('Notification not found');
        }

        if (!$notification->canRetry(3)) {
            throw new \RuntimeException('Maximum retry attempts reached');
        }

        $preference = $this->preferenceRepository->findByStudentId($notification->getStudentId());
        
        if (!$preference) {
            throw new \RuntimeException('Student preferences not found');
        }

        $recipient = $preference->getContactForChannel($notification->getChannel());
        
        if (!$recipient) {
            throw new \RuntimeException('No contact for channel');
        }

        try {
            $externalId = $this->sendViaChannel($notification, $recipient);
            $notification->markAsSent($externalId);
            $notification->incrementRetry();
            $this->notificationRepository->save($notification);
            
            return $notification;
            
        } catch (\Exception $e) {
            $notification->markAsFailed($e->getMessage());
            $notification->incrementRetry();
            $this->notificationRepository->save($notification);
            
            throw $e;
        }
    }

    public function markAsDelivered(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        
        if ($notification) {
            $notification->markAsDelivered();
            $this->notificationRepository->save($notification);
        }
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = $this->notificationRepository->findById($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            $this->notificationRepository->save($notification);
        }
    }

    /**
     * @return NotificationChannel[]
     */
    private function determineChannels(
        StudentChannelPreference $preference,
        ?NotificationChannel $preferredChannel
    ): array {
        if ($preferredChannel && $preference->hasContactForChannel($preferredChannel)) {
            return [$preferredChannel];
        }

        return $preference->getAvailableChannels();
    }

    private function sendViaChannel(Notification $notification, string $recipient): string
    {
        $channelName = $notification->getChannel()->value;
        
        if (!isset($this->senders[$channelName])) {
            throw new \RuntimeException("No sender registered for channel: {$channelName}");
        }

        $sender = $this->senders[$channelName];
        
        if (!$sender->supports($notification)) {
            throw new \RuntimeException("Sender does not support this notification type");
        }

        return $sender->send($notification, $recipient);
    }
}
