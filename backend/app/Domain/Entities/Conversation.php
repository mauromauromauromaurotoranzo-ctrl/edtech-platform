<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Conversation
{
    /**
     * @param array<int, Message> $messages
     * @param string[] $chunksReferenced
     */
    public function __construct(
        private ?int $id,
        private int $studentId,
        private int $knowledgeBaseId,
        private array $messages,
        private array $chunksReferenced,
        private ?float $engagementScore,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $studentId,
        int $knowledgeBaseId,
    ): self {
        $now = new DateTimeImmutable();
        return new self(
            id: null,
            studentId: $studentId,
            knowledgeBaseId: $knowledgeBaseId,
            messages: [],
            chunksReferenced: [],
            engagementScore: null,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentId(): int
    {
        return $this->studentId;
    }

    public function getKnowledgeBaseId(): int
    {
        return $this->knowledgeBaseId;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getChunksReferenced(): array
    {
        return $this->chunksReferenced;
    }

    public function getEngagementScore(): ?float
    {
        return $this->engagementScore;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function addMessage(Message $message): void
    {
        $this->messages[] = $message;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function addChunkReference(string $chunkId): void
    {
        if (!in_array($chunkId, $this->chunksReferenced, true)) {
            $this->chunksReferenced[] = $chunkId;
        }
    }

    public function setEngagementScore(float $score): void
    {
        $this->engagementScore = $score;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getLastMessage(): ?Message
    {
        if (empty($this->messages)) {
            return null;
        }
        return $this->messages[count($this->messages) - 1];
    }
}
