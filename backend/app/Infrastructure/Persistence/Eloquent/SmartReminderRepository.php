<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\SmartReminder;
use App\Domain\RepositoryInterfaces\SmartReminderRepositoryInterface;
use DateTimeImmutable;

class SmartReminderRepository implements SmartReminderRepositoryInterface
{
    public function __construct(
        private SmartReminderModel $model
    ) {}

    public function findById(int $id): ?SmartReminder
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findAll(): array
    {
        return $this->model->all()->map(fn($r) => $this->toEntity($r))->toArray();
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findActiveByStudentId(int $studentId): array
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findDueReminders(DateTimeImmutable $before): array
    {
        return $this->model
            ->where('is_active', true)
            ->where('scheduled_at', '<=', $before)
            ->orderBy('priority', 'desc')
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByType(string $type): array
    {
        return $this->model->where('type', $type)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByKnowledgeBaseId(int $knowledgeBaseId): array
    {
        return $this->model->where('knowledge_base_id', $knowledgeBaseId)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(SmartReminder $reminder): void
    {
        $data = [
            'student_id' => $reminder->getStudentId(),
            'knowledge_base_id' => $reminder->getKnowledgeBaseId(),
            'type' => $reminder->getType()->value,
            'title' => $reminder->getTitle(),
            'message' => $reminder->getMessage(),
            'scheduled_at' => $reminder->getScheduledAt(),
            'is_recurring' => $reminder->isRecurring(),
            'recurrence_pattern' => $reminder->getRecurrencePattern(),
            'priority' => $reminder->getPriority(),
            'is_active' => $reminder->isActive(),
            'sent_at' => $reminder->getSentAt(),
            'send_count' => $reminder->getSendCount(),
            'metadata' => json_encode([]),
        ];

        if ($reminder->getId()) {
            $this->model->where('id', $reminder->getId())->update($data);
        } else {
            $this->model->create($data);
        }
    }

    public function delete(int $id): void
    {
        $this->model->destroy($id);
    }

    public function deleteByStudentId(int $studentId): void
    {
        $this->model->where('student_id', $studentId)->delete();
    }

    private function toEntity(SmartReminderModel $model): SmartReminder
    {
        return SmartReminder::fromDatabase([
            'id' => $model->id,
            'student_id' => $model->student_id,
            'knowledge_base_id' => $model->knowledge_base_id,
            'type' => $model->type,
            'title' => $model->title,
            'message' => $model->message,
            'scheduled_at' => $model->scheduled_at->toDateTimeString(),
            'is_recurring' => $model->is_recurring,
            'recurrence_pattern' => $model->recurrence_pattern,
            'priority' => $model->priority,
            'is_active' => $model->is_active,
            'sent_at' => $model->sent_at?->toDateTimeString(),
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ]);
    }
}
