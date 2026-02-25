<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class SpacedRepetitionItem
{
    // SM-2 Algorithm parameters
    private float $easinessFactor = 2.5;
    private int $repetitionCount = 0;
    private int $intervalDays = 0;
    private ?DateTimeImmutable $nextReviewAt = null;
    private ?DateTimeImmutable $lastReviewedAt = null;
    private array $reviewHistory = [];

    private function __construct(
        private ?int $id,
        private int $studentId,
        private int $contentChunkId,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        int $contentChunkId,
    ): self {
        $now = new DateTimeImmutable();
        
        return new self(
            id: null,
            studentId: $studentId,
            contentChunkId: $contentChunkId,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function fromDatabase(array $data): self
    {
        $item = new self(
            id: $data['id'],
            studentId: $data['student_id'],
            contentChunkId: $data['content_chunk_id'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );

        $item->easinessFactor = $data['easiness_factor'] ?? 2.5;
        $item->repetitionCount = $data['repetition_count'] ?? 0;
        $item->intervalDays = $data['interval_days'] ?? 0;
        $item->nextReviewAt = $data['next_review_at'] 
            ? new DateTimeImmutable($data['next_review_at']) 
            : null;
        $item->lastReviewedAt = $data['last_reviewed_at'] 
            ? new DateTimeImmutable($data['last_reviewed_at']) 
            : null;
        $item->reviewHistory = json_decode($data['review_history'] ?? '[]', true);

        return $item;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getStudentId(): int { return $this->studentId; }
    public function getContentChunkId(): int { return $this->contentChunkId; }
    public function getEasinessFactor(): float { return $this->easinessFactor; }
    public function getRepetitionCount(): int { return $this->repetitionCount; }
    public function getIntervalDays(): int { return $this->intervalDays; }
    public function getNextReviewAt(): ?DateTimeImmutable { return $this->nextReviewAt; }
    public function getLastReviewedAt(): ?DateTimeImmutable { return $this->lastReviewedAt; }
    public function getReviewHistory(): array { return $this->reviewHistory; }

    /**
     * Review the item with a quality rating (0-5)
     * 0 = complete blackout
     * 1 = incorrect response, correct one remembered
     * 2 = incorrect response, easy to recall correct
     * 3 = correct with serious difficulty
     * 4 = correct with hesitation
     * 5 = perfect response
     */
    public function review(int $quality): void
    {
        if ($quality < 0 || $quality > 5) {
            throw new \InvalidArgumentException('Quality must be between 0 and 5');
        }

        $now = new DateTimeImmutable();

        // Record review
        $this->reviewHistory[] = [
            'date' => $now->format('Y-m-d H:i:s'),
            'quality' => $quality,
        ];

        // SM-2 Algorithm
        if ($quality >= 3) {
            if ($this->repetitionCount === 0) {
                $this->intervalDays = 1;
            } elseif ($this->repetitionCount === 1) {
                $this->intervalDays = 6;
            } else {
                $this->intervalDays = (int) round($this->intervalDays * $this->easinessFactor);
            }
            $this->repetitionCount++;
        } else {
            $this->repetitionCount = 0;
            $this->intervalDays = 1;
        }

        // Update easiness factor
        $this->easinessFactor = $this->easinessFactor 
            + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        
        if ($this->easinessFactor < 1.3) {
            $this->easinessFactor = 1.3;
        }

        $this->lastReviewedAt = $now;
        $this->nextReviewAt = $now->modify("+{$this->intervalDays} days");
        $this->updatedAt = $now;
    }

    public function isDue(): bool
    {
        if (!$this->nextReviewAt) {
            return true;
        }

        $now = new DateTimeImmutable();
        return $this->nextReviewAt <= $now;
    }

    public function getDaysUntilReview(): int
    {
        if (!$this->nextReviewAt) {
            return 0;
        }

        $now = new DateTimeImmutable();
        $diff = $now->diff($this->nextReviewAt);
        
        return $diff->invert ? -$diff->days : $diff->days;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'content_chunk_id' => $this->contentChunkId,
            'easiness_factor' => $this->easinessFactor,
            'repetition_count' => $this->repetitionCount,
            'interval_days' => $this->intervalDays,
            'next_review_at' => $this->nextReviewAt?->format('Y-m-d H:i:s'),
            'last_reviewed_at' => $this->lastReviewedAt?->format('Y-m-d H:i:s'),
            'review_history' => json_encode($this->reviewHistory),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
