<?php

namespace App\Domain\Entities;

use DateTimeImmutable;

class Conversation
{
    private string $id;
    private string $studentId;
    private string $knowledgeBaseId;
    private array $messages;
    private array $contextRetrieval;
    private ?float $engagementScore;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $lastMessageAt;

    public function __construct(
        string $id,
        string $studentId,
        string $knowledgeBaseId,
        ?DateTimeImmutable $createdAt = null
    ) {
        $this->id = $id;
        $this->studentId = $studentId;
        $this->knowledgeBaseId = $knowledgeBaseId;
        $this->messages = [];
        $this->contextRetrieval = [];
        $this->engagementScore = null;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->lastMessageAt = null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getKnowledgeBaseId(): string
    {
        return $this->knowledgeBaseId;
    }

    public function addMessage(string $role, string $content, ?int $tokensUsed = null): void
    {
        $this->messages[] = [
            'role' => $role,
            'content' => $content,
            'tokens_used' => $tokensUsed,
            'timestamp' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
        $this->lastMessageAt = new DateTimeImmutable();
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setContextRetrieval(array $chunksReferenced): void
    {
        $this->contextRetrieval = $chunksReferenced;
    }

    public function getContextRetrieval(): array
    {
        return $this->contextRetrieval;
    }

    public function setEngagementScore(float $score): void
    {
        $this->engagementScore = $score;
    }

    public function getEngagementScore(): ?float
    {
        return $this->engagementScore;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastMessageAt(): ?DateTimeImmutable
    {
        return $this->lastMessageAt;
    }

    public function getLastMessages(int $count = 10): array
    {
        return array_slice($this->messages, -$count);
    }
}
