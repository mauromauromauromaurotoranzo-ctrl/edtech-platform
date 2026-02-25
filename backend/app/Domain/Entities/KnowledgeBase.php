<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class KnowledgeBase
{
    public function __construct(
        private ?int $id,
        private int $instructorId,
        private string $title,
        private ?string $description,
        private string $slug,
        private KnowledgeBaseStatus $status,
        private bool $publicAccess,
        private ?string $pricingModel,
        private int $totalChunks,
        private ?DateTimeImmutable $lastIndexedAt,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $instructorId,
        string $title,
        ?string $description,
        string $slug,
        bool $publicAccess = false,
        ?string $pricingModel = null,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            instructorId: $instructorId,
            title: $title,
            description: $description,
            slug: $slug,
            status: KnowledgeBaseStatus::DRAFT,
            publicAccess: $publicAccess,
            pricingModel: $pricingModel,
            totalChunks: 0,
            lastIndexedAt: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstructorId(): int
    {
        return $this->instructorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStatus(): KnowledgeBaseStatus
    {
        return $this->status;
    }

    public function isPublicAccess(): bool
    {
        return $this->publicAccess;
    }

    public function getPricingModel(): ?string
    {
        return $this->pricingModel;
    }

    public function getTotalChunks(): int
    {
        return $this->totalChunks;
    }

    public function getLastIndexedAt(): ?DateTimeImmutable
    {
        return $this->lastIndexedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function publish(): void
    {
        $this->status = KnowledgeBaseStatus::PUBLISHED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->status = KnowledgeBaseStatus::ARCHIVED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateMetadata(string $title, ?string $description, string $slug): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateSettings(bool $publicAccess, ?string $pricingModel): void
    {
        $this->publicAccess = $publicAccess;
        $this->pricingModel = $pricingModel;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function markAsIndexed(int $totalChunks): void
    {
        $this->totalChunks = $totalChunks;
        $this->lastIndexedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }
}
