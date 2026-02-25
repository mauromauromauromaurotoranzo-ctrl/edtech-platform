<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Notification
{
    public function __construct(
        private ?int $id,
        private int $studentId,
        private string $subject,
        private string $content,
        private NotificationChannel $channel,
        private NotificationStatus $status,
        private ?string $externalId,
        private ?DateTimeImmutable $sentAt,
        private ?DateTimeImmutable $deliveredAt,
        private ?DateTimeImmutable $readAt,
        private ?string $errorMessage,
        private int $retryCount,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        string $subject,
        string $content,
        NotificationChannel $channel,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            studentId: $studentId,
            subject: $subject,
            content: $content,
            channel: $channel,
            status: NotificationStatus::PENDING,
            externalId: null,
            sentAt: null,
            deliveredAt: null,
            readAt: null,
            errorMessage: null,
            retryCount: 0,
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

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getChannel(): NotificationChannel
    {
        return $this->channel;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getDeliveredAt(): ?DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function markAsSent(string $externalId): void
    {
        $this->status = NotificationStatus::SENT;
        $this->externalId = $externalId;
        $this->sentAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsDelivered(): void
    {
        $this->status = NotificationStatus::DELIVERED;
        $this->deliveredAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsRead(): void
    {
        $this->status = NotificationStatus::READ;
        $this->readAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->status = NotificationStatus::FAILED;
        $this->errorMessage = $errorMessage;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function incrementRetry(): void
    {
        $this->retryCount++;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function canRetry(int $maxRetries): bool
    {
        return $this->retryCount < $maxRetries;
    }
}
