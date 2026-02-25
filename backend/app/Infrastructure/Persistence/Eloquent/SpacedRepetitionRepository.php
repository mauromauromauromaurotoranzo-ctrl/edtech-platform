<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Entities\SpacedRepetitionItem;
use App\Domain\RepositoryInterfaces\SpacedRepetitionRepositoryInterface;
use DateTimeImmutable;

class SpacedRepetitionRepository implements SpacedRepetitionRepositoryInterface
{
    public function __construct(
        private SpacedRepetitionModel $model
    ) {}

    public function findById(int $id): ?SpacedRepetitionItem
    {
        $record = $this->model->find($id);
        return $record ? $this->toEntity($record) : null;
    }

    public function findByStudentId(int $studentId): array
    {
        return $this->model->where('student_id', $studentId)
            ->orderBy('next_review_at', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findByStudentAndChunk(int $studentId, int $contentChunkId): ?SpacedRepetitionItem
    {
        $record = $this->model
            ->where('student_id', $studentId)
            ->where('content_chunk_id', $contentChunkId)
            ->first();

        return $record ? $this->toEntity($record) : null;
    }

    public function findDueItems(int $studentId): array
    {
        $now = new DateTimeImmutable();
        
        return $this->model
            ->where('student_id', $studentId)
            ->where(function ($query) use ($now) {
                $query->whereNull('next_review_at')
                      ->orWhere('next_review_at', '<=', $now);
            })
            ->orderBy('next_review_at', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function findAllDue(): array
    {
        $now = new DateTimeImmutable();
        
        return $this->model
            ->where(function ($query) use ($now) {
                $query->whereNull('next_review_at')
                      ->orWhere('next_review_at', '<=', $now);
            })
            ->orderBy('next_review_at', 'asc')
            ->get()
            ->map(fn($r) => $this->toEntity($r))
            ->toArray();
    }

    public function save(SpacedRepetitionItem $item): void
    {
        $data = [
            'student_id' => $item->getStudentId(),
            'content_chunk_id' => $item->getContentChunkId(),
            'easiness_factor' => $item->getEasinessFactor(),
            'repetition_count' => $item->getRepetitionCount(),
            'interval_days' => $item->getIntervalDays(),
            'next_review_at' => $item->getNextReviewAt(),
            'last_reviewed_at' => $item->getLastReviewedAt(),
            'review_history' => json_encode($item->getReviewHistory()),
        ];

        if ($item->getId()) {
            $this->model->where('id', $item->getId())->update($data);
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

    public function getStats(int $studentId): array
    {
        $items = $this->model->where('student_id', $studentId)->get();
        
        $total = $items->count();
        $due = $items->filter(fn($i) => 
            !$i->next_review_at || new DateTimeImmutable($i->next_review_at) <= new DateTimeImmutable()
        )->count();
        
        $avgEasiness = $items->avg('easiness_factor') ?? 2.5;
        
        return [
            'total_items' => $total,
            'due_for_review' => $due,
            'average_easiness' => round($avgEasiness, 2),
            'total_reviews' => $items->sum('repetition_count'),
        ];
    }

    private function toEntity(SpacedRepetitionModel $model): SpacedRepetitionItem
    {
        return SpacedRepetitionItem::fromDatabase([
            'id' => $model->id,
            'student_id' => $model->student_id,
            'content_chunk_id' => $model->content_chunk_id,
            'easiness_factor' => $model->easiness_factor,
            'repetition_count' => $model->repetition_count,
            'interval_days' => $model->interval_days,
            'next_review_at' => $model->next_review_at?->toDateTimeString(),
            'last_reviewed_at' => $model->last_reviewed_at?->toDateTimeString(),
            'review_history' => $model->review_history,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ]);
    }
}
