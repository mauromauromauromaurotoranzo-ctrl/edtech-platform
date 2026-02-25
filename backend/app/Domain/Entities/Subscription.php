<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Subscription
{
    public function __construct(
        private ?int $id,
        private int $studentId,
        private ?int $knowledgeBaseId,
        private ?int $courseId,
        private SubscriptionStatus $status,
        private DateTimeImmutable $currentPeriodStartsAt,
        private DateTimeImmutable $currentPeriodEndsAt,
        private ?array $paymentProviderData,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
        if ($knowledgeBaseId === null && $courseId === null) {
            throw new \InvalidArgumentException('Subscription must have either knowledgeBaseId or courseId');
        }
    }

    public static function create(
        int $studentId,
        ?int $knowledgeBaseId,
        ?int $courseId,
        DateTimeImmutable $currentPeriodEndsAt,
        ?array $paymentProviderData = null,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            courseId: $courseId,
            status: SubscriptionStatus::ACTIVE,
            currentPeriodStartsAt: $now,
            currentPeriodEndsAt: $currentPeriodEndsAt,
            paymentProviderData: $paymentProviderData,
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

    public function getKnowledgeBaseId(): ?int
    {
        return $this->knowledgeBaseId;
    }

    public function getCourseId(): ?int
    {
        return $this->courseId;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function getCurrentPeriodStartsAt(): DateTimeImmutable
    {
        return $this->currentPeriodStartsAt;
    }

    public function getCurrentPeriodEndsAt(): DateTimeImmutable
    {
        return $this->currentPeriodEndsAt;
    }

    public function getPaymentProviderData(): ?array
    {
        return $this->paymentProviderData;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function cancel(): void
    {
        $this->status = SubscriptionStatus::CANCELLED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function expire(): void
    {
        $this->status = SubscriptionStatus::EXPIRED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function renew(DateTimeImmutable $newPeriodEndsAt): void
    {
        $this->currentPeriodStartsAt = new DateTimeImmutable();
        $this->currentPeriodEndsAt = $newPeriodEndsAt;
        $this->status = SubscriptionStatus::ACTIVE;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::ACTIVE 
            && $this->currentPeriodEndsAt > new DateTimeImmutable();
    }
}
