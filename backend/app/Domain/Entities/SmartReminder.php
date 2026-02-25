<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\ReminderType;
use DateTimeImmutable;

class SmartReminder
{
    private ?DateTimeImmutable $sentAt = null;
    private int $sendCount = 0;
    private array $metadata = [];

    private function __construct(
        private ?int $id,
        private int $studentId,
        private int $knowledgeBaseId,
        private ReminderType $type,
        private string $title,
        private string $message,
        private DateTimeImmutable $scheduledAt,
        private bool $isRecurring,
        private ?string $recurrencePattern,
        private float $priority,
        private bool $isActive,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        int $knowledgeBaseId,
        ReminderType $type,
        string $title,
        string $message,
        DateTimeImmutable $scheduledAt,
        bool $isRecurring = false,
        ?string $recurrencePattern = null,
        float $priority = 1.0,
    ): self {
        $now = new DateTimeImmutable();
        
        return new self(
            id: null,
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            type: $type,
            title: $title,
            message: $message,
            scheduledAt: $scheduledAt,
            isRecurring: $isRecurring,
            recurrencePattern: $recurrencePattern,
            priority: $priority,
            isActive: true,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            id: $data['id'],
            studentId: $data['student_id'],
            knowledgeBaseId: $data['knowledge_base_id'],
            type: ReminderType::from($data['type']),
            title: $data['title'],
            message: $data['message'],
            scheduledAt: new DateTimeImmutable($data['scheduled_at']),
            isRecurring: $data['is_recurring'],
            recurrencePattern: $data['recurrence_pattern'] ?? null,
            priority: $data['priority'],
            isActive: $data['is_active'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getStudentId(): int { return $this->studentId; }
    public function getKnowledgeBaseId(): int { return $this->knowledgeBaseId; }
    public function getType(): ReminderType { return $this->type; }
    public function getTitle(): string { return $this->title; }
    public function getMessage(): string { return $this->message; }
    public function getScheduledAt(): DateTimeImmutable { return $this->scheduledAt; }
    public function isRecurring(): bool { return $this->isRecurring; }
    public function getRecurrencePattern(): ?string { return $this->recurrencePattern; }
    public function getPriority(): float { return $this->priority; }
    public function isActive(): bool { return $this->isActive; }
    public function getSentAt(): ?DateTimeImmutable { return $this->sentAt; }
    public function getSendCount(): int { return $this->sendCount; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    // Business methods
    public function markAsSent(): void
    {
        $this->sentAt = new DateTimeImmutable();
        $this->sendCount++;
        $this->updatedAt = new DateTimeImmutable();

        if ($this->isRecurring && $this->recurrencePattern) {
            $this->reschedule();
        } else {
            $this->isActive = false;
        }
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updateSchedule(DateTimeImmutable $newSchedule): void
    {
        $this->scheduledAt = $newSchedule;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function shouldSend(): bool
    {
        if (!$this->isActive) {
            return false;
        }

        $now = new DateTimeImmutable();
        return $this->scheduledAt <= $now;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
        $this->updatedAt = new DateTimeImmutable();
    }

    private function reschedule(): void
    {
        // Simple recurrence patterns
        $pattern = $this->recurrencePattern;
        
        match($pattern) {
            'daily' => $this->scheduledAt = $this->scheduledAt->modify('+1 day'),
            'weekly' => $this->scheduledAt = $this->scheduledAt->modify('+1 week'),
            'monthly' => $this->scheduledAt = $this->scheduledAt->modify('+1 month'),
            default => $this->isActive = false,
        };
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'knowledge_base_id' => $this->knowledgeBaseId,
            'type' => $this->type->value,
            'title' => $this->title,
            'message' => $this->message,
            'scheduled_at' => $this->scheduledAt->format('Y-m-d H:i:s'),
            'is_recurring' => $this->isRecurring,
            'recurrence_pattern' => $this->recurrencePattern,
            'priority' => $this->priority,
            'is_active' => $this->isActive,
            'sent_at' => $this->sentAt?->format('Y-m-d H:i:s'),
            'send_count' => $this->sendCount,
            'metadata' => json_encode($this->metadata),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
