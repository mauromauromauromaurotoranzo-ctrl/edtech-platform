<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class StudentChannelPreference
{
    /**
     * @param NotificationChannel[] $priorityOrder
     */
    public function __construct(
        private ?int $id,
        private int $studentId,
        private array $priorityOrder,
        private ?string $whatsappNumber,
        private ?string $telegramChatId,
        private ?string $emailAddress,
        private bool $notificationsEnabled,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        array $priorityOrder = [NotificationChannel::TELEGRAM, NotificationChannel::EMAIL, NotificationChannel::WHATSAPP],
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            studentId: $studentId,
            priorityOrder: $priorityOrder,
            whatsappNumber: null,
            telegramChatId: null,
            emailAddress: null,
            notificationsEnabled: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getPriorityOrder(): array
    {
        return $this->priorityOrder;
    }

    public function getPrimaryChannel(): ?NotificationChannel
    {
        return $this->priorityOrder[0] ?? null;
    }

    public function getWhatsappNumber(): ?string
    {
        return $this->whatsappNumber;
    }

    public function getTelegramChatId(): ?string
    {
        return $this->telegramChatId;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function isNotificationsEnabled(): bool
    {
        return $this->notificationsEnabled;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setWhatsappNumber(?string $number): void
    {
        $this->whatsappNumber = $number;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setTelegramChatId(?string $chatId): void
    {
        $this->telegramChatId = $chatId;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setEmailAddress(?string $email): void
    {
        $this->emailAddress = $email;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setPriorityOrder(array $order): void
    {
        $this->priorityOrder = $order;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setNotificationsEnabled(bool $enabled): void
    {
        $this->notificationsEnabled = $enabled;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getContactForChannel(NotificationChannel $channel): ?string
    {
        return match($channel) {
            NotificationChannel::WHATSAPP => $this->whatsappNumber,
            NotificationChannel::TELEGRAM => $this->telegramChatId,
            NotificationChannel::EMAIL => $this->emailAddress,
        };
    }

    public function hasContactForChannel(NotificationChannel $channel): bool
    {
        return $this->getContactForChannel($channel) !== null;
    }

    public function getAvailableChannels(): array
    {
        return array_filter(
            $this->priorityOrder,
            fn(NotificationChannel $channel) => $this->hasContactForChannel($channel)
        );
    }
}
