<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class Course
{
    private string $id;
    private string $knowledgeBaseId;
    private string $title;
    private string $description;
    private string $level;
    private array $modules;
    private array $settings;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $knowledgeBaseId,
        string $title,
        string $description,
        string $level = 'beginner',
        array $modules = [],
        array $settings = [],
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->knowledgeBaseId = $knowledgeBaseId;
        $this->title = $title;
        $this->description = $description;
        $this->level = $level;
        $this->modules = $modules;
        $this->settings = $settings;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKnowledgeBaseId(): string
    {
        return $this->knowledgeBaseId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function addModule(array $module): void
    {
        $this->modules[] = $module;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getSettings(): array
    {
        return $this->settings;
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
