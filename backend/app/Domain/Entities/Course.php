<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Course
{
    /**
     * @param array<int, Module> $modules
     */
    public function __construct(
        private ?int $id,
        private int $knowledgeBaseId,
        private string $title,
        private ?string $description,
        private CourseLevel $level,
        private bool $selfPaced,
        private ?DateTimeImmutable $startDate,
        private ?DateTimeImmutable $endDate,
        private array $modules,
        private CourseStatus $status,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $knowledgeBaseId,
        string $title,
        ?string $description,
        CourseLevel $level,
        bool $selfPaced = true,
        ?DateTimeImmutable $startDate = null,
        ?DateTimeImmutable $endDate = null,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            knowledgeBaseId: $knowledgeBaseId,
            title: $title,
            description: $description,
            level: $level,
            selfPaced: $selfPaced,
            startDate: $startDate,
            endDate: $endDate,
            modules: [],
            status: CourseStatus::DRAFT,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKnowledgeBaseId(): int
    {
        return $this->knowledgeBaseId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getLevel(): CourseLevel
    {
        return $this->level;
    }

    public function isSelfPaced(): bool
    {
        return $this->selfPaced;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getStatus(): CourseStatus
    {
        return $this->status;
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
        $this->status = CourseStatus::PUBLISHED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->status = CourseStatus::ARCHIVED;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateMetadata(string $title, ?string $description, CourseLevel $level): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->level = $level;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setSchedule(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate): void
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->selfPaced = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function addModule(Module $module): void
    {
        $this->modules[] = $module;
        $this->updatedAt = new DateTimeImmutable();
    }
}
