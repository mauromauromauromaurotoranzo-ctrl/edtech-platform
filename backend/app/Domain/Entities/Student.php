<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\LearningPreferences;
use DateTimeImmutable;

class Student
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private LearningPreferences $learningPreferences,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $userId,
        ?LearningPreferences $learningPreferences = null,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            userId: $userId,
            learningPreferences: $learningPreferences ?? new LearningPreferences(),
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

    public function getLearningPreferences(): LearningPreferences
    {
        return $this->learningPreferences;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateLearningPreferences(LearningPreferences $preferences): void
    {
        $this->learningPreferences = $preferences;
        $this->updatedAt = new DateTimeImmutable();
    }
}
