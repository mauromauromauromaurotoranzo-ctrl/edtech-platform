<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class KnowledgeBase
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    private string $id;
    private string $instructorId;
    private string $title;
    private string $description;
    private string $slug;
    private string $status;
    private array $settings;
    private ?DateTimeImmutable $lastIndexedAt;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $instructorId,
        string $title,
        string $description,
        string $slug,
        string $status = self::STATUS_DRAFT,
        array $settings = [],
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->instructorId = $instructorId;
        $this->title = $title;
        $this->description = $description;
        $this->slug = $slug;
        $this->status = $status;
        $this->settings = $settings;
        $this->lastIndexedAt = null;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getInstructorId(): string
    {
        return $this->instructorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function publish(): void
    {
        $this->status = self::STATUS_PUBLISHED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function updateSettings(array $settings): void
    {
        $this->settings = $settings;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getLastIndexedAt(): ?DateTimeImmutable
    {
        return $this->lastIndexedAt;
    }

    public function markAsIndexed(): void
    {
        $this->lastIndexedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
