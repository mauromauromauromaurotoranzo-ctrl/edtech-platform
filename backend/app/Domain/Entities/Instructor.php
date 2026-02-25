<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Instructor
{
    /**
     * @param string[] $expertiseAreas
     */
    public function __construct(
        private ?int $id,
        private int $userId,
        private array $expertiseAreas,
        private VerificationStatus $verificationStatus,
        private ?string $stripeAccountId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $userId,
        array $expertiseAreas = [],
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            userId: $userId,
            expertiseAreas: $expertiseAreas,
            verificationStatus: VerificationStatus::PENDING,
            stripeAccountId: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getExpertiseAreas(): array
    {
        return $this->expertiseAreas;
    }

    public function getVerificationStatus(): VerificationStatus
    {
        return $this->verificationStatus;
    }

    public function getStripeAccountId(): ?string
    {
        return $this->stripeAccountId;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function verify(): void
    {
        $this->verificationStatus = VerificationStatus::VERIFIED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function reject(): void
    {
        $this->verificationStatus = VerificationStatus::REJECTED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setStripeAccountId(string $stripeAccountId): void
    {
        $this->stripeAccountId = $stripeAccountId;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateExpertiseAreas(array $expertiseAreas): void
    {
        $this->expertiseAreas = $expertiseAreas;
        $this->updatedAt = new DateTimeImmutable();
    }
}
