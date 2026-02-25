<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\DailyChallenge;
use App\Domain\RepositoryInterfaces\DailyChallengeRepositoryInterface;
use DateTimeImmutable;

class DailyChallengeRepository implements DailyChallengeRepositoryInterface
{
    public function __construct(
        private DailyChallengeModel $model
    ) {}

    public function findById(int $id): ?DailyChallenge
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
            ->orderBy('scheduled_for', 'desc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByStudentIdAndDate(int $studentId, DateTimeImmutable $date): ?DailyChallenge
    {
        $startOfDay = $date->setTime(0, 0, 0);
        $endOfDay = $date->setTime(23, 59, 59);

        $record = $this->model
            ->where('student_id', $studentId)
            ->whereBetween('scheduled_for', [$startOfDay, $endOfDay])
            ->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findPendingByStudentId(int $studentId): array
    {
        return $this->model
            ->where('student_id', $studentId)
            ->whereNull('answered_at')
            ->orderBy('scheduled_for', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findPendingForToday(): array
    {
        $now = new DateTimeImmutable();
        
        return $this->model
            ->whereNull('sent_at')
            ->where('scheduled_for', '<=', $now)
            ->orderBy('scheduled_for', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByKnowledgeBaseId(int $knowledgeBaseId): array
    {
        return $this->model
            ->where('knowledge_base_id', $knowledgeBaseId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findAnsweredByStudentId(int $studentId, int $limit = 10): array
    {
        return $this->model
            ->where('student_id', $studentId)
            ->whereNotNull('answered_at')
            ->orderBy('answered_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(DailyChallenge $challenge): void
    {
        $data = [
            'student_id' => $challenge->getStudentId(),
            'knowledge_base_id' => $challenge->getKnowledgeBaseId(),
            'type' => $challenge->getType()->value,
            'title' => $challenge->getTitle(),
            'content' => $challenge->getContent(),
            'correct_answer' => $challenge->getCorrectAnswer(),
            'options' => json_encode($challenge->getOptions()),
            'explanation' => $challenge->getExplanation(),
            'points' => $challenge->getPoints(),
            'scheduled_for' => $challenge->getScheduledFor(),
            'sent_at' => $challenge->getSentAt(),
            'answered_at' => $challenge->getAnsweredAt(),
            'student_answer' => $challenge->getStudentAnswer(),
            'is_correct' => $challenge->isCorrect(),
            'points_earned' => $challenge->getPointsEarned(),
            'metadata' => json_encode($challenge->getMetadata()),
        ];

        if ($challenge->getId()) {
            $this->model->where('id', $challenge->getId())->update($data);
        } else {
            $record = $this->model->create($data);
            // Note: In real implementation, we'd need to update the entity with the new ID
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

    public function countByStudentId(int $studentId): int
    {
        return $this->model->where('student_id', $studentId)->count();
    }

    public function countCorrectByStudentId(int $studentId): int
    {
        return $this->model
            ->where('student_id', $studentId)
            ->where('is_correct', true)
            ->count();
    }

    private function toEntity(DailyChallengeModel $model): DailyChallenge
    {
        return DailyChallenge::fromDatabase([
            'id' => $model->id,
            'student_id' => $model->student_id,
            'knowledge_base_id' => $model->knowledge_base_id,
            'type' => $model->type,
            'title' => $model->title,
            'content' => $model->content,
            'correct_answer' => $model->correct_answer,
            'options' => $model->options,
            'explanation' => $model->explanation,
            'points' => $model->points,
            'scheduled_for' => $model->scheduled_for?->toDateTimeString(),
            'sent_at' => $model->sent_at?->toDateTimeString(),
            'answered_at' => $model->answered_at?->toDateTimeString(),
            'student_answer' => $model->student_answer,
            'is_correct' => $model->is_correct,
            'points_earned' => $model->points_earned,
            'metadata' => $model->metadata,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ]);
    }
}
