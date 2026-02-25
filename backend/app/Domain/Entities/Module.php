<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Module
{
    /**
     * @param array<int, Lesson> $lessons
     */
    public function __construct(
        private string $id,
        private string $title,
        private ?string $description,
        private int $order,
        private array $lessons = [],
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getLessons(): array
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): void
    {
        $this->lessons[] = $lesson;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'lessons' => array_map(fn(Lesson $l) => $l->toArray(), $this->lessons),
        ];
    }
}
