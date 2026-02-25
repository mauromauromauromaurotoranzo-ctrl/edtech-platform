<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Lesson
{
    public function __construct(
        private string $id,
        private string $title,
        private ?string $content,
        private string $type,
        private int $order,
        private ?int $durationMinutes,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'order' => $this->order,
            'duration_minutes' => $this->durationMinutes,
        ];
    }
}
