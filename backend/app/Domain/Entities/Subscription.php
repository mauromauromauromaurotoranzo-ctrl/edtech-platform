<?php

namespace App\Domain\Entities;

use DateTimeImmutable;
use App\Domain\ValueObjects\Money;

class Subscription
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PENDING = 'pending';

    private string $id;
    private string $studentId;
    private string $knowledgeBaseId;
    private string $status;
    private Money $amount;
    private string $interval;
    private DateTimeImmutable $currentPeriodStartsAt;
    private DateTimeImmutable $currentPeriodEndsAt;
    private ?string $paymentProviderSubscriptionId;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $cancelledAt;

    public function __construct(
        string $id,
        string $studentId,
        string $knowledgeBaseId,
        Money $amount,
        string $interval = 'month',
        ?DateTimeImmutable $currentPeriodStartsAt = null,
        ?DateTimeImmutable $currentPeriodEndsAt = null,
        ?string $paymentProviderSubscriptionId = null,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->studentId = $studentId;
        $this->knowledgeBaseId = $knowledgeBaseId;
        $this->status = self::STATUS_PENDING;
        $this->amount = $amount;
        $this->interval = $interval;
        $this->currentPeriodStartsAt = $currentPeriodStartsAt ?? new DateTimeImmutable();
        $this->currentPeriodEndsAt = $currentPeriodEndsAt ?? (new DateTimeImmutable())->modify('+1 month');
        $this->paymentProviderSubscriptionId = $paymentProviderSubscriptionId;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->cancelledAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getKnowledgeBaseId(): string
    {
        return $this->knowledgeBaseId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
    }

    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelledAt = new DateTimeImmutable();
    }

    public function expire(): void
    {
        $this->status = self::STATUS_EXPIRED;
    }

    public function renew(DateTimeImmutable $newPeriodEnd): void
    {
        $this->currentPeriodStartsAt = $this->currentPeriodEndsAt;
        $this->currentPeriodEndsAt = $newPeriodEnd;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function getCurrentPeriodEndsAt(): DateTimeImmutable
    {
        return $this->currentPeriodEndsAt;
    }

    public function isExpired(): bool
    {
        return $this->currentPeriodEndsAt < new DateTimeImmutable();
    }

    public function getPaymentProviderSubscriptionId(): ?string
    {
        return $this->paymentProviderSubscriptionId;
    }

    public function setPaymentProviderSubscriptionId(string $id): void
    {
        $this->paymentProviderSubscriptionId = $id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCancelledAt(): ?DateTimeImmutable
    {
        return $this->cancelledAt;
    }
}
