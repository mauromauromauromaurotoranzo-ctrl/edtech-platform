<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\ChallengeType;
use DateTimeImmutable;

class DailyChallenge
{
    private array $metadata = [];
    private ?DateTimeImmutable $sentAt = null;
    private ?DateTimeImmutable $answeredAt = null;
    private ?string $studentAnswer = null;
    private ?bool $isCorrect = null;
    private int $pointsEarned = 0;

    private function __construct(
        private ?int $id,
        private int $studentId,
        private int $knowledgeBaseId,
        private ChallengeType $type,
        private string $title,
        private string $content,
        private ?string $correctAnswer,
        private array $options,
        private string $explanation,
        private int $points,
        private DateTimeImmutable $scheduledFor,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        int $knowledgeBaseId,
        ChallengeType $type,
        string $title,
        string $content,
        ?string $correctAnswer = null,
        array $options = [],
        string $explanation = '',
        int $points = 10,
        ?DateTimeImmutable $scheduledFor = null,
    ): self {
        $now = new DateTimeImmutable();
        
        return new self(
            id: null,
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            type: $type,
            title: $title,
            content: $content,
            correctAnswer: $correctAnswer,
            options: $options,
            explanation: $explanation,
            points: $points,
            scheduledFor: $scheduledFor ?? $now,
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
            type: ChallengeType::from($data['type']),
            title: $data['title'],
            content: $data['content'],
            correctAnswer: $data['correct_answer'] ?? null,
            options: json_decode($data['options'] ?? '[]', true),
            explanation: $data['explanation'] ?? '',
            points: $data['points'] ?? 10,
            scheduledFor: new DateTimeImmutable($data['scheduled_for']),
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
        );
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getStudentId(): int { return $this->studentId; }
    public function getKnowledgeBaseId(): int { return $this->knowledgeBaseId; }
    public function getType(): ChallengeType { return $this->type; }
    public function getTitle(): string { return $this->title; }
    public function getContent(): string { return $this->content; }
    public function getCorrectAnswer(): ?string { return $this->correctAnswer; }
    public function getOptions(): array { return $this->options; }
    public function getExplanation(): string { return $this->explanation; }
    public function getPoints(): int { return $this->points; }
    public function getScheduledFor(): DateTimeImmutable { return $this->scheduledFor; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }
    public function getMetadata(): array { return $this->metadata; }
    public function getSentAt(): ?DateTimeImmutable { return $this->sentAt; }
    public function getAnsweredAt(): ?DateTimeImmutable { return $this->answeredAt; }
    public function getStudentAnswer(): ?string { return $this->studentAnswer; }
    public function isCorrect(): ?bool { return $this->isCorrect; }
    public function getPointsEarned(): int { return $this->pointsEarned; }

    // Business methods
    public function markAsSent(): void
    {
        $this->sentAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function submitAnswer(string $answer): void
    {
        $this->studentAnswer = $answer;
        $this->answeredAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        
        // Evaluate answer
        if ($this->correctAnswer !== null) {
            $this->isCorrect = $this->evaluateAnswer($answer);
            $this->pointsEarned = $this->isCorrect ? $this->points : 0;
        }
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function isPending(): bool
    {
        return $this->sentAt === null;
    }

    public function isAnswered(): bool
    {
        return $this->answeredAt !== null;
    }

    public function canBeSent(): bool
    {
        $now = new DateTimeImmutable();
        return $this->sentAt === null && $this->scheduledFor <= $now;
    }

    private function evaluateAnswer(string $answer): bool
    {
        // Normalize and compare
        $normalizedAnswer = strtolower(trim($answer));
        $normalizedCorrect = strtolower(trim($this->correctAnswer ?? ''));
        
        return $normalizedAnswer === $normalizedCorrect;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->studentId,
            'knowledge_base_id' => $this->knowledgeBaseId,
            'type' => $this->type->value,
            'title' => $this->title,
            'content' => $this->content,
            'correct_answer' => $this->correctAnswer,
            'options' => json_encode($this->options),
            'explanation' => $this->explanation,
            'points' => $this->points,
            'scheduled_for' => $this->scheduledFor->format('Y-m-d H:i:s'),
            'sent_at' => $this->sentAt?->format('Y-m-d H:i:s'),
            'answered_at' => $this->answeredAt?->format('Y-m-d H:i:s'),
            'student_answer' => $this->studentAnswer,
            'is_correct' => $this->isCorrect,
            'points_earned' => $this->pointsEarned,
            'metadata' => json_encode($this->metadata),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
